<?php

declare(strict_types=1);

namespace App\Domain\Repository\SupportCase;

use App\Domain\Entity\SupportCase;

readonly class SupportCaseRepository
{
    public function __construct(
        private SupportCaseRepositoryInterface $supportCaseRepository,
    ) {
    }

    public function browse(): array
    {
        return $this->supportCaseRepository->browse();
    }

    public function read(int $id): ?SupportCase
    {
        return $this->supportCaseRepository->findById($id);
    }

    public function edit(SupportCase $form): void
    {
        $this->supportCaseRepository->save($form, false);
    }

    public function add(SupportCase $form): void
    {
        $this->supportCaseRepository->save($form, true);
    }

    public function delete(SupportCase $form): void
    {
        $this->supportCaseRepository->delete($form);
    }
}
