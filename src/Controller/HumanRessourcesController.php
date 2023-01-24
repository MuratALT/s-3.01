<?php

namespace App\Controller;

use App\Entity\DocumentUser;
use App\Entity\User;
use App\Form\DocumentsUserFormType;
use App\Form\EmployeeFormType;
use App\Form\SearchUserFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/employe')]
class HumanRessourcesController extends AbstractController
{
    #[Route('/', name: 'app_employe_index', methods: ['POST', 'GET'])]
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        if ($this->getUser()->getService()->getLibelle() != "Ressources Humaines")
        {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(SearchUserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nom = $form->get('q')->getData();
            $categ = $form->get('service')->getData();
            $archive = $form->get('archive')->getData();
            $fonction = $form->get('fonction')->getData();

            $users_search = $userRepository->findBySearch($nom, $categ,$fonction ,$archive);

            $utilisateurs = $paginator->paginate(
                $users_search,
                $request->query->getInt('page', 1),
                10
            );

            return $this->render('employe/index.html.twig', [
                'users' => $utilisateurs,
                'form' => $form->createView(),
            ]);
        }

        /* Ajouter ici le filtrage */
        
        return $this->render('employe/index.html.twig', [
            'users' => $userRepository->findAllWithoutAdminAndWait(),
            'form' => $form->createView(),
        ]);
    }
    #[Route('/export', name: 'app_employe_export', methods: ['GET','POST'])]
    public function export(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchUserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $nom = $form->get('q')->getData();
            $categ = $form->get('service')->getData();
            $fonc = $form->get('fonction')->getData();
            $archive = $form->get('archive')->getData();

            $donnees_produit = $userRepository->findBySearch($nom, $categ,$fonc, $archive);

            $produits = $paginator->paginate(
                $donnees_produit,
                $request->query->getInt('page', 1),
                10
            );

            return $this->render('employe/export.html.twig', [
                'form' => $form->createView(),
                'users' => $produits,
            ]);
        }

        $donnees = $userRepository->findAllWithoutAdminAndWait();

        $produit = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),10
        );

        return $this->render('employe/export.html.twig', [
            'users' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/exports', name: 'app_employe_exports', methods: ['GET','POST'])]
    public function exports(UserRepository $userRepository, Request $request, TranslatorInterface $translator): Response
    {
        $data = $request->request->get("checked_items");
        // On récupère tous les ID des produis à exporter
        $ids = explode(",", $data);

        if ($ids[0] == "")
        {
            $this->addFlash("error",$translator->trans("Veuillez sélectionner au moins un employé").'.');
            return $this->redirectToRoute('app_employe_export');
        }

        $utilisateurs = [];
        for ($i = 0; $i < count($ids); $i++) {
            $utilisateurs[] = $userRepository->find($ids[$i]);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $translator->trans("Nom"));
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->setCellValue('B1', $translator->trans("Prénom"));
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->setCellValue('C1', $translator->trans("Mail"));
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->setCellValue('D1', $translator->trans("Service"));
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->setCellValue('E1', $translator->trans('Fonction'));
        $sheet->getColumnDimension('E')->setWidth(20);

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:E1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->setTitle('Employé');

        for ($i = 0; $i < count($utilisateurs); $i++) {
            $sheet->setCellValue('A' . ($i + 2), $utilisateurs[$i]->getNom());
            $sheet->setCellValue('B' . ($i + 2), $utilisateurs[$i]->getPrenom());
            $sheet->setCellValue('C' . ($i + 2), $utilisateurs[$i]->getEmail());
            $sheet->setCellValue('D' . ($i + 2), $utilisateurs[$i]->getService()->getLibelle());
            $sheet->setCellValue('E' . ($i + 2), $utilisateurs[$i]->getFonction()->getLibelle());
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'exportEmploye' . date('d-m-Y_H-i-s') . '.xlsx';
        $writer->save($filename);
        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->deleteFileAfterSend(true);
        return $response;
    }
    #[Route('/{id}', name: 'app_employe_show', methods: ['GET'])]
    public function show(Request $request, User $user): Response
    {
        if ($this->getUser()->getService()->getLibelle() != "Ressources Humaines")
        {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(EmployeeFormType::class, $user);
        $form->handleRequest($request);
        return $this->render('employe/show.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_employe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, EntityManagerInterface $em): Response {
        if ($this->getUser()->getService()->getLibelle() != "Ressources Humaines")
        {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(EmployeeFormType::class, $user);
        $form->handleRequest($request);

        $documentuser = new DocumentUser();

        $form2 = $this->createForm(DocumentsUserFormType::class, $documentuser);
        $form2->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère le document upload
            $document = $form->get('documentsUser')->getData();
            // On vérifie si un document a été upload
                $nom_fichier = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
                $fichier = $user->getNom() . '-' . $user->getPrenom() . '-' . $nom_fichier . '.' . $document->guessExtension();

                $libelle = $form2->get('libelle')->getData();
                $commentaire = $form2->get('commentaire')->getData() != null ? $form2->get('commentaire')->getData() : "";
                try {
                    // On copie le fichier dans le dossier uploads
                    $document->move(
                        $this->getParameter('employee_documents_directory'),
                        $fichier
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $doc = new DocumentUser();
                $doc->setName($fichier);

                /* set libellé */
                $doc->setLibelle($libelle);

                /* set commentaire */
                $doc->setCommentaire($commentaire);

                /* set date_ajout */
                $doc->setDateAjout(new \DateTime());
                // On met à jour le nom du fichier dans la base de données
                $user->addDocumentUser($doc);


            $em->persist($user);
            $em->flush();

            /* Ajout d'une notification de type success si un document a été ajouté ou supprimé */
            $this->addFlash('success', 'Les modifications ont bien été enregistrées');

            return $this->redirectToRoute('app_employe_edit', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employe/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ]);
    }


    /* Suppression d'un document */

    #[Route('/delete-document/{id}', name: 'app_employe_delete_document', methods: ['GET', 'POST'])]
    public function deleteDocument(Request $request, DocumentUser $documentUser, EntityManagerInterface $em): Response
    {
        if ($this->getUser()->getService()->getLibelle() != "Ressources Humaines")
        {
            throw $this->createAccessDeniedException();
        }

        // On le suppime du dossier uploads
        unlink($this->getParameter('employee_documents_directory') . '/' . $documentUser->getName());

        // On le supprime de la base de données

        $em->remove($documentUser);
        $em->flush();

        /* Ajout d'une notification de type success si un document a été ou supprimé */

        $this->addFlash('success', 'Le document a bien été supprimé');

        /* Redirection vers app_employe_edit avec l'id du user en paramètre */

        return $this->redirectToRoute('app_employe_edit', ['id' => $documentUser->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }


}
