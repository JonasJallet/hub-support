<?php

namespace App\Application\Command\SupportCase\AddSupportCase;

use App\Application\Bus\Command\CommandHandler;
use App\Domain\Repository\SupportCase\SupportCaseRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Bundle\SecurityBundle\Security;

#[AsMessageHandler]
final readonly class AddSupportCaseHandler implements CommandHandler
{
    public function __construct(
        private SupportCaseRepository $supportCaseRepository,
        private MailerInterface       $mailer,
        private Security              $security,
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

        $userEmail = $this->security->getUser()->getUserIdentifier();

        $this->supportCaseRepository->add($form);
        $addForm->id = $form->getId();

        $email = (new TemplatedEmail())
            ->from($userEmail)
            ->to('hub-support@mail.com')
            ->subject('Nouveau ticket de support')
            ->htmlTemplate('emails/form_submitted.html.twig')
            ->context([
                'form' => $form,
                'user_email' => $userEmail,
            ]);

        if ($addForm->file !== null) {
            $email->attachFromPath(
                $addForm->file->getPathname(),
                $addForm->file->getClientOriginalName(),
                $addForm->file->getMimeType()
            );
        }

        $this->mailer->send($email);
    }
}
