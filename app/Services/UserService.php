<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->userRepository->paginate($perPage);
    }

    public function find(int $id): User
    {
        return $this->userRepository->findOrFail($id);
    }

    public function create(array $attributes): User
    {
        return $this->userRepository->create($attributes);
    }

    public function update(int $id, array $attributes): User
    {
        $user = $this->find($id);

        return $this->userRepository->update($user, $attributes);
    }

    public function delete(int $id): void
    {
        $user = $this->find($id);

        $this->userRepository->delete($user);
    }
}
