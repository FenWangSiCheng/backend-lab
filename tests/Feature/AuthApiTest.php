<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_a_user_can_login_and_access_their_profile(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $loginResponse = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
            'device_name' => 'PHPUnit',
        ])
            ->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonStructure(['token']);

        $this->withToken($loginResponse->json('token'))
            ->getJson(route('me'))
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_login_rejects_invalid_credentials_and_input(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
            'device_name' => 'PHPUnit',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->postJson(route('login'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password', 'device_name']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_revokes_only_the_current_access_token(): void
    {
        $user = User::factory()->create();
        $currentToken = $user->createToken('current')->plainTextToken;
        $otherToken = $user->createToken('other')->plainTextToken;

        $this->withToken($currentToken)
            ->postJson(route('logout'))
            ->assertNoContent();

        Auth::forgetGuards();

        $this->withToken($currentToken)
            ->getJson(route('me'))
            ->assertUnauthorized();

        Auth::forgetGuards();

        $this->withToken($otherToken)
            ->getJson(route('me'))
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_guests_cannot_access_protected_routes(): void
    {
        $user = User::factory()->create();

        $this->getJson(route('me'))->assertUnauthorized();
        $this->getJson(route('users.show', $user))->assertUnauthorized();
        $this->patchJson(route('users.update', $user), [])->assertUnauthorized();
        $this->deleteJson(route('users.destroy', $user))->assertUnauthorized();
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        $payload = [
            'email' => 'missing@example.com',
            'password' => 'incorrect-password',
            'device_name' => 'PHPUnit',
        ];

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10']);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson(route('login'), $payload)->assertUnprocessable();
        }

        $this->postJson(route('login'), $payload)->assertTooManyRequests();
    }
}
