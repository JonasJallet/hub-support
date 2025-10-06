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
    public function __invoke(AddSupportCase $addSupportCase): void
    {
        $supportCase = $addSupportCase->toEntity();
        $this->supportCaseRepository->add($supportCase);
        $userEmail = $this->security->getUser()->getUserIdentifier();

        $email = (new TemplatedEmail())
            ->from($userEmail)
            ->to('hub-support@mail.com')
            ->subject('Nouveau ticket de support')
            ->htmlTemplate('emails/support_case_email.html.twig')
            ->context([
                'support_case' => $addSupportCase,
                'user_email' => $userEmail,
            ]);

        if ($addSupportCase->file !== null) {
            $email->attach(
                file_get_contents($addSupportCase->file->getPathname()),
                $addSupportCase->file->getClientOriginalName(),
                $addSupportCase->file->getMimeType()
            );
        }

        $this->mailer->send($email);
        $addSupportCase->id = $supportCase->getId();
    }
}
