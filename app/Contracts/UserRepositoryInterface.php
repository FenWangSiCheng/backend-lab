<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginate(int $perPage): LengthAwarePaginator;

    public function findOrFail(int $id): User;

    public function create(array $attributes): User;

    public function update(User $user, array $attributes): User;

    public function delete(User $user): void;
}
