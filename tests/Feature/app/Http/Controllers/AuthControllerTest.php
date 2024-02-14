<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Enums\TypeUserEnum;
use App\Models\Member;
use App\Models\Representative;
use App\Models\TypeUser;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Laravel\Passport\Passport;
use Tests\Feature\Utils\LoginUsersTrait;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginUsersTrait;

    public const BASE_URL = 'api/users';

    public function testShouldCreate()
    {
        $payload = $this->getFakePayload();
        $response = $this->post('/api/register', $payload);
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals(Arr::only($payload, ['name', 'email']), Arr::only($actual, ['name', 'email']));
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testShouldSetUserRepresentativeWhenCreate()
    {
        $representative = Representative::factory(['email' => 'teste@teste.com'])->create();

        $payload = [
            'name'       => 'Test Name',
            'email'      => $representative->email,
            'password'   => '12345678',
            'c_password' => '12345678',
        ];
        $response = $this->post('/api/register', $payload);
        $actual = json_decode($response->getContent(), true);

        $representative = Representative::where(['email' => $representative->email])->first();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($representative->user_id, $actual['id']);
        $this->assertEquals(3, $actual['type_user_id']);
    }

    public function testShouldSetUserMemberWhenCreate()
    {
        $member = Member::factory(['email' => 'teste@teste.com'])->create();

        $payload = [
            'name'       => 'Test Name',
            'email'      =>  $member->email,
            'password'   => '12345678',
            'c_password' => '12345678',
        ];
        $response = $this->post('/api/register', $payload);
        $actual = json_decode($response->getContent(), true);

        $member = Member::where(['email' => $member->email])->first();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($member->user_id, $actual['id']);
        $this->assertEquals(4, $actual['type_user_id']);
    }

    public function testShouldNotCreateWhenValidationErrors()
    {
        $payload = [
            'name'       => 'Test Name',
            'email'      => 'teste',
            'password'   => '12345678',
            'c_password' => '12345678',
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
        User::factory(['type_user_id' => $typeUser->id])->create();
        $payload = Arr::except($this->getFakePayload($typeUser->id), 'c_password');

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

        $this->assertEquals('Não autorizado', $actual['errors']);
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
        $this->assertEquals('Não autorizado', $actual['errors']);
    }

    public function testShouldForgotPassword()
    {
        Mail::fake();
        $user = User::first();
        $response = $this->post('api/forgot-password', ['email' => $user->email]);
        $actual = json_decode($response->getContent(), true);

        $this->assertEquals('Enviamos seu link de redefinição de senha por e-mail!', $actual['status']);
    }

    public function testShouldNotForgotPassword()
    {
        Mail::fake();
        $response = $this->post('api/forgot-password', ['email' => 'abc@mail.com']);
        $actual = json_decode($response->getContent(), true);

        $this->assertEquals('Não encontramos um usuário com esse endereço de e-mail.', $actual['status']);
    }

    public function testShouldResetPassword()
    {
        // Crie um usuário de teste
        $user = User::factory()->create(['email' => 'test@example.com']);

        // Crie dados de redefinição de senha simulados
        $resetData = [
            'email' => 'test@example.com',
            'token' => 'fake-token', // Simulando um token válido
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];

        // Mock Password::reset() para simular uma redefinição de senha bem-sucedida
        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::PASSWORD_RESET);

        $response = $this->post('api/reset-password', $resetData);
        $actual = json_decode($response->getContent(), true);
        // Verifique se a redefinição de senha foi bem-sucedida
        $this->assertEquals(['status' => 'Sua senha foi redefinida!'], $actual);
    }

    public function testShouldNotResetPassword()
    {
        // Crie dados de redefinição de senha simulados
        $resetData = [
            'email' => 'test@example.com',
            'token' => 'fake-token', // Simulando um token válido
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];

        $response = $this->post('api/reset-password', $resetData);
        $actual = json_decode($response->getContent(), true);
        $user = User::where(['email' => 'test@example.com'])->first();
        // Verifique se a redefinição de senha foi bem-sucedida
        $this->assertEquals(['status' => 'Este token de redefinição de senha é inválido.'], $actual);
    }

    private function getFakePayload(): array
    {
        return [
            'name'       => 'Test Name',
            'email'      => 'teste@email.com',
            'password'   => '12345678',
            'c_password' => '12345678',
        ];
    }
}
