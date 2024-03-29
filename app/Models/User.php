<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;

class User extends Authenticatable implements CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPasswordTrait;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'type_user_id',
        'active',
        'url_photo',
        'password',
    ];

    protected $with = ['typeUser', 'apiToken'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'c_password',
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

    public function getNotFoundMessage(): string
    {
        return 'Usuário não encontrado';
    }

    public function typeUser(): BelongsTo
    {
        return $this->belongsTo(TypeUser::class);
    }

    public function apiToken(): HasOne
    {
        return $this->hasOne(ApiToken::class, 'user_id');
    }

    public function groupsMembers(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'members', 'user_id', 'group_id');
    }

    public function role(): string
    {
        return $this->typeUser->name;
    }
}
