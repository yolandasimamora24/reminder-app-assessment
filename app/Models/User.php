<?php

namespace App\Models;

use App\Http\Controllers\Api\v1\JobApplicationController;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Spatie\Permission\Traits\HasRoles;
// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Provider\ProviderReview;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class User extends Authenticatable
{
    use CrudTrait;
    use HasRoles;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $guard_name = 'backpack';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     *  Setup model event hooks
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
        });
    }

    /**
     * Get the user relationship for the Appointment.
     */
    public function appointment(): HasMany
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    public function changePassword($crud = false)
    {
        return '<a class="btn btn-sm btn-link" data-toggle="tooltip" title="Change password"><i class="la la-key"></i> Change Password</a>';
    }

    public function last_appointment(): HasOne
    {
        return $this->hasOne(Appointment::class)->where('status', 'completed')->latest();
    }
}