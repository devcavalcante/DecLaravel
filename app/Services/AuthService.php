<?php

namespace App\Services;

use App\Exceptions\AuthorizedException;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class AuthService
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @throws Throwable
     */
    public function register(array $data): Model
    {
        try {
            DB::beginTransaction();
            $data['password'] = bcrypt($data['password']);
            $user = $this->userRepository->findByFilters(Arr::only($data, 'email'));

            if ($user) {
                throw new AuthorizedException('Usuario ja existe', 401);
            }

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
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();
            $user['token'] =  $user->createToken(env('APP_NAME'))-> accessToken;
            return $user;
        }

        throw new AuthorizedException('Não autorizado', 401);
    }

    /**
     * @throws AuthorizedException
     */
    public function logout(): void
    {
        if (!Auth::check()) {
            throw new AuthorizedException('Usuário não está logado', 401);
        }
        Auth::logout();
    }
}
