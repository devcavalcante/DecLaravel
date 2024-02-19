<?php

namespace App\Services\Auth;

use App\Exceptions\AuthorizedException;
use App\Exceptions\EmailExists;
use App\Models\User;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\RepresentativeRepositoryInterface;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Passport\TokenRepository;
use Throwable;
use Illuminate\Support\Facades\Password;

class AuthService extends AbstractAuthService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected TokenRepository $tokenRepository,
        protected RepresentativeRepositoryInterface $representativeRepository,
        protected TypeUserRepositoryInterface $typeUserRepository,
        protected MemberRepositoryInterface $memberRepository,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function register(array $data): Model
    {
        try {
            DB::beginTransaction();
            $data['password'] = bcrypt($data['password']);
            $email = Arr::get($data, 'email');
            $user = $this->userRepository->findByFilters(['email' => $email]);

            if ($user->isNotEmpty()) {
                throw new EmailExists();
            }

            $user = $this->createUser($data);
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
        $this->tokenRepository->revokeAccessToken(Auth::guard('api')->user()->token()->id);
    }

    private function createUser(array $data): Model
    {
        $user = $this->createUserWithType($data);
        $user['token'] = $user->createToken(env('APP_NAME'))->accessToken;

        return $user;
    }

    public function forgotPassword(array $email): array
    {
        $status = Password::sendResetLink($email);
        return ['status' => __($status)];
    }

    public function resetPassword(array $data): array
    {
        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return ['status' => __($status)];
    }
}
