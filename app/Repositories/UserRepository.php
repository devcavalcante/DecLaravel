<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    use CRUDTrait;

    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findPasswordResetTokenByEmail(string $email): Builder
    {
        return DB::table('password_reset_tokens')->where([['email', $email]]);
    }

    public function createPasswordResetToken(string $email, string $pin): bool
    {
        return DB::table('password_reset_tokens')->insert(
            [
                'email' => $email,
                'token' => $pin,
                'created_at' => Carbon::now()
            ]
        );
    }

    public function findPasswordResetTokenByEmailAndToken(string $email, string $token): Collection
    {
        return DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->get();
    }

    public function deletePasswordResetToken(string $email, string $token): void
    {
        DB::table('password_reset_tokens')->where('email', $email)->where('token', $token)->delete();
    }
}
