<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Enums\TypeUserEnum;
use App\Models\Group;
use App\Models\GroupHasRepresentative;
use App\Models\Member;
use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\Feature\Utils\LoginUsersTrait;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginUsersTrait;

    public function testShouldListAll()
    {
        $group = Group::factory()->create();
        $user = User::factory()->create();
        Member::factory(['group_id' => $group->id])->create();
        Member::factory(['user_id' => $user->id, 'group_id' => $group->id])->create();

        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get('api/members');

        $response->assertStatus(200);
        $this->assertCount(2, json_decode($response->getContent(), true)['data']);
    }

    public function testShouldListOne()
    {
        $group = Group::factory()->create();
        $member = Member::factory(['group_id' => $group->id])->create();
        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get(sprintf('api/members/%s', $member->id));

        $response->assertStatus(200);
        $response->assertJsonStructure($this->getJsonStructure());
    }

    public function testNotShouldListOneWhenNotFoundMember()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get(sprintf('api/members/%s', 100));

        $response->assertStatus(404);
        $this->assertEquals('Membro não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldCreate()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();

        $payload = [
            [
                'phone' => '93991167653',
                'role' => 'professor',
                'entry_date' => '01-10-2023',
                'departure_date' => '01-11-2023',
                'user_id' => (string) $user1->id
            ],
            [
                'phone' => '93991778765',
                'role' => 'reitor',
                'entry_date' => '01-10-2023',
                'departure_date' => '01-11-2023',
                'user_id' => (string) $user2->id
            ]
        ];

        $response = $this->post(sprintf('/api/group/%s/members', $group->id), $payload);

        Arr::set($payload, '0.entry_date', '2023-10-01');
        Arr::set($payload, '0.departure_date', '2023-11-01"');
        Arr::set($payload, '1.entry_date', '2023-10-01');
        Arr::set($payload, '1.departure_date', '2023-11-01"');

        $response->assertStatus(201);
        $this->assertDatabaseHas('members', array_merge($payload[0], ['group_id' => $group->id]));
        $this->assertDatabaseHas('members', array_merge($payload[1], ['group_id' => $group->id]));
    }

    public function testShouldNotCreateWhenGroupNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();

        $payload = [
            [
                'phone' => '93991167653',
                'role' => 'professor',
                'entry_date' => '01-10-2023',
                'departure_date' => '01-11-2023',
                'user_id' => (string) $user1->id
            ],
            [
                'phone' => '93991778765',
                'role' => 'reitor',
                'entry_date' => '01-10-2023',
                'departure_date' => '01-11-2023',
                'user_id' => (string) $user2->id
            ]
        ];

        $response = $this->post(sprintf('/api/group/%s/members', 100), $payload);

        $response->assertStatus(404);
        $this->assertEquals('Grupo não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotCreateWhenMembersExistsInGroup()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();
        Member::factory(['user_id' => $user1->id, 'group_id' => $group->id])->create();

        $payload = [
            [
                'phone' => '93991167653',
                'role' => 'professor',
                'entry_date' => '01-10-2023',
                'departure_date' => '01-11-2023',
                'user_id' => (string) $user1->id
            ],
            [
                'phone' => '93991778765',
                'role' => 'reitor',
                'entry_date' => '01-10-2023',
                'departure_date' => '01-11-2023',
                'user_id' => (string) $user2->id
            ]
        ];

        $response = $this->post(sprintf('/api/group/%s/members', $group->id), $payload);

        $response->assertStatus(400);
        $this->assertEquals('Membro ja cadastrado no grupo', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotCreateWhenIsNotTheRepresentativeOfGroup()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory(['type_user_id' => $typeUser->id])->create();
        $user2 = User::factory()->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $user1->id])->create();

        $payload = [
            [
                'phone' => '93991167653',
                'role' => 'professor',
                'entry_date' => '01-10-2023',
                'departure_date' => '01-11-2023',
                'user_id' => (string) $user2->id
            ],
        ];

        $response = $this->post(sprintf('/api/group/%s/members', $group->id), $payload);

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldUpdate()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory()->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();
        $member = Member::factory(['user_id' => $user1->id, 'group_id' => $group->id])->create();

        $payload = [
            'phone' => '9391919191',
        ];

        $response = $this->put(sprintf('api/members/%s', $member->id), $payload);

        $actual = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertEquals($payload['phone'], $actual['phone']);
    }

    public function testShouldNotUpdateWhenMemberNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory()->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();
        $member = Member::factory(['user_id' => $user1->id, 'group_id' => $group->id])->create();

        $payload = [
            'phone' => '9391919191',
        ];

        $response = $this->put(sprintf('api/members/%s', 100), $payload);

        $actual = json_decode($response->getContent(), true);

        $response->assertStatus(404);
        $this->assertEquals('Membro não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotUpdateWhenIsNotTheRepresentativeOfGroup()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory(['type_user_id' => $typeUser->id])->create();
        $user2 = User::factory()->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $user1->id])->create();
        $member = Member::factory(['user_id' => $user2->id, 'group_id' => $group->id])->create();

        $payload = [
            'phone' => '9391919191',
        ];

        $response = $this->put(sprintf('api/members/%s', $member->id), $payload);

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldDelete()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory()->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();
        $member = Member::factory(['user_id' => $user1->id, 'group_id' => $group->id])->create();

        $response = $this->delete(sprintf('api/group/%s/members/%s', $group->id,$member->id));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('members', $member->toArray());
    }

    public function testShouldNotDeleteWhenGroupNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory()->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();
        $member = Member::factory(['user_id' => $user1->id, 'group_id' => $group->id])->create();

        $response = $this->delete(sprintf('api/group/%s/members/%s', 100, $member->id));

        $response->assertStatus(404);
        $this->assertEquals('Grupo não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotDeleteWhenMemberNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory()->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $userRepresentative->id])->create();
        $member = Member::factory(['user_id' => $user1->id, 'group_id' => $group->id])->create();

        $response = $this->delete(sprintf('api/group/%s/members/%s', $group->id, 100));

        $response->assertStatus(404);
        $this->assertEquals('Membro não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotDeleteWhenIsNotTheRepresentativeOfGroup()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory(['type_user_id' => $typeUser->id])->create();
        $user2 = User::factory()->create();
        $group = Group::factory()->create();
        GroupHasRepresentative::factory(['group_id' => $group->id, 'user_id' => $user1->id])->create();
        $member = Member::factory(['user_id' => $user2->id, 'group_id' => $group->id])->create();

        $response = $this->delete(sprintf('api/group/%s/members/%s', $group->id, $member->id));

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    private function getJsonStructure(): array
    {
        return [
            'data' => [
                'id',
                'role',
                'phone',
                'entry_date',
                'departure_date',
                'created_at',
                'updated_at',
                'group_id',
                'user'
            ],
        ];
    }
}
