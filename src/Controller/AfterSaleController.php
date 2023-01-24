<?php

namespace App\Controller;

use App\Entity\Piece;
use App\Entity\User;
use App\Repository\PieceRepository;
use App\Entity\Ticket;
use App\Repository\TicketRepository;
use App\Form\TicketFormType;
use App\Form\ImportType;
use App\Form\EmployeeFormType;
use App\Repository\UserRepository;
use App\Form\PieceFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class AfterSaleController extends AbstractController
{
    public function __construct(TranslatorInterface $translator, Security $security)
    {
        $this->translator = $translator;
        $this->security = $security;
    }
    
    #[Route('/piece', name: 'app_piece_index', methods: ['GET'])]
    public function indexPiece(PieceRepository $pieceRepository, PaginatorInterface $paginator, Request $request): Response
    {

        // Seulement le serive après vente peut accéder à cette page
        if ($this->getUser()->getService()->getLibelle() != "Service Après Vente")
        {
            throw $this->createAccessDeniedException();
        }
        $donnees = $pieceRepository->findAll();
        
        $piece = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),15
        );

        return $this->render('spare_parts/index.html.twig', [
            'pieces' => $piece,
        ]);
    }

    #[Route('/piece/nouveau', name: 'app_piece_new', methods: ['GET', 'POST'])]
    public function newpiece(Piece $piece = null , TranslatorInterface $translator , Request $request, PieceRepository $pieceRepository, EntityManagerInterface $em): Response
    {
        if ($this->getUser()->getService()->getLibelle() != "Service Après Vente")
        {
            throw $this->createAccessDeniedException();
        }

        if(!$piece){
            $piece = new Piece();
        }

        $form = $this->createForm(PieceFormType::class, $piece);
        $form->handleRequest($request);

   if($form->isSubmitted() && $form->isValid())
   {
        if($pieceRepository->findOneBy(['libelle' => $piece->getLibelle()]) != null && $piece->getId() == null){
            $this->addFlash('error',  $translator->trans("Le libellé existe déjà !"));
        }
        else
        {
                $this->addFlash('success',  $translator->trans("La pièce détachée a bien été créée !"));
                $em->persist($piece);
                $em->flush();
                return $this->redirectToRoute("app_piece_index");                           
        }
    }

        return $this->render('spare_parts/new.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/piece/{id}/edit', name: 'app_piece_edit', methods: ['GET', 'POST'])]   
    public function editUser(Request $request, Piece $piece, PieceRepository $pieceRepository, EntityManagerInterface $em): Response {

        $form = $this->createForm(PieceFormType::class, $piece);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($piece);
            $em->flush();

            $this->addFlash('success', 'Les modifications ont bien été enregistrées');

            return $this->redirectToRoute('app_piece_index', ['id' => $piece->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('spare_parts/edit.html.twig', [
            'piece' => $piece,
            'PieceForm' => $form->createView(),
        ]);
    }



    #[Route('/piece/{id}/delete', name: 'app_piece_delete', methods: ['GET', 'POST'])]

    public function deletepiece(Piece $piece, EntityManagerInterface $em, TranslatorInterface $translator): Response {
        if ($this->getUser()->getService()->getLibelle() != "Service Après Vente")
        {
            throw $this->createAccessDeniedException();
        }
        $em->remove($piece);
        $em->flush();
        $this->addFlash('success', $translator->trans("La pièce détachée a bien été supprimée !"));
        return $this->redirectToRoute('app_piece_index');
    }

    /**
     * @Route("/import/dl/piece", name="part_model")
     */

     public function partModel()
     {
         if ($this->getUser()->getService()->getLibelle() != "Service Après Vente")
         {
             throw $this->createAccessDeniedException();
         }
         
         $spreadsheet = new Spreadsheet();
 
         $sheet = $spreadsheet->getActiveSheet();
         $sheet->setCellValue('A1', 'Libelle');
         // Définition de la largeur de la colonne
         $sheet->getColumnDimension('A')->setWidth(20);
         // Définition de la hauteur de la ligne
         $sheet->getRowDimension('1')->setRowHeight(30);
         $sheet->setCellValue('B1', 'Hauteur');
         $sheet->getColumnDimension('B')->setWidth(30);
         $sheet->getRowDimension('1')->setRowHeight(30);
         $sheet->setCellValue('C1', 'Poids');
         $sheet->getColumnDimension('C')->setWidth(50);
         $sheet->getRowDimension('1')->setRowHeight(30);
         $sheet->setCellValue('D1', 'Longueur');
         $sheet->getColumnDimension('D')->setWidth(50);
         $sheet->getRowDimension('1')->setRowHeight(30);
         $sheet->setCellValue('E1', 'Profondeur');
         $sheet->getColumnDimension('E')->setWidth(50);
         $sheet->getRowDimension('1')->setRowHeight(30);

         $protection = $sheet->getProtection();
 
         // Garder la majuscule pour les noms de colonnes
         $sheet->getStyle('A1:E1')->getFont()->setBold(true);
 
         $writer = new Xlsx($spreadsheet);
         $filename = 'model_import' . date('d-m-Y_H-i-s') . '.xlsx';
         $writer->save(__DIR__ . '/../../public/export/Model/' . $filename);
         $response = new BinaryFileResponse(__DIR__ . '/../../public/export/Model/' . $filename);
         $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
         // Suppression du fichier après téléchargement
         $response->deleteFileAfterSend(true);
         return $response;
 
     }

     #[Route('/piece/import/fichier', name: 'app_piece_import')]
     public function importParts(TranslatorInterface $translator, Request $request, PieceRepository $pieceRepository, EntityManagerInterface $em): Response
     {
         if ($this->getUser()->getService()->getLibelle() != "Service Après Vente")
         {
             throw $this->createAccessDeniedException();
         }
         $form = $this->createForm(ImportType::class);
         $form->handleRequest($request);
 
         if ($form->isSubmitted() && $form->isValid()) {
             $file = $form->get('file')->getData();
             
             // On ne stocke pas le fichier sur le serveur
 
             // On récupère le contenu du fichier
 
             $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
             $spreadsheet = $reader->load($file);
             $sheetData = $spreadsheet->getActiveSheet()->toArray();
 
             // On boucle sur les lignes du fichier
 
             $contenu = '';
             $i = 0;
 
            $headers = $sheetData[0];
 
 
             if ($headers !== ['Libelle', 'Hauteur', 'Poids', 'Longueur', 'Profondeur']) {
                 $this->addFlash('error', $translator->trans('Le fichier XLSX est invalide, Il manque des colonnes').'.');
                 return $this->redirectToRoute('app_piece_import');
             }
 
 
             $content = array_slice($sheetData, 1);
 
             $XLSX = array();
             foreach ($content as $row) {
                 $XLSX[] = array_combine($headers, $row);
             }
 
             $errors = "";
 
             // On boucle sur les lignes du CSV pour vérifier qu'aucun champ n'est vide
             foreach ($XLSX as $data) {
                 if (
                     (!((empty($data['Libelle']) &&
                         empty($data['Hauteur']) &&
                         empty($data['Poids']) &&
                         empty($data['Longueur']) &&
                         empty($data['Profondeur']))))
                     && (empty($data['Libelle']) || empty($data['Hauteur']) || empty($data['Poids']) || empty($data['Longueur']) || empty($data['Profondeur']))) {
                     $this->addFlash('error', $translator->trans('Le fichier CSV est invalide'));
                     return $this->redirectToRoute('app_piece_import');
                 }
             }
 
             // On récupère seulements les pieces ayant toutes les informations
             $res = [];
             foreach ($XLSX as $data)
             {
                 if(!empty($data['Libelle']) && !empty($data['Hauteur']) && !empty($data['Poids']) && !empty($data['Longueur']) && !empty($data['Profondeur']) )
                 {
                     $res[] = $data;
                 }
             }
 
 
             // On boucle sur les lignes du CSV
 
              foreach ($res as $data) {
                 // On vérifie si la piece existe déjà
                 $piece = $pieceRepository->findOneBy(['libelle' => $data['Libelle']]);
                     if (!$piece) {
                         $piece = new Piece();
                     }
 
                     $libelle = (string)$data['Libelle'];
                     $hauteur = doubleval($data['Hauteur']);
                     $poids = doubleval($data['Poids']);
                     $longueur = doubleval($data['Longueur']);
                     $profondeur = doubleval($data['Profondeur']);

                 // On hydrate l'objet avec les données du CSV
 
                     $piece->setLibelle($libelle);
                     $piece->setHauteur($hauteur);
                     $piece->setPoids($poids);
                     $piece->setLongueur($longueur);
                     $piece->setProfondeur($profondeur);

                     $em->persist($piece);
                     $em->flush();
             } 
 
             $this->addFlash('success', 'Importation réussie');
             return $this->redirectToRoute('app_piece_index');
         }
 
         return $this->render('spare_parts/import.html.twig', [
             'form' => $form->createView(),
         
         ]);
 
     }

    // Les Tickets

    #[Route('/ticket', name: 'app_ticket_index', methods: ['GET'])]
    public function indexTicket(TicketRepository $ticketRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if ($this->getUser()->getService()->getLibelle() != "Service Après Vente")
        {
            throw $this->createAccessDeniedException();
        }
        $donnees = $ticketRepository->orderByID();
        
        $ticket = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),5

        );

        return $this->render('ticket/index.html.twig', [
            'tickets' => $ticket,
        ]);
    }

    #[Route('/ticket/show/{id}', name: 'app_ticket_show', methods: ['GET', 'POST'])]
    public function show(Ticket $ticket, TicketRepository $TicketRepo, Request $request, TranslatorInterface $translator, EntityManagerInterface $em): Response
    {
        $form3 = $this->createFormBuilder()
            ->add('new_comment', CKEditorType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                ],
                'label' => $translator->trans('Nouveau commentaire'),
                'required' => true
            ])
            ->getForm();

        $form3->handleRequest($request);


        if ($this->getUser()->getService()->getLibelle() != "Service Après Vente")
        {
            throw $this->createAccessDeniedException();
        }

        if ($form3->isSubmitted() && $form3->isValid()) {

            $comment = $form3->get('new_comment')->getData();
            $commentaire = $ticket->getCommentaire();
            // En tête : ----- Date et heure de l'ajout du commentaire (NOM Prénom) ----- \n
            $commentaire .= "----- " . date("d/m/Y H:i:s") . " (" . $this->getUser()->getNom() . " " . $this->getUser()->getPrenom() . ") -----\n";
            $commentaire .= $comment . "\n";

            $ticket->setCommentaire($commentaire);
            $em->persist($ticket);
            $em->flush();
            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);

        }
        
        $ticket = $TicketRepo->find($ticket->getId());
        $user = $ticket->getUser();

        $form = $this->createForm(TicketFormType::class, $ticket);
        $form2 = $this->createForm(EmployeeFormType::class, $user);
        $form->handleRequest($request);
        $form2->handleRequest($request);


        return $this->render('ticket/show.html.twig', [
            'TicketForm' => $form->createView(),
            'UserForm' => $form2->createView(),
            'form3' => $form3->createView(),
            'ticket' => $ticket,
        ]);
    }

    #[Route('/ticket/new', name: 'app_ticket_new', methods: ['GET','POST'])]

    public function createTicket(Ticket $ticket = null, EntityManagerInterface $em, Request $request, TranslatorInterface $translator, UserRepository $er, MailerInterface $mailer): Response
    {
        if (!$ticket) {
            $ticket = new Ticket();
        }

        // Si l'utilisateur a pour service "Service Technique", il ne peut pas créer de ticket
        if (($this->getUser()->getService()->getLibelle() == "Service Après Vente" && $this->getUser()->getRoles()[0] == "ROLE_VALID") || $this->getUser()->getRoles()[0] == "ROLE_ADMIN") {
            throw $this->createAccessDeniedException('Vous ne pouvez pas créer de ticket');
        }


        $form = $this->createForm(TicketFormType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setDateCreation(new \DateTime());
            $ticket->setUser($this->getUser());
            $ticket->setStatus("En attente");
            $em->persist($ticket);
            $em->flush();



            // Envoi d'un mail aux personnels du service Après Vente

            $AllUsersSAV = $er->findAllWithoutMeAndAdminService($this->getUser()->getId(), "Service Après Vente");

            foreach ($AllUsersSAV as $userSAV) {
                $email = (new Email())
                    ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
                    ->to($userSAV->getEmail())
                    ->subject($translator->trans('[Metz Connect] Un nouveau ticket a été créé'))
                    ->html($this->renderView('mail/new_ticketSAV.html.twig', [
                        'ticket' => $ticket,
                        'user' => $userSAV,
                    ]));

                $mailer->send($email);

            }

            // Envoi d'un mail au créateur

            $email = (new Email())
                ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
                ->to($this->getUser()->getEmail())
                ->subject($translator->trans('[Metz Connect] Un nouveau ticket a été créé'))
                ->html($this->renderView('mail/new_ticketUSER.html.twig', [
                    'ticket' => $ticket,
                ]));

            $mailer->send($email);


            $this->addFlash('success', $translator->trans("Le ticket a bien été créé ! Le technicien va vous contacter dans les plus brefs délais."));
            return $this->redirectToRoute('app_home');
        }

        return $this->render('ticket/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ticket/choose/{id}', name: 'app_ticket_choose', methods: ['GET','POST'])]

    public function chooseTicket(Ticket $ticket, EntityManagerInterface $em, Request $request, TranslatorInterface $translator, UserRepository $er, MailerInterface $mailer): Response
    {

        if ($this->getUser()->getService()->getLibelle() != "Service Après Vente") {
            throw $this->createAccessDeniedException();
        }

        $ticket->setStaff($this->getUser());
        $ticket->setStatus("En cours");
        $ticket->setDatePrise(new \DateTime());
        $em->persist($ticket);
        $em->flush();

        $this->addFlash('success',$translator->trans("Le ticket a bien été attribué !"));
        return $this->redirectToRoute('app_ticket_index');

    }

    #[Route('/ticket/close/{id}', name: 'app_ticket_close', methods: ['GET','POST'])]

    public function closeTicket(Ticket $ticket, EntityManagerInterface $em, Request $request, TranslatorInterface $translator, UserRepository $er, MailerInterface $mailer): Response
    {

        if ($this->getUser()->getService()->getLibelle() != "Service Après Vente") {
            throw $this->createAccessDeniedException();
        }

        $ticket->setStatus("Résolu");
        $ticket->setDateResolution(new \DateTime());

        $em->persist($ticket);
        $em->flush();

        $this->addFlash('success',$translator->trans("Le ticket a bien été résolu !"));
        return $this->redirectToRoute('app_ticket_index');

    }







} 