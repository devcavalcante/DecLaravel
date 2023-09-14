<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Enums\TypeUserEnum;
use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testIndexUsers()
    {
        $this->login();
        // Cria 10 usuários no banco de dados usando o model factory
        User::factory(10)->create();

        // Envia uma solicitação para listar todos os usuários
        $response = $this->get('/api/users');
        $actual = json_decode($response->getContent(), true);

        // Verifica se a solicitação foi bem-sucedida e se a resposta contém os usuários
        $response->assertStatus(200);
        $this->assertEquals(User::all()->toArray(), $actual);
    }

    public function testShouldNotListUsersWithoutPermission()
    {
        $this->loginViewer();
        User::factory(10)->create();

        $response = $this->get('/api/users');
        $response->assertStatus(403);
    }

    /**
     * Teste de falha: Verificar se um usuário inexistente retorna um erro 404.
     *
     * @return void
     */
    public function testIndexNotExistsUser()
    {
        $this->login();

        // Cria um ID inválido para um usuário inexistente
        $invalidId = 999;

        // Envia uma solicitação para exibir o usuário inexistente
        $response = $this->getJson('/api/users/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }

    public function testShowUser()
    {
        $this->login();

        // Cria um usuário no banco de dados usando o model factory
        $user = User::factory()->create();

        // Envia uma solicitação para exibir o usuário criado
        $response = $this->getJson('/api/users/' . $user->id);

        // Converte o objeto Carbon para string antes da asserção
        $userArray = $user->toArray();
        $userArray['email_verified_at'] = $user->email_verified_at->toISOString();

        // Verifica se a solicitação foi bem-sucedida e se os dados retornados são corretos
        $response->assertStatus(200)
            ->assertJson($userArray);
    }

    /**
     * Teste de falha: Verificar se um usuário inexistente retorna um erro 404.
     *
     * @return void
     */
    public function testShowNotExistingUser()
    {
        $this->login();

        // Cria um ID inválido para um usuário inexistente
        $invalidId = 999;

        // Envia uma solicitação para exibir o usuário inexistente
        $response = $this->getJson('/api/users/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }

    public function testDestroyUser()
    {
        $this->login();

        // Cria um usuário no banco de dados usando o model factory
        $user = User::factory()->create();

        // Envia uma solicitação para excluir o usuário criado
        $response = $this->deleteJson('/api/users/' . $user->id);

        // Verifica se a solicitação foi bem-sucedida e se a resposta está vazia (204)
        $response->assertStatus(204);

        // Verifica se o usuário foi removido corretamente do banco de dados
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function testDestroyNonExistingUser()
    {
        $this->login();

        // Cria um ID inválido para um usuário inexistente
        $invalidId = 999;

        // Envia uma solicitação para excluir o usuário inexistente
        $response = $this->deleteJson('/api/users/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }

    public function testRestoreUser()
    {
        $this->login();

        // Cria um usuário e apaga ele
        $user = User::factory()->create();
        $user->delete();

        // Envia uma solicitação para restaurar o usuário
        $response = $this->putJson("/api/users/restore/{$user->id}");

        // Verifica se a solicitação foi bem-sucedida
        $response->assertStatus(200);

        // Verifica se o usuário foi restaurado corretamente
        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'deleted_at' => null,
        ]);
    }

    public function testRestoreNotExistingUser()
    {
        $this->login();

        // Cria um ID inválido para um usuário inexistente
        $invalidId = 999;

        // Envia uma solicitação para restaurar o usuário inexistente
        $response = $this->putJson("/api/users/restore/{$invalidId}");

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }

    public function testShouldUpdate()
    {
        $user = $this->login();

        $response = $this->put(sprintf('api/users/%s', $user->id), ['name' => 'outro nome']);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testShouldNotUpdateOthersUsers()
    {
        $this->login();
        $user = User::factory()->create();

        $response = $this->put(sprintf('api/users/%s', $user->id), ['name' => 'outro nome']);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testShouldNotDestroyWithoutPermissions()
    {
        $this->loginViewer();
        $user = User::factory()->create();

        $response = $this->delete(sprintf('api/users/%s', $user->id), ['name' => 'outro nome']);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testShouldNotRestoreWithoutPermissions()
    {
        $this->loginViewer();
        $user = User::factory()->create();

        $response = $this->put(sprintf('api/users/restore/%s', $user->id), ['name' => 'outro nome']);

        $this->assertEquals(403, $response->getStatusCode());
    }

    private function login(): User
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::ADMIN)->first();
        $user = User::where('type_user_id', $typeUser->id)->first();
        Passport::actingAs($user);
        return $user;
    }

    private function loginViewer(): void
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::VIEWER)->first();
        $user = User::where('type_user_id', $typeUser->id)->first();
        Passport::actingAs($user);
    }
}
