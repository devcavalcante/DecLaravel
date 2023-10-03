<?php

namespace App\Services;

use App\Exceptions\AuthorizedException;
use App\Repositories\Interfaces\UserRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Laravel\Passport\TokenRepository;
use Throwable;

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
            $data['password'] = bcrypt($data['password']);
            $data['creator_user_id'] = Auth::id();
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

    public function getAuthorizationUrl(): string
    {
        $baseUrl = env('AUTH_SERVER_URL');
        return sprintf(
            '%s/authorize?%s',
            $baseUrl,
            http_build_query([
                'client_id' => env('CLIENT_ID'),
                'response_type' => 'code',
                'redirect_uri' =>  env('REDIRECT_URI')
            ])
        );
    }

    /**
     * @throws GuzzleException
     */
    public function handleCallback(string $code): array|string|null
    {
        $client = new Client();

        $response = $client->post(env('AUTH_SERVER_URL') . '/token', [
            'form_params' => [
                'client_id' => env('CLIENT_ID'),
                'client_secret' => env('CLIENT_SECRET'),
                'redirect_uri' => env('REDIRECT_URI'),
                'grant_type' => 'authorization_code',
                'code' => $code,
            ],
        ]);

        return $response->getBody()->getContents();
    }
}
