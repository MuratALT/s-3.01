<?php

namespace App\Controller;

use App\Entity\TypeMedia;
use App\Entity\TypeProd;
use App\Entity\TypeAlim;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use App\Entity\Reglementation;
use App\Entity\Service;
use App\Entity\User;
use App\Repository\TypeMediaRepository;
use App\Repository\TypeProdRepository;
use App\Repository\TypeAlimRepository;
use App\Repository\ReglementationRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\Parser\Token;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin", name="admin_")
 */

class AdminController extends AbstractController
{
    public function __construct(TranslatorInterface $translator, Security $security)
    {
        $this->translator = $translator;
        $this->security = $security;
    }

    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }


    /**
     * @Route("/type_media", name="list_type_media")
     */
    public function list_TypeMedia(Request $request, TranslatorInterface $translator, PaginatorInterface $paginator, TypeMediaRepository $TypeMediaRepo): Response
    {
        $donnees = $TypeMediaRepo->findAll();

        $type_media = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1), 15
        );


        return $this->render('type_media/list.html.twig', [
            'types_media' => $type_media,
        ]);
    }


    /**
     * @Route("/type_media/add", name="add_type_media")
     * @Route("/type_media/{id}/edit", name="edit_type_media")
     */


    public function addTypeMedia(TypeMedia $type_media = null, TranslatorInterface $translator, Request $request, EntityManagerInterface $manager, TypeMediaRepository $TypeMediaRepo)
    {
        if (!$type_media) {
            $type_media = new TypeMedia();
        }
        $form = $this->createFormBuilder($type_media)
            ->add('libelle', TextType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir un libellé'),
                    'class' => 'form-control',
                ],
                'label' => $translator->trans('Libéllé'),
                'required' => true
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($TypeMediaRepo->findOneBy(['libelle' => $type_media->getLibelle()]) != null && $type_media->getId() == null) {
                $this->addFlash('error', $translator->trans("Le libellé existe déjà !"));
            } else {
                /* On vérifie qu"il y'a bien eu des changements : */


                {

                    if ($type_media->getId() == null) {
                        $this->addFlash('success', $translator->trans("Le type de média a bien été créé !"));
                    } else {
                        $this->addFlash('success', $translator->trans("Le type de média a bien été modifié !"));
                    }

                    $manager->persist($type_media);
                    $manager->flush();

                    return $this->redirectToRoute("admin_list_type_media");
                }


            }
        }

        return $this->render('/type_media/add.html.twig', [
            'form' => $form->createView(),
            'editMode' => $type_media->getId() !== null
        ]);
    }


    /**
     * @Route("/type_media/{id}/delete", name="delete_type_media")
     */

    public function deleteTypeMedia(TypeMedia $typeMedia, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {

        $em->remove($typeMedia);
        $em->flush();
        $this->addFlash('success', $translator->trans("Le type de média a bien été supprimé !"));
        return $this->redirectToRoute('admin_list_type_media');

    }


    /**
     * @Route("/type_produit", name="list_type_produit")
     */

    public function list_type_produit(Request $request, PaginatorInterface $paginator, TypeProdRepository $TypeMediaRepo): Response
    {
        $donnees = $TypeMediaRepo->findAll();

        $type_produit = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1), 15
        );


        return $this->render('type_produit/list.html.twig', [
            'types_produits' => $type_produit,
        ]);
    }


    /**
     * @Route("/type_produit/add", name="add_type_produit")
     * @Route("/type_produit/{id}/edit", name="edit_type_produit")
     */


    public function addTypeProduit(TypeProd $type_produit = null, TranslatorInterface $translator, Request $request, EntityManagerInterface $manager, TypeProdRepository $TypeProdRepo)
    {
        if (!$type_produit) {
            $type_produit = new TypeProd();
        }
        $form = $this->createFormBuilder($type_produit)
            ->add('libelle', TextType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir un libellé'),
                    'class' => 'form-control',
                ],
                'label' => $translator->trans('Libéllé'),
                'required' => true
            ])
            ->getForm();

        $form->handleRequest($request);

        $base = $type_produit->getLibelle();
        if ($form->isSubmitted() && $form->isValid()) {
            if ($TypeProdRepo->findOneBy(['libelle' => $type_produit->getLibelle()]) != null && $type_produit->getId() == null) {
                $this->addFlash('error', $translator->trans("Le libellé existe déjà !"));
            } else {

                if ($type_produit->getId() == null) {
                    $this->addFlash('success', $translator->trans("Le type de produit a bien été créé !"));
                } else {
                    $this->addFlash('success', $translator->trans("Le type de produit a bien été modifié !"));
                }

                $manager->persist($type_produit);
                $manager->flush();

                return $this->redirectToRoute("admin_list_type_produit");

            }

        }

        return $this->render('/type_produit/add.html.twig', [
            'form' => $form->createView(),
            'editMode' => $type_produit->getId() !== null
        ]);
    }


    /**
     * @Route("/type_produit/{id}/delete", name="delete_type_produit")
     */

    public function deleteTypeProduit(TypeProd $type_produit, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {

        // On vérifie que le type de produit n'est pas utilisé par un produit
        if ($type_produit->getProduits()->count() > 0) {
            $this->addFlash('error', $translator->trans("Le type de produit est utilisé par un produit ! Elle ne peut pas être supprimée !"));
        } else {
            $em->remove($type_produit);
            $em->flush();
            $this->addFlash('success', $translator->trans("La catégorie de produit a bien été supprimée !"));

        }
        return $this->redirectToRoute('admin_list_type_produit');
    }


    /**
     * @route("/type_alim" , name = "list_type_alim")
     */

    public function list_type_alimentation(Request $request, PaginatorInterface $paginator, TypeAlimRepository $TypeAlimRepo): Response
    {
        $donnees = $TypeAlimRepo->findAll();

        $type_alim = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1), 15
        );


        return $this->render('type_alim/list.html.twig', [
            'types_alim' => $type_alim,
        ]);
    }

    /**
     * @Route("/type_alim/add", name="add_type_alim")
     * @Route("/type_alim/{id}/edit", name="edit_type_alim")
     */


    public function addTypeAlimentation(TypeAlim $type_alim = null, TranslatorInterface $translator, Request $request, EntityManagerInterface $manager, TypeAlimRepository $TypeAlimRepo)
    {
        if (!$type_alim) {
            $type_alim = new TypeAlim();
        }
        $form = $this->createFormBuilder($type_alim)
            ->add('libelle', TextType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir un libellé'),
                    'class' => 'form-control',
                ],
                'label' => $translator->trans('Libéllé'),
                'required' => true
            ])
            ->getForm();

        $form->handleRequest($request);

        $base = $type_alim->getLibelle();
        if ($form->isSubmitted() && $form->isValid()) {
            if ($TypeAlimRepo->findOneBy(['libelle' => $type_alim->getLibelle()]) != null && $type_alim->getId() == null) {
                $this->addFlash('error', $translator->trans("Le libellé existe déjà !"));
            } else {

                if ($type_alim->getId() == null) {
                    $this->addFlash('success', $translator->trans("Le type d'alimentation a bien été créé !"));
                } else {
                    $this->addFlash('success', $translator->trans("Le type d'alimentation a bien été modifié !"));
                }

                $manager->persist($type_alim);
                $manager->flush();

                return $this->redirectToRoute("admin_list_type_alim");

            }

        }

        return $this->render('/type_alim/add.html.twig', [
            'form' => $form->createView(),
            'editMode' => $type_alim->getId() !== null
        ]);
    }

    /**
     * @Route("/type_alim/{id}/delete", name="delete_type_alim")
     */

    public function deleteTypeAlimentation(TypeAlim $type_alim, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {

        $em->remove($type_alim);
        $em->flush();
        $this->addFlash('success', $translator->trans("La catégorie d'alimentation a bien été supprimée !"));
        return $this->redirectToRoute('admin_list_type_alim');
    }

    /**
     * @route("/reglementation" , name = "list_reglementation")
     */

    public function list_reglementation(Request $request, PaginatorInterface $paginator, ReglementationRepository $TypeRegRepo): Response
    {
        $donnees = $TypeRegRepo->findAll();

        $reglementation = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1), 15
        );


        return $this->render('reglementation/list.html.twig', [
            'types_reglementation' => $reglementation,
        ]);
    }

    /**
     * @Route("/reglementation/add", name="add_reglementation")
     * @Route("/reglementation/{id}/edit", name="edit_reglementation")
     */

    public function addTypeReglementation(Reglementation $reglementation = null, SluggerInterface $slugger, TranslatorInterface $translator, Request $request, EntityManagerInterface $manager, ReglementationRepository $TypeRegRepo)
    {
        if (!$reglementation) {
            $reglementation = new Reglementation();
        }
        $form = $this->createFormBuilder($reglementation)
            ->add('libelle', TextType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir un libellé'),
                    'class' => 'form-control',
                ],
                'label' => $translator->trans('Libéllé'),
                'required' => true
            ])
            ->add('picto', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/png',
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/png',
                        ],
                        'mimeTypesMessage' => $translator->trans('Veuillez uploader un fichier PNG.'),

                    ])
                ],

            ])
            ->getForm();

        $form->handleRequest($request);
        $file = $form->get('picto')->getData();
        $fichier = $reglementation->getPicto();
        $base = $reglementation->getLibelle();
        if ($form->isSubmitted() && $form->isValid()) {
            if ($TypeRegRepo->findOneBy(['libelle' => $reglementation->getLibelle()]) != null && $reglementation->getId() == null) {
                $this->addFlash('error', $translator->trans("Le libellé existe déjà !"));
            } else {
                //  On supprime l'ancien fichier
                if ($fichier != null && $file != null) {
                    unlink($this->getParameter('picto_directory') . '/' . $fichier);
                }


                if ($reglementation->getId() == null) {
                    $this->addFlash('success', $translator->trans("La réglementation a bien été créé !"));
                } else {
                    $this->addFlash('success', $translator->trans("La réglementation a bien été modifié !"));
                }

               
                if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                    try {
                        $file->move(
                            $this->getParameter('picto_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }
                    $reglementation->setPicto($newFilename);
                }
                $manager->persist($reglementation);
                $manager->flush();

                return $this->redirectToRoute("admin_list_reglementation");

            }

        }

        return $this->render('/reglementation/add.html.twig', [
            'form' => $form->createView(),
            'picto' => $reglementation->getPicto(),
            'editMode' => $reglementation->getId() !== null
        ]);
    }

    /**
     * @Route("/reglementation/{id}/delete", name="delete_reglementation")
     */

    public function deleteTypeReglementation(Reglementation $reglementation, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {


        // On supprime le fichier
        unlink($this->getParameter('picto_directory') . '/' . $reglementation->getPicto());
        $em->remove($reglementation);
        $em->flush();
        $this->addFlash('success', $translator->trans("La catégorie de réglementation a bien été supprimée !"));
        return $this->redirectToRoute('admin_list_reglementation');
    }

    /**
     * @Route("/field", name="app_field")
     */

    public function field(Request $request, TranslatorInterface $translator): Response
    {
        return $this->render('field/index.html.twig', [
            'controller_name' => 'FieldController',
        ]);
    }


    /**
     * @Route("/usurpation/{id}", name="app_usurpation")
     */

    public function usurpation(Request $request, TranslatorInterface $translator, Service $service, Security $security, EntityManagerInterface $em): Response
    {

        $user = $this->security->getUser();

        $user->setUsurp(true);
        $user->setService($service);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }


    /**
     * @Route("/unusurpation", name="unusurpation")
     */

    public function unusurpation(Request $request, TranslatorInterface $translator, Security $security, EntityManagerInterface $em): Response
    {

        $user = $this->security->getUser();
        $user->setUsurp(false);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/export/bdd", name="export_bdd")
     */

    // Export of the database in a .sql file

    public function exportDatabase()
    {
        $filename = 'export_db' . date('d-m-Y_H-i-s') . '.sql';
        $command = 'mysqldump -u root -p55772003 MetzConnect > ' . __DIR__ . '/../../public/export/' . $filename;
        exec($command);
        $response = new BinaryFileResponse(__DIR__ . '/../../public/export/' . $filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        return $response;
    }

    /**
     * @Route("/export/users", name="export_users")
     */
    // Téléchargement du fichier excel des utilisateurs avec leurs informations
    public function downloadUsers(UserRepository $userRepo, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $data = $userRepo->findAllWithoutAdmin();
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $translator->trans('Nom'));
        $sheet->setCellValue('B1', $translator->trans('Prénom'));
        $sheet->setCellValue('C1', $translator->trans('Email'));
        $sheet->setCellValue('D1', $translator->trans('Date de naissance'));
        $sheet->setCellValue('E1', $translator->trans('Fonction'));
        $sheet->setCellValue('E1', $translator->trans('Service'));

        $i = 2;

        foreach ($data as $user) {
            $sheet->setCellValue('A' . $i, $user->getNom());
            $sheet->setCellValue('B' . $i, $user->getPrenom());
            $sheet->setCellValue('C' . $i, $user->getEmail());
            $sheet->setCellValue('D' . $i, $user->getDateNaissance()->format('d/m/Y'),);
            $sheet->setCellValue('E' . $i, $user->getFonction()->getLibelle());
            $sheet->setCellValue('E' . $i, $user->getService()->getLibelle());
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'export_users' . date('d-m-Y_H-i-s') . '.xlsx';
        $writer->save(__DIR__ . '/../../public/export/Users/' . $filename);
        $response = new BinaryFileResponse(__DIR__ . '/../../public/export/Users/' . $filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        return $response;

    }


    #[Route('/users/wait', name: 'user_wait')]
    public function UsersWait(UserRepository $userRepository): Response
    {
        return $this->render('user/indexWait.html.twig', [
            'users' => $userRepository->findAllWait(),
        ]);
    }


    #[Route('/users/accept/{id}', name: 'user_accept')]
    public function UsersAccept(User $user, EntityManagerInterface $em, TranslatorInterface $translator, MailerInterface $mailer): Response
    {
        $user->setRoles(['ROLE_VALID']);
        $em->persist($user);
        $em->flush();

        // Send Mail to user

        $email = (new TemplatedEmail())
            ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
            ->to(new Address($user->getEmail()))
            ->cc('amarbach5@gmail.com')
            ->subject($translator->trans('[Acceptation] Metz Connect'))
            ->htmlTemplate('mail/acceptation.html.twig')
            ->context([
                'user' => $user
            ]);

        $mailer->send($email);

        $this->addFlash('success', $translator->trans("L'utilisateur a bien été accepté !"));
        return $this->redirectToRoute('admin_user_wait');
    }

    /**
     * @Route("/admin/user/accept/selected", name="user_accept_selected")
     */
    public function UsersAcceptSelected(Request $request, UserRepository $userRepository, EntityManagerInterface $em, TranslatorInterface $translator, MailerInterface $mailer): Response
    {

        // Vérifier que le bouton a été cliqué et que des utilisateurs ont été sélectionnés
        $status = $request->request->get('submit');


    // Récupère les IDs des utilisateurs sélectionnés
        $AllData = $request->request->All();

        if (count($AllData) <= 1) {
            $this->addFlash('error', $translator->trans("Aucun utilisateur sélectionné !"));
            return $this->redirectToRoute('admin_user_wait');
        }

        // AllData est sous la forme : Clé = selectedUsers_1, Valeur = 1
        // On récupère donc les valeurs des clés

        $selectedUsers = array_values($AllData);

        // Maintenant on récupère les utilisateurs correspondants

        // on enleve le premier élément du tableau
        array_shift($selectedUsers);

        foreach ($selectedUsers as $selectedUser) {
            $user = $userRepository->find($selectedUser);
            $users[] = $user;
        }

        if($status == 'accept'){

            foreach ($users as $user) {
                $user->setRoles(['ROLE_VALID']);
                $em->persist($user);

                // Send Mail to user
                $email = (new TemplatedEmail())
                    ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
                    ->to(new Address($user->getEmail()))
                    ->cc('amarbach5@gmail.com')
                    ->subject($translator->trans('[Acceptation] Metz Connect'))
                    ->htmlTemplate('mail/acceptation.html.twig')
                    ->context([
                        'user' => $user
                    ]);

                $mailer->send($email);
            }

            $em->flush();
            $this->addFlash("success",$translator->trans('Les utilisateurs ont bien été acceptés !'));
        }
        else {
           foreach ($users as $user) {


               // Supprimer en cascade les données de l'utilisateur
               $em->remove($user);
               $em->flush();

               // Send Mail to user

               $email = (new TemplatedEmail())
                   ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
                   ->to(new Address($user->getEmail()))
                   ->cc('amarbach5@gmail.com')
                   ->subject($translator->trans('[Refus] Metz Connect'))
                   ->htmlTemplate('mail/refus.html.twig')
                   ->context([
                       'user' => $user
                   ]);

               $mailer->send($email);

               $this->addFlash("success",$translator->trans('Les utilisateurs ont bien été supprimées !'));

           }
        }


        return $this->redirectToRoute('admin_user_wait');
    }


    #[Route('/users/refus/{id}', name: 'user_refus')]
    public function UsersRefus(User $user, EntityManagerInterface $em, TranslatorInterface $translator, MailerInterface $mailer): Response
    {

        $em->remove($user);
        $em->flush();

        // Send Mail to user

        $email = (new TemplatedEmail())
            ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
            ->to(new Address($user->getEmail()))
            ->cc('amarbach5@gmail.com')
            ->subject($translator->trans('[Refus] Metz Connect'))
            ->htmlTemplate('mail/refus.html.twig')
            ->context([
                'user' => $user
            ]);

        $mailer->send($email);

        $this->addFlash('success', $translator->trans("L'utilisateur a bien été supprimé !"));
        return $this->redirectToRoute('admin_user_wait');
    }

    /**
     * @Route("/import/dl", name="download_model")
     */

    // Téléchargement du model EXCEL pour l'import des produits avec les 6 colonnes obligatoires
    // 1.reference, 2.libelle, 3.garantie, 4.pu, 5.categorie, 6.alimentation
    // Pour la colonne 5, on choisir via une liste déroulante le nom de la catégorie
    // Pour la colonne 6, on choisir via une liste déroulante le nom de l'alimentation


    public function downloadModel(TypeProdRepository $categories, TypeAlimRepository $types)
    {

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Reference');
        // Définition de la largeur de la colonne
        $sheet->getColumnDimension('A')->setWidth(20);
        // Définition de la hauteur de la ligne
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->setCellValue('B1', 'Libelle');
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->setCellValue('C1', 'Garantie');
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->setCellValue('D1', 'Prix unitaire');
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->setCellValue('E1', 'Categorie');
        $sheet->getColumnDimension('E')->setWidth(50);
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->setCellValue('F1', 'Alimentation');
        $sheet->getColumnDimension('F')->setWidth(50);
        $sheet->getRowDimension('1')->setRowHeight(30);


        $categoriess = $categories->findAll();


        $categoryNames = array_map(function ($category) {
            // On ajoute des gullets pour chaque catégorie
            return '"' . $category->getLibelle() . '"';
        }, $categoriess);


        $protection = $sheet->getProtection();


        $dataValidation = $sheet->getCell('E2')->getDataValidation();

        $dataValidation->setType(DataValidation::TYPE_LIST)
            ->setAllowBlank(false)
            ->setShowDropDown(true)
            ->setFormula1(implode(',', $categoryNames));

        // Garder la majuscule pour les noms de colonnes
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);



        for ($row = 2; $row <= 20; $row++) {
            $sheet->getCell('E' . $row)->setDataValidation($dataValidation);
        }


        $types = $types->findAll();

        $typeNames = array_map(function ($type) {
          // On ajoute des guillemets pour les noms de colonnes
            return '"' . $type->getLibelle() . '"';
        }, $types);



        $dataValidation = $sheet->getCell('F2')->getDataValidation();


        $dataValidation->setType(DataValidation::TYPE_LIST)
            ->setAllowBlank(true)
            ->setShowDropDown(true)
            ->setFormula1(join(',', $typeNames));

        for ($row = 2; $row <= 20; $row++) {
            $sheet->getCell('F' . $row)->setDataValidation($dataValidation);
        }



        $writer = new Xlsx($spreadsheet);
        $filename = 'model_import' . date('d-m-Y_H-i-s') . '.xlsx';
        $writer->save(__DIR__ . '/../../public/export/Model/' . $filename);
        $response = new BinaryFileResponse(__DIR__ . '/../../public/export/Model/' . $filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        // Suppression du fichier après téléchargement
        $response->deleteFileAfterSend(true);
        return $response;

    }






}

