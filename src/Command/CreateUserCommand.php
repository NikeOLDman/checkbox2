<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[AsCommand(
    name: 'app:create-user',
    description: 'Создание нового пользователя.',
    hidden: false,
    aliases: ['app:add-user']
)]
class CreateUserCommand extends Command
{

    private $userPasswordHasher;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a user...');
        $this
            // configure an argument
            ->addArgument('login', InputArgument::REQUIRED, 'User LOGIN')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addArgument('name', InputArgument::REQUIRED, 'User FIO');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Создаватель челавекафф',
            '======================',
            '',
        ]);

        $date = new DateTime();
        $user = new User();
        $user->setUsername($input->getArgument('login'));
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $input->getArgument('password')
            )
        );
        $user->setUname($input->getArgument('name'));
        $user->setCreatetime($date);
        $user->setDeleted(false);
        $user->setRoles(['ROLE_SUPERUSER']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln([
            'Челавек ' . $input->getArgument('login'),
            'С удивительным именем ' . $input->getArgument('name'),
            'УСПЕШНО СОЗДАН!'
        ]);
        return Command::SUCCESS;
    }
}
