<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(private UserRepository $users) {}

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->users->paginate($perPage);
    }

    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     */
    public function create(array $attributes): User
    {
        return $this->users->create($attributes);
    }

    /**
     * @param  array{name?: string, email?: string, password?: string}  $attributes
     */
    public function update(User $user, array $attributes): User
    {
        return $this->users->update($user, $attributes);
    }

    public function delete(User $user): void
    {
        $this->users->delete($user);
    }
}
