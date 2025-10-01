<?php

namespace App\Application\Query\SupportCase\ReadSupportCase;

use App\Application\Bus\Query\QueryHandler;
use App\Application\Query\SupportCase\SupportCaseResponse;
use App\Domain\Exception\FormSupport\SupportCaseNotFoundException;
use App\Domain\Repository\SupportCase\SupportCaseRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ReadSupportCaseHandler implements QueryHandler
{
    public function __construct(
        private SupportCaseRepository $formRepository
    )
    {
    }

    public function __invoke(ReadSupportCase $readForm): SupportCaseResponse
    {
        $form = $this->formRepository->read($readForm->id);

        if ($form === null) {
            throw new SupportCaseNotFoundException();
        }

        return (new SupportCaseResponse())->fromEntity($form);
    }
}
