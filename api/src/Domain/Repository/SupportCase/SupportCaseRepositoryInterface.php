<?php

namespace App\Domain\Repository\SupportCase;

use App\Domain\Entity\SupportCase;

interface SupportCaseRepositoryInterface
{
    public function save(SupportCase $form, bool $newEntity): void;

    public function delete(SupportCase $form): void;

    public function findById(int $id): ?SupportCase;

    public function browse(): array;
}
