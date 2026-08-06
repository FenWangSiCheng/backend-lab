<?php

namespace App\Services;

class GreetingService
{
    public function greet(string $name): string
    {
        return "你好，{$name}！欢迎学习 Laravel。";
    }
}
