<?php

namespace Tests\Feature;

use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use App\Repositories\EloquentUserRepository;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_resolves_the_user_repository_contract(): void
    {
        $this->assertInstanceOf(
            EloquentUserRepository::class,
            $this->app->make(UserRepository::class),
        );
    }

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

        Sanctum::actingAs($user);

        $this->getJson(route('users.show', $user))
            ->assertOk()
            ->assertJsonPath('data.email', 'zhangsan@example.com');
    }

    public function test_a_user_can_update_and_delete_their_own_account(): void
    {
        $user = User::factory()->create();
        $user->createToken('account-token');

        Sanctum::actingAs($user);

        $this->patchJson(route('users.update', $user), [
            'name' => '新名字',
            'email' => $user->email,
        ])
            ->assertOk()
            ->assertJsonPath('data.name', '新名字');

        $this->deleteJson(route('users.destroy', $user))
            ->assertNoContent();

        $this->assertModelMissing($user);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_it_validates_registration_and_returns_not_found(): void
    {
        $this->postJson(route('users.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson(route('users.show', 999))
            ->assertNotFound();
    }

    public function test_a_user_cannot_access_other_users_or_list_users(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson(route('users.index'))->assertForbidden();
        $this->getJson(route('users.show', $otherUser))->assertForbidden();
        $this->patchJson(route('users.update', $otherUser), [
            'name' => '越权修改',
        ])->assertForbidden();
        $this->deleteJson(route('users.destroy', $otherUser))->assertForbidden();

        $this->assertSame($otherUser->name, $otherUser->fresh()->name);
        $this->assertModelExists($otherUser);
    }
}
