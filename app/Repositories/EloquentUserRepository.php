<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepository
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return User::query()->latest()->paginate($perPage);
    }

    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     */
    public function create(array $attributes): User
    {
        return User::query()->create($attributes);
    }

    /**
     * @param  array{name?: string, email?: string, password?: string}  $attributes
     */
    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
