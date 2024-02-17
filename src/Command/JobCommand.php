<?php

namespace App\Command;

use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'asera:job',
    description: 'Manage user job',
)]
class JobCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('enable-all', null, InputOption::VALUE_NONE, 'Enable all existing job')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('enable-all')) {
            $jobs = $this->entityManager->getRepository(Job::class)->findAll();
            $progressBar = $io->createProgressBar(true);

            foreach ($jobs as $job) {
                $progressBar->advance();
                $job->setIsEnabled(true);
                $this->entityManager->flush();
            }

            $progressBar->finish();
        }

        return Command::SUCCESS;
    }
}
