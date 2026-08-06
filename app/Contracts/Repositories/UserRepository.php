<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepository
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $perPage): LengthAwarePaginator;

    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     */
    public function create(array $attributes): User;

    /**
     * @param  array{name?: string, email?: string, password?: string}  $attributes
     */
    public function update(User $user, array $attributes): User;

    public function delete(User $user): void;
}
