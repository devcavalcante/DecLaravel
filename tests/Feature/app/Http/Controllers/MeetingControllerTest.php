<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Models\Meeting;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\Feature\Utils\LoginUsersTrait;
use Tests\TestCase;

class MeetingControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginUsersTrait;

    public function testShouldListAll()
    {
        $group = Group::factory()->create();
        $meeting = Meeting::factory(['group_id' => $group->id])->create();

        $this->login();

        $response = $this->get('api/group/{$group->id}/meeting-history');

        $response->assertStatus(200);
        $this->assertCount(1, json_decode($response->getContent(), true)['data']);
    }

    public function testShouldListOne()
    {
        $group = Group::factory()->create();
        $meeting = Meeting::factory(['group_id' => $group->id])->create();

        $this->login();

        $response = $this->get(sprintf('api/group/{$group->id}/meeting-history/%s', $meeting->id));

        $response->assertStatus(200);
        $response->assertJsonStructure($this->getJsonStructure());
    }

    public function testNotShouldListOneWhenNotFoundMeeting()
    {
        $this->login();

        $response = $this->get(sprintf('api/group/{$group->id}/meeting-history/%s', 100));

        $response->assertStatus(404);
        $this->assertEquals('Reunião não encontrada', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldCreate()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $payload = [
            'content'  => 'Esta é uma reunião de teste',
            'summary'  => 'Este é um resumo da reunião de teste',
            'ata'      => 'Esta é a ata da reunião de teste',
            'date'     => now(),
            'group_id' => $group->id,
        ];

        $this->login($user);

        $response = $this->post('api/group/{$group->id}/meeting-history', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('meetings', $payload);
    }

    public function testShouldNotCreateWhenIsNotTheRepresentativeOfGroup()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $payload = [
            'content'  => 'Esta é uma reunião de teste',
            'summary'  => 'Este é um resumo da reunião de teste',
            'ata'      => 'Esta é a ata da reunião de teste',
            'date'     => now(),
            'group_id' => $group->id,
        ];

        $this->login($user);

        $response = $this->post('api/group/{$group->id}/meeting-history', $payload);

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldUpdate()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $meeting = Meeting::factory(['group_id' => $group->id])->create();

        $payload = [
            'content' => 'Esta é uma reunião de teste atualizada',
        ];

        $this->login($user);

        $response = $this->put(sprintf('api/group/{$group->id}/meeting-history/%s', $meeting->id), $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('meetings', Arr::merge($payload, ['id' => $meeting->id]));
    }


    public function testShouldNotDeleteWhenIsNotTheRepresentativeOfGroup()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $meeting = Meeting::factory(['group_id' => $group->id])->create();

        $this->login($user);

        $response = $this->delete(sprintf('api/group/{$group->id}/meeting-history/%s', $meeting->id));

        $response->assertStatus(403);
        $this->assertEquals('This action is unauthorized.', json_decode($response->getContent(), true)['errors']);
    }

    public function testShouldNotDeleteWhenMeetingNotFound()
    {
        $user = User::factory()->create();

        $response = $this->delete(sprintf('api/group/{$group->id}/meeting-history/%s', 100));

        $response->assertStatus(404);
        $this->assertEquals('Reunião não encontrada', json_decode($response->getContent(), true)['errors']);
    }
}
