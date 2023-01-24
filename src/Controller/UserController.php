<?php

namespace App\Controller;

use App\Entity\DocumentUser;
use App\Entity\User;
use App\Form\DocumentsUserFormType;
use App\Form\EditProfileType;
use App\Form\EditUserType;
use App\Form\EmployeeFormType;
use App\Form\SearchUserFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['POST', 'GET'])]
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {

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

            return $this->render('user/index.html.twig', [
                'users' => $utilisateurs,
                'form' => $form->createView(),
            ]);
        }
        $donnees = $userRepository->findAllWithoutAdminAndWait();

        $produit = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),10
        );

        return $this->render('user/index.html.twig', [
            'users' => $produit,
            'form' => $form->createView(),

        ]);
    }

    /* #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    } */


    #[Route('/export', name: 'app_user_export', methods: ['GET','POST'])]
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

            return $this->render('user/export.html.twig', [
                'form' => $form->createView(),
                'users' => $produits,
            ]);
        }

        $donnees = $userRepository->findAllWithoutAdminAndWait();

        $produit = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),10
        );

        return $this->render('user/export.html.twig', [
            'users' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/exports', name: 'app_user_exports', methods: ['GET','POST'])]
    public function exports(UserRepository $userRepository, Request $request, TranslatorInterface $translator): Response
    {
        $data = $request->request->get("checked_items");
        // On récupère tous les ID des produis à exporter
        $ids = explode(",", $data);

        if ($ids[0] == "")
        {
            $this->addFlash("error",$translator->trans("Veuillez sélectionner au moins un produit").'.');
            return $this->redirectToRoute('app_user_export');
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
        $spreadsheet->getActiveSheet()->setTitle('Utilisateurs');

        for ($i = 0; $i < count($utilisateurs); $i++) {
            $sheet->setCellValue('A' . ($i + 2), $utilisateurs[$i]->getNom());
            $sheet->setCellValue('B' . ($i + 2), $utilisateurs[$i]->getPrenom());
            $sheet->setCellValue('C' . ($i + 2), $utilisateurs[$i]->getEmail());
            $sheet->setCellValue('D' . ($i + 2), $utilisateurs[$i]->getService()->getLibelle());
            $sheet->setCellValue('E' . ($i + 2), $utilisateurs[$i]->getFonction()->getLibelle());
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'exportUser' . date('d-m-Y_H-i-s') . '.xlsx';
        $writer->save($filename);
        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->deleteFileAfterSend(true);
        return $response;
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, Request $request): Response
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'editProfileForm' => $form->createView(),
        ]);
    }



    #[Route('/freeze/{id}', name: 'app_user_freeze')]
    public function Geler(TranslatorInterface $trans, Request $request, User $user, UserRepository $userRepository): Response
    {

        if($user->isIsArchive() == true)
        {
            $this->addFlash('error',$trans->trans('Utilisateur archivé, impossible de le geler'));
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
        $user->setIsFreeze(true);
        $userRepository->save($user, true);
        

        $this->addFlash('success', $trans->trans('Utilisateur gelé avec succès'));
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/unfreeze/{id}', name: 'app_user_unfreeze')]
    public function DeGeler(TranslatorInterface $trans, Request $request, User $user, UserRepository $userRepository): Response
    {
        $user->setIsFreeze(false);
        $userRepository->save($user, true);
        

        $this->addFlash('success', $trans->trans('Utilisateur dégelé avec succès'));
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/archive/{id}', name: 'app_user_archive')]
    public function Archiver(TranslatorInterface $trans, Request $request, User $user, UserRepository $userRepository): Response
    {
        if($user->isIsFreeze() == true)
        {
            $user->setIsFreeze(false);
        }
        $user->setIsArchive(true);
        $userRepository->save($user, true);
        

        $this->addFlash('success', $trans->trans('Utilisateur archivé avec succès'));
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/unarchive/{id}', name: 'app_user_unarchive')]
    public function DeArchiver(TranslatorInterface $trans, Request $request, User $user, UserRepository $userRepository): Response
    {
        $user->setIsArchive(false);
        $userRepository->save($user, true);


        

        $this->addFlash('success', $trans->trans('Utilisateur désarchivé avec succès'));
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/edit/{id}', name: 'user_edit')]
    public function editUser(Request $request, User $user, UserRepository $userRepository, EntityManagerInterface $em): Response {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();

            /* Ajout d'une notification de type success si un document a été ajouté ou supprimé */
            $this->addFlash('success', 'Les modifications ont bien été enregistrées');

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'editProfileForm' => $form->createView(),
        ]);
    }

}
