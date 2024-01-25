<?php

namespace App\Services;

use App\Enums\TypeUserEnum;
use App\Exceptions\AuthorizedException;
use App\Exceptions\EmailExists;
use App\Mail\ResetPassword;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\RepresentativeRepositoryInterface;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Laravel\Passport\TokenRepository;
use Throwable;

class AuthService
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

        throw new AuthorizedException('Nao autorizado', 401);
    }

    /**
     * @throws AuthorizedException
     */
    public function logout(): void
    {
        $this->tokenRepository->revokeAccessToken(Auth::guard('api')->user()->token()->id);
    }

    public function sendResetPasswordEmail($email): string
    {
        // Enviar e-mail de redefinição de senha
//        Mail::to($email)->send(new ResetPassword($token));
        return Password::sendResetLink(
            $email
        );
    }

    public function resetPassword(array $data): Model
    {
        $email = Arr::get($data, 'email');
        $token = Arr::get($data, 'token');
        $password = Arr::get($data, 'password');

        $user = $this->userRepository->findByFilters(['email'=>$email])->first();
        return $this->userRepository->update($user->id, ['password'=>$password]);

    }

    private function createUser(array $data): Model
    {
        $email = Arr::get($data, 'email');
        $representative = $this->representativeRepository->findByFilters(['email' => $email]);
        $member = $this->memberRepository->findByFilters(['email' => $email]);

        $typeUserId = $this->getTypeUserId($representative);

        $user = $this->userRepository->create(array_merge($data, ['type_user_id' => $typeUserId]));
        $this->updateRepresentativeOrMember($representative, $member, $user->id);
        $user['token'] = $user->createToken(env('APP_NAME'))->accessToken;

        return $user;
    }

    private function getTypeUserId($representative): string
    {
        if ($representative->isNotEmpty()) {
            return $this->getTypeUserIdByName(TypeUserEnum::REPRESENTATIVE);
        }
        return $this->getTypeUserIdByName(TypeUserEnum::VIEWER);
    }

    private function getTypeUserIdByName(string $typeName): string
    {
        return $this->typeUserRepository->findByFilters(['name' => $typeName])->first()->id;
    }

    private function updateRepresentativeOrMember($representative, $member, $userId): void
    {
        $payload = ['user_id' => $userId];
        if ($representative->isNotEmpty()) {
            $this->representativeRepository->update($representative->first()->id, $payload);
        }
        if ($member->isNotEmpty()) {
            $this->memberRepository->update($member->first()->id, $payload);
        }
    }
}
