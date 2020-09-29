<?php

declare(strict_types=1);

namespace CfdiAdmin\Account\Command;

use CfdiAdmin\Account\Entity\PasswordHistory;
use CfdiAdmin\Account\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Uid\Uuid;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'cfdiadmin:account:create-user';

    private EntityManagerInterface $entityManager;

    private PasswordEncoderInterface $encoder;

    private string $defaultLocale;

    public function __construct(
        EntityManagerInterface $entityManager,
        EncoderFactoryInterface $encoderFactory,
        string $defaultLocale
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->encoder = $encoderFactory->getEncoder(User::class);
        $this->defaultLocale = $defaultLocale;
    }

    public function configure(): void
    {
        $this->setDescription('Creates a new valid user');
        $this->addArgument('email', InputArgument::REQUIRED, 'email');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $email */
        $email = $input->getArgument('email');
        /** @var string $password */
        $password = $input->getArgument('password');

        $user = new User(
            Uuid::v4(),
            $email,
            $this->encoder->encodePassword($password, null),
            $this->defaultLocale,
            true
        );

        $passwordHistory = new PasswordHistory(
            Uuid::v4(),
            $user,
            $user->getPassword()
        );

        $this->entityManager->persist($user);
        $this->entityManager->persist($passwordHistory);
        $this->entityManager->flush();

        $output->writeln('<info>User created.</info>');

        return 0;
    }
}
