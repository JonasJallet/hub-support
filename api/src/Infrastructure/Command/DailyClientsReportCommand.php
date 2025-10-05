<?php
namespace App\Infrastructure\Command;

use App\Infrastructure\Persistence\Repository\DoctrineUserRepository;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand(
    name: 'app:check-daily-clients',
    description: 'Vérifie le nombre de clients connectés aujourd\'hui',
)]
#[AsCronTask('0 0 * * *', schedule: 'default')]
class DailyClientsReportCommand extends Command
{
    public function __construct(
        private readonly DoctrineUserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Vérification du nombre de clients connectés');
        $date = (new DateTime())->format('Y-m-d');

        $connectedClientsCount = $this->userRepository->countUsersLoggedToday();

        $io->info("Date : $date");
        $io->success("Nombre de clients connectés aujourd'hui : $connectedClientsCount");

        return Command::SUCCESS;
    }
}
