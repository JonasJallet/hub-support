<?php

namespace App\Application\Command\SupportCase\AddSupportCase;

use App\Application\Bus\Command\CommandHandler;
use App\Domain\Repository\SupportCase\SupportCaseRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AddSupportCaseHandler implements CommandHandler
{
    public function __construct(
        private SupportCaseRepository $supportCaseRepository,
        private MailerInterface       $mailer,
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(AddSupportCase $addForm): void
    {
        $form = $addForm->toEntity();

        if ($addForm->file !== null) {
            $form->setFile(file_get_contents($addForm->file->getPathname()));
        }

        $this->supportCaseRepository->add($form);
        $addForm->id = $form->getId();

        $email = (new TemplatedEmail())
            ->from('noreply@tondomaine.com')
            ->to('suppport@tondomaine.com')
            ->subject('Nouveau ticket de support soumis')
            ->htmlTemplate('emails/form_submitted.html.twig')
            ->context([
                'form' => $form,
                'file' => $addForm->file,
            ]);

        $this->mailer->send($email);
    }
}
