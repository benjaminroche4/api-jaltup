<?php

namespace App\Command;

use App\Enum\PublicationStatus;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-expired-offers',
    description: 'Update the status of the expired offers.',
)]
class UpdateExpiredOffersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OfferRepository $offerRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $expiredOffers = $this->offerRepository->findExpiredOffers();

        foreach ($expiredOffers as $offer) {
            $offer->setStatus(PublicationStatus::Expired->value);
        }

        $this->entityManager->flush();
        $io->success(count($expiredOffers).' offers were updated to "Expired".');

        return Command::SUCCESS;
    }
}
