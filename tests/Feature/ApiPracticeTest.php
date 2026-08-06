<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiPracticeTest extends TestCase
{
    public function test_health_endpoint_returns_json(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('service', 'Backend Lab');
    }

    public function test_greet_endpoint_validates_and_returns_a_message(): void
    {
        $this->postJson('/api/greet', ['name' => '小王'])
            ->assertCreated()
            ->assertJsonPath('message', '你好，小王！欢迎学习 Laravel。');

        $this->postJson('/api/greet', ['name' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }
}
