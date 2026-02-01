<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Models\Brands;
use App\Models\UserBrandSaleSystem;
use App\Models\UserSaleSystem;
use App\Orchid\Layouts\Role\RolePermissionLayout;
use App\Orchid\Layouts\User\UserBrandDiscountsLayout;
use App\Orchid\Layouts\User\UserCategoryDiscountsLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserPasswordLayout;
use App\Orchid\Layouts\User\UserRoleLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
use App\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserEditScreen extends Screen
{
    /**
     * @var User
     */
    public $user;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(User $user): iterable
    {
        $user->load(['roles']);

        $user->active = ($user->active === 'on');

        $img = trim((string) ($user->avatar ?? ''));
        $imgUrl = '';

        if ($img !== '') {
            $imgUrl = preg_match('~^https?://~i', $img) === 1
                ? $img
                : asset('storage/' . ltrim($img, '/'));
        }

        $discounts = [
            'categoriesTree' => [],
            'brands'         => [],
        ];

        if ($user->exists) {
            // ---------- категории (дерево) ----------
            $tree = categoryTreeSort(); // ожидаем: [['id'=>..,'title'=>..,'children'=>[...] ]]

            $checkedCategories = UserSaleSystem::checkedCategories($user->id)->toArray();

            $categorySales = UserSaleSystem::query()
                ->where('user_id', $user->id)
                ->get()
                ->keyBy('category_id');

            $categoriesTree = [];
            foreach ($tree as $parent) {
                $parentId = (int) $parent['id'];

                $parentSaleRow = $categorySales->get($parentId);
                $parentEnabled = in_array($parentId, $checkedCategories, true);

                $childrenOut = [];
                foreach (($parent['children'] ?? []) as $child) {
                    $childId = (int) $child['id'];
                    $childSaleRow = $categorySales->get($childId);

                    $childrenOut[] = [
                        'id'      => $childId,
                        'title'   => (string) ($child['title'] ?? ('Категория #' . $childId)),
                        'enabled' => in_array($childId, $checkedCategories, true),
                        'sale'    => $childSaleRow?->sale,
                        'parent'  => $parentId,
                    ];
                }

                $categoriesTree[] = [
                    'id'       => $parentId,
                    'title'    => (string) ($parent['title'] ?? ('Категория #' . $parentId)),
                    'enabled'  => $parentEnabled,
                    'sale'     => $parentSaleRow?->sale,
                    'children' => $childrenOut,
                ];
            }

            $discounts['categoriesTree'] = $categoriesTree;

            // ---------- бренды ----------
            $brands = Brands::query()->select(['id', 'title'])->orderBy('title')->get();

            $checkedBrands = UserBrandSaleSystem::checkedBrands($user->id)->toArray();

            $brandSales = UserBrandSaleSystem::query()
                ->where('user_id', $user->id)
                ->get()
                ->keyBy('brand_id');

            foreach ($brands as $brand) {
                $brandId = (int) $brand->id;
                $saleRow = $brandSales->get($brandId);

                $discounts['brands'][] = [
                    'id'      => $brandId,
                    'title'   => (string) $brand->title,
                    'enabled' => in_array($brandId, $checkedBrands, true),
                    'sale'    => $saleRow?->sale,
                ];
            }
        }

        //dd($discounts);

        return [
            'user'       => $user,
            'user.img_url' => $imgUrl,
            'permission' => $user->statusOfPermissions(),
            'discounts'     => $discounts,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return $this->user->exists ? 'Редактирование пользователя' : 'Создание пользователя';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Профиль, доступ и права пользователя.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Impersonate user'))
                ->icon('bg.box-arrow-in-right')
                ->confirm(__('You can revert to your original state by logging out.'))
                ->method('loginAs')
                ->canSee($this->user->exists && $this->user->id !== \request()->user()->id),

            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                ->method('remove')
                ->canSee($this->user->exists),

            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make('Сохранить скидки (категории)')
                ->method('saveCategoryDiscounts')
                ->canSee($this->user->exists),


            Button::make('Сохранить скидки (бренды)')
                ->method('saveBrandDiscounts')
                ->canSee($this->user->exists),

        ];
    }

    public function saveCategoryDiscounts(User $user, Request $request)
    {
        $request->validate([
            'discounts.categoriesTree' => ['array'],
            'discounts.categoriesTree.*.id' => ['required', 'integer'],
            'discounts.categoriesTree.*.enabled' => ['nullable'],
            'discounts.categoriesTree.*.sale' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discounts.categoriesTree.*.children' => ['nullable', 'array'],
            'discounts.categoriesTree.*.children.*.id' => ['required', 'integer'],
            'discounts.categoriesTree.*.children.*.enabled' => ['nullable'],
            'discounts.categoriesTree.*.children.*.sale' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $tree = $request->input('discounts.categoriesTree', []);
        $userId = (int) $user->id;

        // разворачиваем дерево в плоский список с учетом правила "родитель -> дети"
        $rows = [];
        foreach ($tree as $parent) {
            $parentId = (int) ($parent['id'] ?? 0);
            if ($parentId <= 0) {
                continue;
            }

            $parentEnabled = filter_var($parent['enabled'] ?? false, FILTER_VALIDATE_BOOL);
            $parentSale = $parent['sale'] ?? null;
            $parentSale = ($parentSale === '' || $parentSale === null) ? null : (float) $parentSale;

            $rows[] = [
                'category_id' => $parentId,
                'enabled'     => $parentEnabled,
                'sale'        => $parentSale,
            ];

            foreach (($parent['children'] ?? []) as $child) {
                $childId = (int) ($child['id'] ?? 0);
                if ($childId <= 0) {
                    continue;
                }

                // ключевое: если родитель включен — ребенок включен независимо от чекбокса
                $childEnabled = $parentEnabled
                    ? true
                    : filter_var($child['enabled'] ?? false, FILTER_VALIDATE_BOOL);

                $childSale = $child['sale'] ?? null;
                $childSale = ($childSale === '' || $childSale === null) ? null : (float) $childSale;

                $rows[] = [
                    'category_id' => $childId,
                    'enabled'     => $childEnabled,
                    'sale'        => $childSale,
                ];
            }
        }

        DB::transaction(function () use ($rows, $userId) {
            foreach ($rows as $row) {
                $categoryId = (int) $row['category_id'];
                $enabled = (bool) $row['enabled'];
                $sale = $row['sale'];

                if ($enabled) {
                    UserSaleSystem::query()->updateOrCreate(
                        ['user_id' => $userId, 'category_id' => $categoryId],
                        ['sale' => $sale]
                    );

                    DB::table('user_sales')->updateOrInsert(
                        ['user_id' => $userId, 'category_id' => $categoryId],
                        ['sale' => $sale]
                    );
                } else {
                    UserSaleSystem::query()
                        ->where('user_id', $userId)
                        ->where('category_id', $categoryId)
                        ->delete();

                    DB::table('user_sales')
                        ->where('user_id', $userId)
                        ->where('category_id', $categoryId)
                        ->delete();
                }
            }
        });

        Toast::info('Скидки по категориям сохранены.');
        return back();
    }


    public function saveBrandDiscounts(User $user, Request $request)
    {
        //dd($request->all());
        $request->validate([
            'discounts.brands' => ['array'],
            'discounts.brands.*.id' => ['required', 'integer'],
            'discounts.brands.*.enabled' => ['nullable'],
            'discounts.brands.*.sale' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $rows = $request->input('discounts.brands', []);
        $userId = (int) $user->id;

        DB::transaction(function () use ($rows, $userId) {
            foreach ($rows as $row) {
                $brandId = (int) ($row['id'] ?? 0);
                $enabled = filter_var($row['enabled'] ?? false, FILTER_VALIDATE_BOOL);
                $sale = $row['sale'];

                $sale = ($sale === '' || $sale === null) ? null : (float) $sale;

                if ($brandId <= 0) {
                    continue;
                }

                if ($enabled) {
                    \App\Models\UserBrandSaleSystem::query()->updateOrCreate(
                        ['user_id' => $userId, 'brand_id' => $brandId],
                        ['sale' => $sale, 'amount' => 0]
                    );

                    DB::table('user_brand_sales')->updateOrInsert(
                        ['user_id' => $userId, 'brand_id' => $brandId],
                        ['sale' => $sale]
                    );
                } else {
                    \App\Models\UserBrandSaleSystem::query()
                        ->where('user_id', $userId)
                        ->where('brand_id', $brandId)
                        ->delete();

                    DB::table('user_brand_sales')
                        ->where('user_id', $userId)
                        ->where('brand_id', $brandId)
                        ->delete();
                }
            }
        });

        Toast::info('Скидки по брендам сохранены.');
        return back();
    }



    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::tabs([
                'Профиль' => [
                    UserEditLayout::class,
                    UserPasswordLayout::class,
                ],
                'Скидки' => [
                    Layout::view('orchid.users.discounts_categories'),
                    UserBrandDiscountsLayout::class,
                ],

                'Роли и права' => [
                    UserRoleLayout::class,
                    RolePermissionLayout::class,
                ],
            ]),
        ];
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(User $user, Request $request)
    {
        $request->validate(
            [
                'user.email' => [
                    'required',
                    Rule::unique(User::class, 'email')->ignore($user),
                ],

                'user.password' => $user->exists
                    ? ['nullable', 'string', 'min:8', 'same:user.password_confirmation']
                    : ['required', 'string', 'min:8', 'same:user.password_confirmation'],

                'user.password_confirmation' => $user->exists
                    ? ['nullable', 'string', 'min:8']
                    : ['required', 'string', 'min:8'],
                'user.img_url' => ['nullable', 'string'],
                'user.phon' => ['nullable', 'string', 'max:50'],
                'user.city' => ['nullable', 'string', 'max:255'],
                'user.personal_sale' => ['nullable', 'numeric', 'min:0', 'max:100'],
            ],
            [
                'user.email.required' => 'Email обязателен.',
                'user.email.unique' => 'Пользователь с таким Email уже существует.',

                'user.password.required' => 'Пароль обязателен при создании пользователя.',
                'user.password.min' => 'Пароль должен быть не короче 8 символов.',
                'user.password.same' => 'Пароли не совпадают.',

                'user.password_confirmation.required' => 'Подтверждение пароля обязательно.',
                'user.password_confirmation.min' => 'Подтверждение пароля должно быть не короче 8 символов.',
                'user.personal_sale.min' => 'Скидка не может быть меньше 0%.',
                'user.personal_sale.max' => 'Скидка не может быть больше 100%.',
            ]
        );

        $userData = $request->collect('user')->toArray();

        if (isset($userData['img_url']) && is_string($userData['img_url'])) {
            $img = trim($userData['img_url']);

            if ($img === '') {
                $userData['avatar'] = null;
            } else {
                $storagePrefix = rtrim(asset('storage'), '/') . '/';
                if (str_starts_with($img, $storagePrefix)) {
                    $userData['avatar'] = ltrim(substr($img, strlen($storagePrefix)), '/');
                }
            }
        }

        if (array_key_exists('personal_sale', $userData)) {
            $value = $userData['personal_sale'];
            $userData['personal_sale'] = ($value === '' || $value === null) ? null : (float) $value;
        }

        if (array_key_exists('active', $userData)) {
            $userData['active'] = filter_var($userData['active'], FILTER_VALIDATE_BOOL) ? 'on' : 'off';
        }

        if (array_key_exists('manager_id', $userData) && ($userData['manager_id'] === '' || $userData['manager_id'] === null)) {
            $userData['manager_id'] = null;
        }

        $permissions = collect($request->get('permissions'))
            ->map(fn ($value, $key) => [base64_decode($key) => $value])
            ->collapse()
            ->toArray();

        $user->when($request->filled('user.password'), function (Builder $builder) use ($request) {
            $builder->getModel()->password = Hash::make($request->input('user.password'));
        });

        $user
            ->fill(collect($userData)->except(['password', 'password_confirmation', 'permissions', 'roles'])->toArray())
            ->forceFill(['permissions' => $permissions])
            ->save();

        $user->replaceRoles($request->input('user.roles'));

        Toast::info('Пользователь сохранён.');

        return redirect()->route('platform.systems.users');

    }

    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(User $user)
    {
        $user->delete();

        Toast::info(__('User was removed'));

        return redirect()->route('platform.systems.users');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAs(User $user)
    {
        Impersonation::loginAs($user);

        Toast::info(__('You are now impersonating this user'));

        return redirect()->route(config('platform.index'));
    }
}
