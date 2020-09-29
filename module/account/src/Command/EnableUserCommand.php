<?php

declare(strict_types=1);

namespace CfdiAdmin\Account\Command;

use CfdiAdmin\Account\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnableUserCommand extends Command
{
    protected static $defaultName = 'cfdiadmin:account:enable-user';

    private EntityManagerInterface $entityManager;

    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function configure(): void
    {
        $this->setDescription('Enables a given user');
        $this->addArgument('email', InputArgument::REQUIRED, 'email');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $email */
        $email = $input->getArgument('email');

        $user = $this->userRepository->findByEmail($email);

        if (null === $user) {
            $output->writeln('<error>User not found.</error>');

            return 1;
        }

        $user->setEnabled(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('<info>User enabled.</info>');

        return 0;
    }
}
