<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Enums\TypeUserEnum;
use App\Models\Group;
use App\Models\MemberHasGroup;
use App\Models\Representative;
use App\Models\Member;
use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Utils\LoginUsersTrait;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginUsersTrait;

    const BASE_URL = 'api/groups';

    public function testShouldListAll()
    {
        $group = Group::factory()->create();
        $group2 = Group::factory()->create();
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();
        $member = Member::factory()->create();
        MemberHasGroup::factory(['member_id' => $member1->id, 'group_id' => $group->id])->create();
        MemberHasGroup::factory(['member_id' => $member2->id, 'group_id' => $group->id])->create();
        MemberHasGroup::factory(['member_id' => $member->id, 'group_id' => $group2->id])->create();

        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get(sprintf('%s/%s/members', self::BASE_URL, $group->id));

        $response->assertStatus(200);
        $this->assertCount(2, json_decode($response->getContent(), true)['data']);
    }

    public function testShouldListOne()
    {
        $group = Group::factory()->create();
        $member = Member::factory()->create();
        MemberHasGroup::factory(['member_id' => $member->id, 'group_id' => $group->id])->create();

        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get(sprintf('%s/%s/members/%s', self::BASE_URL, $group->id, $member->id));

        $response->assertStatus(200);
        $response->assertJsonStructure($this->getJsonStructure());
    }

    public function testNotShouldListOneWhenNotFoundMember()
    {
        $group = Group::factory()->create();
        $this->login(TypeUserEnum::REPRESENTATIVE);

        $response = $this->get(sprintf('%s/%s/members/%s', self::BASE_URL, $group->id, 100));

        $response->assertStatus(404);
        $this->assertEquals('Membro não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldCreate()
    {
        Mail::fake();

        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();

        $payload = $this->getFakePayload();

        $response = $this->post(sprintf('%s/%s/members', self::BASE_URL, $group->id), $payload);
        Arr::set($payload, '0.entry_date', '2024-01-01');
        Arr::set($payload, '0.departure_date', '2024-01-01');
        Arr::set($payload, '1.entry_date', '2024-01-01');
        Arr::set($payload, '1.departure_date', '2024-01-01');

        $response->assertStatus(201);
        $this->assertDatabaseHas('members', $payload[0]);
        $this->assertDatabaseHas('members', $payload[1]);
    }

    public function testShouldNotCreateWhenGroupNotFound()
    {
        $this->login(TypeUserEnum::REPRESENTATIVE);

        $payload = $this->getFakePayload();

        $response = $this->post(sprintf('%s/%s/members', self::BASE_URL, 100), $payload);

        $response->assertStatus(404);
        $this->assertEquals('Grupo não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotCreateWhenMembersExistsInGroup()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory()->create();
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();
        $member = Member::factory(['user_id' => $user1->id])->create();
        MemberHasGroup::factory(['member_id' => $member->id, 'group_id' => $group->id])->create();

        $payload = $this->getFakePayload();
        $payload[0]['email'] = $member->email;

        $response = $this->post(sprintf('%s/%s/members', self::BASE_URL, $group->id), $payload);
        $actual = json_decode($response->getContent(), true)['errors']['0.email'][0];

        $response->assertStatus(422);
        $this->assertEquals('O campo 0.email já está sendo utilizado.', $actual);
    }

    public function testShouldNotCreateWhenIsNotTheRepresentativeOfGroup()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $userRepresentativeLogged = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory(['type_user_id' => $typeUser->id])->create();
        Representative::factory(['user_id' => $userRepresentativeLogged->id])->create();
        $representative2 = Representative::factory(['user_id' => $user1->id])->create();
        $group = Group::factory(['representative_id' => $representative2->id])->create();

        $payload = [
            [
                'phone'          => '93991167653',
                'role'           => 'professor',
                'entry_date'     => '2023-01-10',
                'departure_date' => '2023-01-11',
                'email'          => 'email@email.com',
            ],
        ];

        $response = $this->post(sprintf('%s/%s/members', self::BASE_URL, $group->id), $payload);

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldCreateWithRegisteredMemberIntoSystem()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();

        $user = User::factory()->create();
        $payload =  [
            [
                'phone'          => '93991167653',
                'role'           => 'professor',
                'entry_date'     => '2023-01-10',
                'departure_date' => '2023-01-11',
                'email'          => $user->email,
            ],
        ];

        Mail::fake();

        $response = $this->post(sprintf('%s/%s/members', self::BASE_URL, $group->id), $payload);

        $member = Member::where(['email' => $user->email])->first();
        $response->assertStatus(201);
        $this->assertEquals($member->user_id, $user->id);
    }

    public function testShouldCreateNotRegisteredMemberIntoSystem()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();

        $payload =  [
            [
                'phone'          => '93991167653',
                'role'           => 'professor',
                'entry_date'     => '2023-01-10',
                'departure_date' => '2023-01-11',
                'email'          => 'teste@teste.com',
            ],
        ];

        Mail::fake();

        $response = $this->post(sprintf('%s/%s/members', self::BASE_URL, $group->id), $payload);

        $member = Member::where(['email' => $payload[0]['email']])->first();
        $response->assertStatus(201);
        $this->assertEquals(null, $member->user_id);
    }

    public function testShouldUpdate()
    {
        Mail::fake();
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();
        $member = Member::factory()->create();

        $payload = [
            'phone' => '9391919191',
        ];

        $response = $this->put(sprintf('%s/%s/members/%s', self::BASE_URL, $group->id, $member->id), $payload);

        $actual = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertEquals($payload['phone'], $actual['phone']);
    }

    public function testShouldNotUpdateWhenMemberNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();

        $payload = [
            'phone' => '9391919191',
        ];

        $response = $this->put(sprintf('%s/%s/members/%s', self::BASE_URL, $group->id, 100), $payload);

        $actual = json_decode($response->getContent(), true);

        $response->assertStatus(404);
        $this->assertEquals('Membro não encontrado', $actual['errors']);
    }

    public function testShouldNotUpdateWhenIsNotTheRepresentativeOfGroup()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $userRepresentativeLogged = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory(['type_user_id' => $typeUser->id])->create();
        Representative::factory(['user_id' => $userRepresentativeLogged->id])->create();
        $representative2 = Representative::factory(['user_id' => $user1->id])->create();
        $group = Group::factory(['representative_id' => $representative2->id])->create();
        $member = Member::factory()->create();

        $payload = [
            'phone' => '9391919191',
        ];

        $response = $this->put(sprintf('%s/%s/members/%s', self::BASE_URL, $group->id, $member->id), $payload);

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldDelete()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();
        $member = Member::factory()->create();
        MemberHasGroup::factory(['member_id' => $member->id, 'group_id' => $group->id])->create();

        $response = $this->delete(sprintf('%s/%s/members/%s', self::BASE_URL, $group->id, $member->id));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('members', $member->toArray());
    }

    public function testShouldNotDeleteWhenGroupNotFound()
    {
        $userRepresentative = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentative->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();
        $member = Member::factory()->create();
        MemberHasGroup::factory(['member_id' => $member->id, 'group_id' => $group->id])->create();

        $response = $this->delete(sprintf('%s/%s/members/%s', self::BASE_URL, 100, $member->id));

        $response->assertStatus(404);
        $this->assertEquals('Grupo não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotDeleteWhenMemberNotFound()
    {
        $userRepresentativeLogged = $this->login(TypeUserEnum::REPRESENTATIVE);
        $representative = Representative::factory(['user_id' => $userRepresentativeLogged->id])->create();
        $group = Group::factory(['representative_id' => $representative->id])->create();
        $user = User::factory()->create();
        $member = Member::factory(['user_id' => $user->id])->create();
        MemberHasGroup::factory(['member_id' => $member->id, 'group_id' => $group->id])->create();

        $response = $this->delete(sprintf('%s/%s/members/%s', self::BASE_URL, $group->id, 100));

        $response->assertStatus(404);
        $this->assertEquals('Membro não encontrado', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotDeleteWhenIsNotTheRepresentativeOfGroup()
    {
        $typeUser = TypeUser::where('name', TypeUserEnum::REPRESENTATIVE)->first();
        $userRepresentativeLogged = $this->login(TypeUserEnum::REPRESENTATIVE);
        $user1 = User::factory(['type_user_id' => $typeUser->id])->create();
        Representative::factory(['user_id' => $userRepresentativeLogged->id])->create();
        $representative2 = Representative::factory(['user_id' => $user1->id])->create();
        $group = Group::factory(['representative_id' => $representative2->id])->create();
        $member = Member::factory()->create();

        $response = $this->delete(sprintf('%s/%s/members/%s', self::BASE_URL, $group->id, $member->id));

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    private function getJsonStructure(): array
    {
        return [
            'data' => [
                'id',
                'email',
                'role',
                'phone',
                'entry_date',
                'departure_date',
                'created_at',
                'updated_at',
                'user',
            ],
        ];
    }

    private function getFakePayload(): array
    {
        return [
            [
                'email'          => 'teste@teste.com',
                'phone'          => '93991167653',
                'role'           => 'professor',
                'entry_date'     => '2024-01-01',
                'departure_date' => '2024-01-01',
            ],
            [
                'email'          => 'teste2@teste.com',
                'phone'          => '93991778765',
                'role'           => 'reitor',
                'entry_date'     => '2024-01-01',
                'departure_date' => '2024-01-01',
            ],
        ];
    }
}
