<?php

namespace App\Repositories\Contracts;

interface TaskRepositoryInterface
{
    public function getAll(): array;

    public function create(array $data): array;
}