<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_and_reads_a_user(): void
    {
        $payload = [
            'name' => '张三',
            'email' => 'zhangsan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $createResponse = $this->postJson('/api/users', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', '张三')
            ->assertJsonPath('data.email', 'zhangsan@example.com')
            ->assertJsonMissingPath('data.password');

        $this->getJson('/api/users/'.$createResponse->json('data.id'))
            ->assertOk()
            ->assertJsonPath('data.email', 'zhangsan@example.com');
    }

    public function test_it_lists_updates_and_deletes_users(): void
    {
        $user = User::factory()->create();

        $this->getJson('/api/users')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->patchJson('/api/users/'.$user->id, ['name' => '新名字'])
            ->assertOk()
            ->assertJsonPath('data.name', '新名字');

        $this->deleteJson('/api/users/'.$user->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_it_validates_user_input_and_returns_not_found(): void
    {
        $this->postJson('/api/users', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);

        $this->getJson('/api/users/999')
            ->assertNotFound();
    }
}
