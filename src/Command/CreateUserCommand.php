<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'asera:user',
    description: 'Manage Asera User command',
)]
class CreateUserCommand extends Command
{
    public function __construct(public UserPasswordHasherInterface $passwordHasher, public EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('create', null, InputOption::VALUE_NONE, 'Create user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if ($input->getOption('create')) {
            $username = $io->askQuestion(new Question('Username :'));
            $password = $io->askQuestion(new Question('Password :'));
            $isAdmin = $io->askQuestion(new Question('Is admin ?', '[yes]'));

            $user = new User();
            $user->setUsername($username)->setLastname('test');
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setRoles($isAdmin ? ['ROLE_ADMIN'] : ['ROLE_USER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $io->success("User $username created successfully !");
        }

        return Command::SUCCESS;
    }
}
