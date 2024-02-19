<?php

namespace App\Services\Auth;

use App\Enums\TypeUserEnum;
use App\Exceptions\AuthorizedException;
use App\Repositories\Interfaces\ApiTokenRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\RepresentativeRepositoryInterface;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Passport\TokenRepository;
use Throwable;

class AuthAPIService extends AbstractAuthService
{
    const CODE = 'code';

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected TokenRepository $tokenRepository,
        protected TypeUserRepositoryInterface $typeUserRepository,
        protected ApiTokenRepositoryInterface $apiTokenRepository,
        protected RepresentativeRepositoryInterface $representativeRepository,
        protected MemberRepositoryInterface $memberRepository,
    ) {
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
    public function loginWithAPIUFOPA(?string $code): Model|Collection
    {
        try {
            if (is_null($code)) {
                throw new \InvalidArgumentException('É necessário o código');
            }
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
            'api_token'            => null,
            'api_token_expires_at' => null,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    private function createOrShowUser(array $user, array $token): Collection|Model
    {
        $typeUser = $this->typeUserRepository->findByFilters(['name' => TypeUserEnum::VIEWER])->first();

        $data = [
            'name'         => Arr::get($user, 'nome-pessoa'),
            'active'       => Arr::get($user, 'ativo'),
            'email'        => Arr::get($user, 'email'),
            'url_photo'    => Arr::get($user, 'url-foto'),
            'type_user_id' => $typeUser->id,
        ];

        $user = $this->userRepository->findByFilters(['email' => Arr::get($data, 'email')])->first();

        if (!$user) {
            $user = $this->createUserWithType($data);
        }

        $this->createOrUpdateApiToken($token, $user->id);

        return $user->load('apiToken');
    }

    /**
     * @throws GuzzleException
     */
    private function createOrUpdateApiToken(array $token, string $userId): void
    {
        $milliseconds = Arr::get($token, 'expires_in');
        $apiToken = $this->apiTokenRepository->findByFilters(['user_id' => $userId])->first();

        if (!$apiToken) {
            $this->apiTokenRepository->create([
                'api_token'            => Arr::get($token, 'access_token'),
                'api_token_expires_at' => Carbon::now()->addMilliseconds($milliseconds),
                'user_id'              => $userId,
            ]);
        }

        if ($apiToken && now()->gt($apiToken->api_token_expires_at)) {
            $response = $this->makeRequestRefreshToken(Arr::get($token, 'refresh_token'));
            $milliseconds = Arr::get($response, 'expires_in');

            $this->apiTokenRepository->update($apiToken->id, [
                'api_token'            => Arr::get($token, 'access_token'),
                'api_token_expires_at' => Carbon::now()->addMilliseconds($milliseconds),
            ]);
        }
    }

    /**
     * @throws GuzzleException
     */
    private function makeRequestGetUserInfo(string $token): array
    {
        $client = new Client();
        $baseUrl = env('BASE_URL');

        $response = $client->get(sprintf('%s/usuario/v1/usuarios/info', $baseUrl), [
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $token),
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @throws GuzzleException
     */
    private function makeRequestAuthorization(string $code): array
    {
        $authServerUrl = env('AUTH_SERVER_URL');
        $clientID = env('CLIENT_ID');
        $clientSecret = env('CLIENT_SECRET');
        $redirectUri = env('REDIRECT_URI');
        $grantType = 'authorization_code';

        $endpoint = "{$authServerUrl}token?client_id={$clientID}&client_secret={$clientSecret}&redirect_uri={$redirectUri}&grant_type={$grantType}&code={$code}";

        $client = new Client();
        $response = $client->post($endpoint);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @throws GuzzleException
     */
    private function makeRequestRefreshToken(string $refreshToken): array
    {
        $client = new Client();

        $response = $client->post(env('AUTH_SERVER_URL') . '/token', [
            'form_params' => [
                'client_id'     => env('CLIENT_ID'),
                'client_secret' => env('CLIENT_SECRET'),
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
