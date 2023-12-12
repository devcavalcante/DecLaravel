<?php

namespace App\Services;

use App\Enums\TypeUserEnum;
use App\Exceptions\AuthorizedException;
use App\Exceptions\EmailExists;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Passport\TokenRepository;
use Throwable;

class AuthService
{
    const CODE = 'code';

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
            $data['password'] = bcrypt($data['password']);
            $data['creator_user_id'] = Auth::id();
            $user = $this->userRepository->findByFilters(['email' => Arr::get($data, 'email')]);

            if ($user->isNotEmpty()) {
                throw new EmailExists();
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

    public function getAuthorizationUrl(): string
    {
        $baseUrl = env('AUTH_SERVER_URL');
        return sprintf(
            '%s/authorize?%s',
            $baseUrl,
            http_build_query([
                'client_id'     => env('CLIENT_ID'),
                'response_type' => self::CODE,
                'redirect_uri'  =>  env('REDIRECT_URI'),
            ])
        );
    }

    /**
     * @throws GuzzleException
     * @throws Throwable
     */
    public function getUsers(string $code): array|string|null
    {
        try {
            $token = $this->makeRequestAuthorization($code);
            $user = $this->makeRequestGetUserInfo(Arr::get($token, 'access_token'));
            return $this->createOrShowUser($user, $token);
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    /**
     * @throws AuthorizedException
     */
    public function logoutUsers(string $token): void
    {
        $token = Str::substr($token, 7);

        if (!$token) {
            throw new AuthorizedException('O Token é obrigatório', 401);
        }

        $user = $this->userRepository->findByFilters(['api_token' => $token])->first();

        if (!$user) {
            throw new AuthorizedException('Token inválido', 401);
        }

        $this->userRepository->update($user->id, [
            'api_token' => null, 'api_token_expires_at' => null
        ]);
    }

    private function createOrShowUser(array $user, array $token): Collection|Model
    {
        $typeUser = $this->typeUserRepository->findByFilters(['name' => TypeUserEnum::VIEWER]);
        $milliseconds = Arr::get($token, 'expires_in');

        $data = [
            'name'                 => Arr::get($user, 'nome-pessoa'),
            'active'               => Arr::get($user, 'ativo'),
            'email'                => Arr::get($user, 'email'),
            'url_photo'            => Arr::get($user, 'url-foto'),
            'type_user_id'         => $typeUser->first()->id,
            'api_token'            =>  Arr::get($token, 'access_token'),
            'api_token_expires_at' => Carbon::now()->addMilliseconds($milliseconds),
        ];

        $user = $this->userRepository->findByFilters(['email' => Arr::get($data, 'email')])->first();

        if (!$user) {
            return $this->userRepository->create($data);
        }

        return $user;
    }

    /**
     * @throws GuzzleException
     */
    private function makeRequestGetUserInfo(string $token): array
    {
        $client = new Client();

        $response = $client->get('https://api.dev.ufopa.edu.br/usuario/v1/usuarios/info', [
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $token),
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @throws GuzzleException
     */
    private function makeRequestAuthorization(string $code)
    {
        $client = new Client();

        $response = $client->post(env('AUTH_SERVER_URL') . '/token', [
            'form_params' => [
                'client_id'     => env('CLIENT_ID'),
                'client_secret' => env('CLIENT_SECRET'),
                'redirect_uri'  => env('REDIRECT_URI'),
                'grant_type'    => 'authorization_code',
                'code'          => $code,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
