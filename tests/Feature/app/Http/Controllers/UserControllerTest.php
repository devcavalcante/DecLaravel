<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexUsers()
    {
        // Cria dois usuários no banco de dados usando o model factory
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Envia uma solicitação para listar todos os usuários
        $response = $this->getJson('/api/users');

        // Converte os objetos Carbon para strings antes da asserção
        $user1Array = $user1->toArray();
        $user1Array['email_verified_at'] = $user1->email_verified_at->toISOString();

        $user2Array = $user2->toArray();
        $user2Array['email_verified_at'] = $user2->email_verified_at->toISOString();

        // Verifica se a solicitação foi bem-sucedida e se a resposta contém os usuários
        $response->assertStatus(200)
            ->assertJson([
                $user1Array,
                $user2Array,
            ]);
    }

    /**
     * Teste de falha: Verificar se um usuário inexistente retorna um erro 404.
     *
     * @return void
     */
    public function testIndexNotExistsUser()
    {
        // Cria um ID inválido para um usuário inexistente
        $invalidId = 999;

        // Envia uma solicitação para exibir o usuário inexistente
        $response = $this->getJson('/api/users/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }
    public function testShowUser()
    {
        // Cria um usuário no banco de dados usando o model factory
        $user = User::factory()->create();

        // Envia uma solicitação para exibir o usuário criado
        $response = $this->getJson('/api/user/' . $user->id);

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
    public function testShowNonExistingUser()
    {
        // Cria um ID inválido para um usuário inexistente
        $invalidId = 999;

        // Envia uma solicitação para exibir o usuário inexistente
        $response = $this->getJson('/api/user/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }

    public function testValidationSuccess()
    {
        $typeUser = TypeUser::factory()->create();
        $response = $this->postJson('/api/user', [
            'name'         => 'John Doe',
            'password'     => 'password123',
            'email'        => 'john@example.com',
            'type_user_id' => $typeUser->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name'         => 'John Doe',
                'email'        => 'john@example.com',
                'type_user_id' => $typeUser->id,
            ]);
    }

    public function testValidationFailedNameRequired()
    {
        $response = $this->postJson('/api/user', []);

        $response->assertStatus(422);
    }

    public function testValidationFailedNameString()
    {
        $response = $this->postJson('/api/user', [
            "name" => 123,
        ]);

        $response->assertStatus(422);
    }

    public function testValidationFailedNameMin()
    {
        $response = $this->postJson('/api/user', [
            "name" => "abc",
        ]);

        $response->assertStatus(422);
    }

    public function testValidationFailedPasswordRequired()
    {
        $response = $this->postJson('/api/user', []);

        $response->assertStatus(422);
    }

    public function testValidationFailedPasswordString()
    {
        $response = $this->postJson('/api/user', [
            "password" => 123,
        ]);

        $response->assertStatus(422);
    }

    public function testValidationFailedPasswordMin()
    {
        $response = $this->postJson('/api/user', [
            "password" => "1234567",
        ]);

        $response->assertStatus(422);
    }

    public function testValidationFailedEmailRequired()
    {
        $response = $this->postJson('/api/user', []);

        $response->assertStatus(422);
    }

    public function testValidationFailedEmailEmail()
    {
        $response = $this->postJson('/api/user', [
            "email" => "invalid_email",
        ]);

        $response->assertStatus(422);
    }

    public function testValidationFailedEmailString()
    {
        $response = $this->postJson('/api/user', [
            "email" => 123,
        ]);

        $response->assertStatus(422);
    }

    public function testValidationFailedTypeUserIdRequired()
    {
        $response = $this->postJson('/api/user', []);

        $response->assertStatus(422);
    }

    public function testDestroyUser()
    {
        // Cria um usuário no banco de dados usando o model factory
        $user = User::factory()->create();

        // Envia uma solicitação para excluir o usuário criado
        $response = $this->deleteJson('/api/user/' . $user->id);

        // Verifica se a solicitação foi bem-sucedida e se a resposta está vazia (204)
        $response->assertStatus(204);

        // Verifica se o usuário foi removido corretamente do banco de dados
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function testDestroyNonExistingUser()
    {
        // Cria um ID inválido para um usuário inexistente
        $invalidId = 999;

        // Envia uma solicitação para excluir o usuário inexistente
        $response = $this->deleteJson('/api/user/' . $invalidId);

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }
    public function testRestoreUser()
    {
        // Cria um usuário e apaga ele
        $user = User::factory()->create();
        $user->delete();

        // Envia uma solicitação para restaurar o usuário
        $response = $this->putJson("/api/user/restore/{$user->id}");

        // Verifica se a solicitação foi bem-sucedida
        $response->assertStatus(200);

        // Verifica se o usuário foi restaurado corretamente
        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'deleted_at' => null,
        ]);
    }

    public function testRestoreNonExistingUser()
    {
        // Cria um ID inválido para um usuário inexistente
        $invalidId = 999;

        // Envia uma solicitação para restaurar o usuário inexistente
        $response = $this->putJson("/api/user/restore/{$invalidId}");

        // Verifica se a solicitação retornou um erro 404
        $response->assertStatus(404);
    }
}
