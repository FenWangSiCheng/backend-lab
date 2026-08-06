<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_creates_and_reads_a_user(): void
    {
        $payload = [
            'name' => '张三',
            'email' => 'zhangsan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $createResponse = $this->postJson(route('users.store'), $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', '张三')
            ->assertJsonPath('data.email', 'zhangsan@example.com')
            ->assertJsonMissingPath('data.password');

        $user = User::query()->findOrFail($createResponse->json('data.id'));

        $this->assertTrue(Hash::check('password123', $user->password));

        $this->getJson(route('users.show', $user))
            ->assertOk()
            ->assertJsonPath('data.email', 'zhangsan@example.com');
    }

    public function test_it_lists_updates_and_deletes_users(): void
    {
        $user = User::factory()->create();

        $this->getJson(route('users.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->patchJson(route('users.update', $user), [
            'name' => '新名字',
            'email' => $user->email,
        ])
            ->assertOk()
            ->assertJsonPath('data.name', '新名字');

        $this->deleteJson(route('users.destroy', $user))
            ->assertNoContent();

        $this->assertModelMissing($user);
    }

    public function test_it_validates_user_input_and_returns_not_found(): void
    {
        $this->postJson(route('users.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);

        $this->getJson(route('users.show', 999))
            ->assertNotFound();
    }

    public function test_it_validates_and_applies_the_page_size(): void
    {
        User::factory()->count(3)->create();

        $this->getJson(route('users.index', ['per_page' => 2]))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.per_page', 2);

        foreach ([0, 101, 'many'] as $invalidPageSize) {
            $this->getJson(route('users.index', ['per_page' => $invalidPageSize]))
                ->assertUnprocessable()
                ->assertJsonValidationErrors('per_page');
        }
    }
}
