<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Enums\TypeUserEnum;
use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Laravel\Passport\Passport;
use Tests\Feature\Utils\LoginUsersTrait;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginUsersTrait;

    public const BASE_URL = 'api/users';

    public function testShouldCreateWhenUserIsAdmin()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::ADMIN)->first();
        $user = User::factory(['type_user_id' => $typeUser->id])->create();
        $payload = $this->getFakePayload($typeUser->id);
        Passport::actingAs($user);
        $response = $this->post('/api/register', $payload);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals(Arr::only($payload, ['name', 'email']), Arr::only($actual, ['name', 'email']));
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testShouldCreateWhenUserIsManagerAndRegisterRepresentative()
    {
        $typeUserManager = TypeUser::where('name', TypeUserEnum::MANAGER)->first();
        $typeUserRepresentative = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $user = User::factory(['type_user_id' => $typeUserManager->id])->create();
        $payload = $this->getFakePayload($typeUserRepresentative->id);
        Passport::actingAs($user);
        $response = $this->post('/api/register', $payload);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals(Arr::only($payload, ['name', 'email']), Arr::only($actual, ['name', 'email']));
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testShouldCreateWhenUserIsRepresentativeAndRegisterViewer()
    {
        $this->artisan('db:seed');

        $typeUserRepresentative = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $typeUserViewer = TypeUser::where('name', TypeUserEnum::VIEWER)->first();
        $user = User::factory(['type_user_id' => $typeUserRepresentative->id])->create();
        $payload = $this->getFakePayload($typeUserViewer->id);

        Passport::actingAs($user);

        $response = $this->post('/api/register', $payload);
        $actual = json_decode($response->getContent(), true);

        $this->assertEquals(Arr::only($payload, ['name', 'email']), Arr::only($actual, ['name', 'email']));
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testShouldNotCreateWhenOutsidePermissionRule()
    {
        $typeUserRepresentative = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $typeUserManager = TypeUser::where('name', TypeUserEnum::MANAGER)->first();
        $user = User::factory(['type_user_id' => $typeUserRepresentative->id])->create();
        $payload = $this->getFakePayload($typeUserManager->id);
        Passport::actingAs($user);
        $response = $this->post('/api/register', $payload);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals('This action is unauthorized.', $actual['errors']);
        $this->assertEquals(403, $actual['code']);
    }

    public function testShouldNotCreateWhenValidationErrors()
    {
        $payload = [
            'name'         => 'Test Name',
            'email'        => 'teste',
            'password'     => '12345678',
            'c_password'   => '12345678',
            'type_user_id' => 1,
        ];

        $this->login(TypeUserEnum::ADMIN);

        $response = $this->post('api/register', $payload);
        $actual = json_decode($response->getContent(), true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('Email invalido.', Arr::first($actual['errors']['email']));
    }

    public function testShouldNotCreateWhenUserExists()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::ADMIN)->first();
        $user = User::factory(['type_user_id' => $typeUser->id])->create();
        $payload = Arr::except($this->getFakePayload($typeUser->id), 'c_password');

        Passport::actingAs($user);
        User::factory($payload)->create();

        $response = $this->postJson('api/register', $this->getFakePayload($typeUser->id));
        $actual = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Esse e-mail ja esta cadastrado', $actual['errors']);
    }

    public function testShouldLogin()
    {
        $user = User::first();
        $response = $this->postJson('api/login', ['email' => $user->email, 'password' => 'visualizador']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testShouldErrorWithInvalidCredentials()
    {
        $user = User::factory()->create();
        $response = $this->postJson('api/login', ['email' => $user->email, 'password' => '12345678']);
        $this->assertEquals(401, $response->getStatusCode());
        $actual = json_decode($response->getContent(), true);

        $this->assertEquals('Nao autorizado', $actual['errors']);
    }

    public function testShouldLogout()
    {
        $user = User::first();
        Passport::actingAs($user);
        $response = $this->post(sprintf('%s/logout', self::BASE_URL));
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testShouldLogoutWhenUserNotAuthenticate()
    {
        $response = $this->postJson(sprintf('%s/logout', self::BASE_URL));
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals('Unauthorized.', $actual['errors']);
    }

    private function getFakePayload(string $typeUserId): array
    {
        return [
            'name'         => 'Test Name',
            'email'        => 'teste@email.com',
            'password'     => '12345678',
            'c_password'   => '12345678',
            'type_user_id' => $typeUserId,
        ];
    }
}
