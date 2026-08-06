<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly User $model,
    ) {}

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->model
            ->newQuery()
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id): User
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    public function create(array $attributes): User
    {
        return $this->model->newQuery()->create($attributes);
    }

    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);

        return $user->refresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
