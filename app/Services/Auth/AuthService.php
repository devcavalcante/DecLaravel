<?php

namespace App\Services\Auth;

use App\Enums\TypeUserEnum;
use App\Exceptions\AuthorizedException;
use App\Exceptions\EmailExists;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\TokenRepository;
use Throwable;

class AuthService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected TokenRepository $tokenRepository,
        protected TypeUserRepositoryInterface $typeUserRepository,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function register(array $data): Model
    {
        try {
            DB::beginTransaction();
            $typeUser = $this->typeUserRepository->findByFilters(['name' => TypeUserEnum::ADMIN])->first();

            $data['password'] = bcrypt($data['password']);
            $data['type_user_id'] = $typeUser->id;

            $user = $this->userRepository->findByFilters(['email' => Arr::get($data, 'email')]);

            if ($user->isNotEmpty()) {
                throw new EmailExists();
            }

            $data = Arr::except($data, 'c_password');
            $user = $this->userRepository->create($data);
            $user['token'] = $user->createToken(env('APP_NAME'))->accessToken;
            DB::commit();
            return $user;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    /**
     * @throws AuthorizedException
     */
    public function login(array $data): array|Authenticatable
    {
        $auth = Auth::guard('web');
        if ($auth->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = $auth->user();
            $user['token'] =  $user->createToken(env('APP_NAME'))->accessToken;
            return $user;
        }

        throw new AuthorizedException('Não autorizado', 401);
    }

    /**
     * @throws AuthorizedException
     */
    public function logout(): void
    {
        $this->tokenRepository->revokeAccessToken(Auth::guard('api')->user()->token()->id);
    }
}
