<?php

namespace App\Models;

use App\Mail\resetPassword;
use App\Orchid\Filters\ManagerFilter;
use App\Orchid\Filters\RoleFilter;
use App\Orchid\Filters\UserSearchFilter;
use App\Orchid\Presenters\UserPresenter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Orchid\Access\UserAccess;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class User extends \TCG\Voyager\Models\User {
    use HasFactory, Notifiable, UserAccess, AsSource, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password', 'uniq_code', 'manager_id', 'phon', 'city'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions'       => 'array',
    ];

    public  $filters = [
        RoleFilter::class,
        UserSearchFilter::class,
        ManagerFilter::class,
    ];

    public static function sendResetPasswordMail($mail)
    {
        $user = User::where('email', $mail)->first();

        if ($user) {
            $password = resetPassword::generatePassword();
            $user->password = Hash::make($password);
            $user->save();
            Mail::to($mail)->send(new resetPassword($password));

        } else {
            // handle case when user is not found
        }
    }

    public static function addSaleToCategory($category_id, $sale, $user) {
        return DB::table('user_sales')->insert(['category_id' => $category_id, 'sale' => $sale, 'user_id' => $user]);
    }

    public static function removeUserSales($id) {
        DB::table('user_sales')->where('user_id', $id)->delete();
    }

    public static function addSaleToBrand($brand_id, $sale, $id) {
        return DB::table('user_brand_sales')->insert(['brand_id' => $brand_id, 'sale' => $sale, 'user_id' => $id]);
    }

    public function address() {
        return $this->hasMany(UserAddress::class);
    }

    public function managerContact() {
        return $this->hasOne(ContactsManagersModel::class, 'id', 'manager_id');
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    //переписать
    public function ordersTotal() {
        $sum = 0;
        foreach ($this->orders as $order) {
            $sum += $order->total();
        }
        return $sum;
    }

    public function preordersTotal(?Preorder $preorder = null) {
        $preorders = $this->preorderCheckouts;
        if ($preorder) {
            $preorders = $preorders->where('preorder_id', $preorder->id);
        }
        $total = 0;
        foreach ($preorders as $preorder) {
            $total += $preorder->total();
        }
        return $total;
    }


    public function preorderCheckouts() {
        return $this->hasMany(PreorderCheckout::class);
    }

    public function preorderCheckoutsForCurrentManager() {
        return PreorderCheckout::whereHas('user', function ($query) {
            $query->where('manager_id', auth()->user()->managerContact->id);
        });
    }

    public function presenter(): UserPresenter
    {
        return new UserPresenter($this);
    }

}
