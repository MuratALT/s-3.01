<?php

namespace App\Command;

use App\Repository\TicketRepository;
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
    name: 'VerifTicketWait',
    description: 'Vérifie les tickets en attente',
)]
class VerifTicketWaitCommand extends Command
{

    private $ticketRepository;
    private $mailer;
    private $userRepository;

    public function __construct(TicketRepository $repository, MailerInterface $mailer, UserRepository $userRepository)
    {
        $this->ticketRepository = $repository;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
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

        $AllTicketsWait = $this->ticketRepository->findAllWaitMoreThanOneDay();
        $AllTicketsWaitToString = "<ul>";

        foreach ($AllTicketsWait as $e) {

            $AllTicketsWaitToString .=  "<li> Ticket n°".$e->getID() . " (" . $e->getUser()->getNom() . ") , </li>";
        }

        $AllTicketsWaitToString .= "</ul>";
        $io = new SymfonyStyle($input, $output);
        $findALLSAV = $this->userRepository->findBySearch(null, "SAV",null,false);

        $nb = count($AllTicketsWait);

        if ($nb > 0) {
            $io->success("Il y a " . $nb . " tickets en attente depuis plus d'un jour");
            foreach ($findALLSAV as $e) {
                $email = (new TemplatedEmail())
                    ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
                    ->to(new Address($e->getEmail(), $e->getNom()))
                    ->subject('Tickets en attente')
                    ->htmlTemplate('emails/ticket_wait.html.twig')
->context([
                        'nb'=>$nb,
                        'user'=>$e,
                        'AllTicketsWaitToString' => $AllTicketsWaitToString,
                    ]);
                $this->mailer->send($email);
            }
        } else {
            $io->success("Il n'y a pas de tickets en attente depuis plus d'un jour");

            foreach ($findALLSAV as $e) {
                $email = (new TemplatedEmail())
                    ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
                    ->to(new Address($e->getEmail(), $e->getNom()))
                    ->subject('Tickets en attente')
                    ->htmlTemplate('emails/ticket_wait_null.html.twig')
                    ->context([
                        'nb' => $nb,
                        'user' => $e
                    ]);
                $this->mailer->send($email);

        }
        }
        return Command::SUCCESS;
    }
}
