<?php

namespace App\Services;

use App\Exceptions\AuthorizedException;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\TokenRepository;
use Throwable;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected TokenRepository $tokenRepository,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function register(array $data): Model
    {
        try {
            DB::beginTransaction();
            $data['creator_user_id'] = Auth::id();
            $user = $this->userRepository->create($data);
            $this->createPin($data['email']);
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
            $user['token'] = $user->createToken(env('APP_NAME'))->accessToken;
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

    /**
     * @throws AuthorizedException
     * @throws Throwable
     */
    public function verifyEmail(array $data): void
    {
        try {
            DB::beginTransaction();

            $email = Arr::get($data, 'email');
            $token = Arr::get($data, 'token');

            $this->verifyPin($email, $token);
            $this->userRepository->deletePasswordResetToken($email, $token);
            $this->saveCredentialsUser($data);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    private function createPin(string $email): void
    {
        $verify = $this->userRepository->findPasswordResetTokenByEmail($email);

        if ($verify->exists()) {
            $verify->delete();
        }

        $pin = rand(100000, 999999);
        $this->userRepository->createPasswordResetToken($email, $pin);
        Mail::to($email)->send(new VerifyEmail($pin));
    }

    /**
     * @throws AuthorizedException
     */
    public function verifyPin(string $email, string $token): bool
    {
        $check = $this->userRepository->findPasswordResetTokenByEmailAndToken($email, $token);

        if ($check->isEmpty()) {
            throw new AuthorizedException('Invalid PIN', 400);
        }

        $difference = Carbon::now()->diffInSeconds($check->first()->created_at);
        if ($difference > 3600) {
            throw new AuthorizedException('Token expirado', 400);
        }

        return true;
    }

    private function saveCredentialsUser(array $data): void
    {
        $user = $this->userRepository->findByFilters(['email' => $data['email']])->first();
        $payload = [
            'email_verified_at' => Carbon::now(),
            'password' =>  bcrypt($data['password']),
        ];
        $this->userRepository->update($user->id, $payload);
    }
}
