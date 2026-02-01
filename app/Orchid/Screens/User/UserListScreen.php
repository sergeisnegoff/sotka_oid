<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserListLayout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
class UserListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Request $request): iterable
    {

        $q = User::query()->with(['roles', 'managerContact']);

        // 1) Роли
        $roleIds = $request->input('filters.roles', []);
        if (is_array($roleIds) && count($roleIds)) {
            $q->whereHas('roles', function($query) use ($roleIds) {
                $query->whereIn('id', $roleIds);
            });
        }
        // 2) Менеджер
        $managerId = $request->input('filters.manager_id');
        if ($managerId) {
            $q->where('manager_id', $managerId);
        }

        // 3) Диапазон даты создания
        $from = $request->input('filters.created_from');
        if ($from) {
            $q->whereDate('created_at', '>=', $from);
        }

        $to = $request->input('filters.created_to');
        if ($to) {
            $q->whereDate('created_at', '<=', $to);
        }

        // 4) Поиск по полю + запрос
        $field = $request->input('filters.field');
        $text  = trim((string) $request->input('filters.q', ''));

        if ($text !== '') {
            $allowed = ['name', 'email', 'phon', 'city'];
            if ($field && in_array($field, $allowed, true)) {
                $q->where($field, 'like', "%{$text}%");
            } else {
                $q->where(function ($b) use ($text) {
                    $b->where('name', 'like', "%{$text}%")
                        ->orWhere('email', 'like', "%{$text}%")
                        ->orWhere('phon', 'like', "%{$text}%")
                        ->orWhere('city', 'like', "%{$text}%");
                });
            }
        }

        // Получаем параметр сортировки от Orchid
        $sortParam = $request->input('sort', '-id');

        // Парсим параметр сортировки
        if (str_starts_with($sortParam, '-')) {
            $sortColumn = substr($sortParam, 1);
            $sortDirection = 'desc';
        } else {
            $sortColumn = $sortParam;
            $sortDirection = 'asc';
        }
        if ($sortColumn === 'manager') {
            // Для сортировки по менеджеру нужен join
            $q->leftJoin('contacts_managers as manager_contact', 'users.manager_id', '=', 'manager_contact.id')
                ->select('users.*')
                ->orderBy('manager_contact.name', $sortDirection);
        } else {
            // Для обычных колонок
            $q->orderBy($sortColumn, $sortDirection);
        }

        return [
            'users' => $q->paginate(),
            'filters' => $request->input('filters', []),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'User Management';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'A comprehensive list of all registered users, including their profiles and privileges.';
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
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->route('platform.systems.users.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('orchid.users.filters'),

            UserListLayout::class,

            Layout::modal('editUserModal', UserEditLayout::class)
                ->deferred('loadUserOnOpenModal'),
        ];
    }

    /**
     * Loads user data when opening the modal window.
     *
     * @return array
     */
    public function loadUserOnOpenModal(User $user): iterable
    {
        return [
            'user' => $user,
        ];
    }

    public function saveUser(Request $request, User $user): void
    {
        $request->validate([
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($user),
            ],
        ]);

        $user->fill($request->input('user'))->save();

        Toast::info(__('User was saved.'));
    }

    public function remove(Request $request): void
    {
        User::findOrFail($request->get('id'))->delete();

        Toast::info(__('User was removed'));
    }

    public function applyFilters(Request $request)
    {
        return redirect()->to(url()->current() . '?' . http_build_query([
                'filters' => $request->input('filters', []),
            ]));
    }

    public function resetFilters()
    {
        return redirect()->to(url()->current());
    }
}
