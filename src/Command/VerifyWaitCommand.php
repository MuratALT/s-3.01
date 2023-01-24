<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsCommand(
    name: 'VerifyWait',
    description: 'Vérifie les utilisateurs en attente de validation',
)]
class VerifyWaitCommand extends Command
{

    private $userRepository;
    private $mailer;

    public function __construct(UserRepository $repository, MailerInterface $mailer)
    {
        $this->userRepository = $repository;
        $this->mailer = $mailer;
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

        $AllUsersWait = $this->userRepository->findAllWaitMoreThanOneDay();
        $AllUsersWaitToString = "<ul>";

        // Pour chaque utilisateur en attente de validation depuis plus d'un jour
        // On écrit sous la forme : NOM Prénom (email) , \n



        foreach ($AllUsersWait as $e) {

            $AllUsersWaitToString .=  "<li>".$e->getNom() . " " . $e->getPrenom() . " (" . $e->getEmail() . ") , </li>";
        }
        $AllUsersWaitToString .= "</ul>";
        $io = new SymfonyStyle($input, $output);


        $AllAdmin = $this->userRepository->findAllAdmin();

        // On envoie un seul mail contenant tous les utilisateurs en attente de validation

        $nb = count($AllUsersWait);
        if( $nb > 0){
            $io->success('Les utilisateurs en attente de validation depuis plus d\'un jour sont : ' . $AllUsersWaitToString);
            foreach ($AllAdmin as $admin) {
                $email = (new TemplatedEmail())
                    ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
                    ->to($admin->getEmail())
                    ->subject('Utilisateurs en attente de validation')
                    ->htmlTemplate('mail/verifyWait.html.twig')
                    ->context([
                        'AllUsersWait' => $AllUsersWaitToString,
                        'user' => $admin,
                        'nb'=> count($AllUsersWait)
                    ]);

                $this->mailer->send($email);
            }
        }
        else
        {
            foreach ($AllAdmin as $admin) {
                $email = (new TemplatedEmail())
                    ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
                    ->to($admin->getEmail())
                    ->subject('Utilisateurs en attente de validation')
                    ->htmlTemplate('mail/verifyWaitNull.html.twig')
                    ->context([
                        'user' => $admin,
                        'nb'=> count($AllUsersWait)
                    ]);

                $this->mailer->send($email);
            }
        }







        if($nb > 0){
            $io->success('Il y a ' . $nb . ' utilisateur(s) en attente de validation depuis plus d\'un jour');
        } else {
            $io->success('Aucun utilisateur en attente de validation');
        }


        return Command::SUCCESS;
    }
}
