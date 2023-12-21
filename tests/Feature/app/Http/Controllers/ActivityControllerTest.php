<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Enums\TypeUserEnum;
use App\Models\Activity;
use App\Models\GroupHasRepresentative;
use App\Models\Group;
use App\Models\TypeUser;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\Feature\Utils\LoginUsersTrait;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginUsersTrait;

    const BASE_URL = 'api/activity';

    public function setUp(): void
    {
        $this->faker = FakerFactory::create();
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testShouldListAll()
    {
        $group = Group::factory()->create();
        Activity::factory(['group_id' => $group->id])->create();
        Activity::factory(['group_id' => $group->id])->create();

        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get(sprintf('api/group/%s/activity', $group->id));

        $response->assertStatus(200);
        $this->assertCount(2, json_decode($response->getContent(), true));
    }

    public function testShouldListOne()
    {
        $group = Group::factory()->create();
        $activity = Activity::factory(['group_id' => $group->id])->create();
        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get(sprintf('%s/%s', self::BASE_URL, $activity->id));

        $response->assertStatus(200);
        $response->assertJsonStructure($this->getJsonStructure());
    }

    public function testNotShouldListOneWhenNotFoundActivity()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get(sprintf('%s/%s', self::BASE_URL, 100));

        $response->assertStatus(404);
        $this->assertEquals('Atividade não encontrada', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldCreateIsRepresentative()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();

        $payload = [
            'name'        => 'teste teste',
            'description' => $this->faker->text,
        ];

        $response = $this->post(sprintf('/api/group/%s/activity', $group->id), $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('activities', array_merge($payload, ['group_id' => $group->id]));
    }

    public function testShouldCreateIsAdmin()
    {
        $userAdmin = $this->login(TypeUserEnum::ADMIN);
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userAdmin->id])->create();

        $payload = [
            'name'        => 'teste teste',
            'description' => $this->faker->text,
        ];

        $response = $this->post(sprintf('/api/group/%s/activity', $group->id), $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('activities', array_merge($payload, ['group_id' => $group->id]));
    }

    public function testShouldNotCreateWhenGroupNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();

        $payload = [
            'name'        => 'teste teste',
            'description' => $this->faker->text,
        ];

        $response = $this->post(sprintf('/api/group/%s/activity', 100), $payload);

        $response->assertStatus(404);
        $this->assertEquals('Grupo não encontrado', json_decode($response->getContent(), true)['errors']);
    }


    public function testShouldNotCreateWhenIsNotTheRepresentativeOfGroup()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory(['type_user_id' => $typeUser->id])->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $user1->id])->create();

        $payload = [
            'name'        => 'teste teste',
            'description' => $this->faker->text,
        ];

        $response = $this->post(sprintf('/api/group/%s/activity', $group->id), $payload);

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldUpdateIsRepresentative()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();
        $activity = Activity::factory(['group_id' => $group->id])->create();

        $payload = [
            'name' => 'teste 2',
        ];

        $response = $this->put(sprintf('%s/%s', self::BASE_URL, $activity->id), $payload);

        $actual = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertEquals($payload, Arr::only($actual, ['name']));
    }

    public function testShouldUpdateIsAdmin()
    {
        $userAdmin = $this->login(TypeUserEnum::ADMIN);
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userAdmin->id])->create();
        $activity = Activity::factory(['group_id' => $group->id])->create();

        $payload = [
            'name' => 'teste 2',
        ];

        $response = $this->put(sprintf('%s/%s', self::BASE_URL, $activity->id), $payload);

        $actual = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertEquals($payload, Arr::only($actual, ['name']));
    }

    public function testShouldNotUpdateWhenActivityNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();

        $payload = [
            'name' => 'teste ajskajska',
        ];

        $response = $this->put(sprintf('api/activity/%s', 100), $payload);

        $response->assertStatus(404);
        $this->assertEquals('Atividade não encontrada', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotUpdateWhenIsNotTheRepresentativeOfGroup()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory(['type_user_id' => $typeUser->id])->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $user1->id])->create();
        $activity = Activity::factory(['group_id' => $group->id])->create();

        $payload = [
            'description' => $this->faker->text,
        ];

        $response = $this->put(sprintf('api/activity/%s', $activity->id), $payload);

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldDeleteIsRepresentative()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();
        $activity = Activity::factory(['group_id' => $group->id])->create();

        $response = $this->delete(sprintf('api/group/%s/activity/%s', $group->id, $activity->id));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('activities', $activity->toArray());
    }

    public function testShouldDeleteIsAdmin()
    {
        $userAdmin = $this->login(TypeUserEnum::ADMIN);
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userAdmin->id])->create();
        $activity = Activity::factory(['group_id' => $group->id])->create();

        $response = $this->delete(sprintf('api/group/%s/activity/%s', $group->id, $activity->id));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('activities', $activity->toArray());
    }

    public function testShouldNotDeleteWhenGroupNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();
        $activity = Activity::factory(['group_id' => $group->id])->create();

        $response = $this->delete(sprintf('api/group/%s/activity/%s', 100, $activity->id));

        $response->assertStatus(404);
        $this->assertEquals('Grupo não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotDeleteWhenActivityNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();

        $response = $this->delete(sprintf('api/group/%s/activity/%s', $group->id, 100));

        $response->assertStatus(404);
        $this->assertEquals('Atividade não encontrada', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotDeleteWhenIsNotTheRepresentativeOfGroup()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory(['type_user_id' => $typeUser->id])->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $user1->id])->create();
        $activity = Activity::factory(['group_id' => $group->id])->create();

        $response = $this->delete(sprintf('api/group/%s/activity/%s', $group->id, $activity->id));

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    private function getJsonStructure(): array
    {
        return [
            'id',
            'name',
            'description',
            'group_id',
            'created_at',
            'updated_at',
        ];
    }
}
