<?php

namespace App\Controller;

use App\Controller\SecurityController as ControllerSecurityController;
use App\Form\EditPasswordFormType;
use App\Form\EditProfileType;
use App\Form\DeleteAccountType;
use App\Repository\LastPasswordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\SecurityBundle\Controller\SecurityController;

/**
 * Class ProfileController
 * @package App\Controller
 * @Route("/profil", name="profil_")
 */

class ProfileController extends AbstractController {

    public function __construct(TranslatorInterface $translator, Security $security)
    {
        $this->translator = $translator;
        $this->security = $security;
    }
    
        #[Route('/edit_profil', name: 'edit_profil')]
        public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
        {


                $user = $this->getUser();
                $form = $this->createForm(EditProfileType::class, $user, [
                    'csrf_protection' => true
                ]);
                $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $image = $form->get('image')->getData();

                if ($image != null)
                {
                    foreach ($image as $images) {
                        $fichier = md5(uniqid()) . '.' . $images->guessExtension();

                        try {
                            // On supprime l'ancienne image
                            $oldImage = $user->getImage();
                            if ($oldImage != null) {
                                unlink($this->getParameter('picture_directory') . '/' . $oldImage);
                            }

                            $images->move(
                                $this->getParameter('picture_directory'),
                                $fichier
                            );
                        } catch (FileException $e) {
                        }

                        $user->setImage($fichier);
                    }
                }

                $entityManager->persist($user);
                $entityManager->flush();

                        $this->addFlash('message', 'Votre profil a été mis à jour avec succès !');
                        return $this->redirectToRoute('app_home');

                }

            return $this->render('profile/edit_profile.html.twig', [
                'editProfileForm' => $form->createView(),
            ]);

            }


    #[Route('/edit_password', name: 'edit_password')]

    public function editPassword(TranslatorInterface $translator, CsrfTokenManagerInterface $csrfTokenManager,LastPasswordRepository $lastPasswordRepository, Request $request,UserPasswordHasherInterface    $userPasswordHasher,EntityManagerInterface $entityManager): Response
    {




            $cloneUser = clone $this->security->getUser();
            $user = $this->security->getUser();
            $lastPassword = $lastPasswordRepository->findOneBy(['user' => $user->getId()]);
            $form = $this->createForm(EditPasswordFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ok = true;
                $oldPassword = $form->get('oldPassword')->getData();
                $newPassword = $form->get('plainPassword')->getData();

                if ($userPasswordHasher->isPasswordValid($user, $oldPassword)) {

                    if ($lastPassword->getPassword1() != null) {


                        $cloneUser->setPassword($lastPassword->getPassword1());
                        if ($userPasswordHasher->isPasswordValid($cloneUser, $newPassword)) {
                            $ok = false;
                            $this->addFlash("error", $translator->trans("Vous ne pouvez pas utiliser le même mot de passe que les 3 précédents"));

                        }

                    }

                    if ($lastPassword->getPassword2() != null) {

                        $cloneUser->setPassword($lastPassword->getPassword2());
                        if ($userPasswordHasher->isPasswordValid($cloneUser, $newPassword)) {
                            $ok = false;
                            $this->addFlash("error", $translator->trans("Vous ne pouvez pas utiliser le même mot de passe que les 3 précédents"));

                        }
                    }

                    if ($lastPassword->getPassword3() != null) {

                        $cloneUser->setPassword($lastPassword->getPassword3());

                        if ($userPasswordHasher->isPasswordValid($cloneUser, $newPassword)) {
                            $ok = false;
                            $this->addFlash("error", $translator->trans("Vous ne pouvez pas utiliser le même mot de passe que les 3 précédents"));
                        }
                    }


                    if ($ok) {
                        $user->setPassword($userPasswordHasher->hashPassword($user, $newPassword));

                        $lastPassword->setPassword3($lastPassword->getPassword2());
                        $lastPassword->setPassword2($lastPassword->getPassword1());
                        $lastPassword->setPassword1($user->getPassword());

                        $entityManager->persist($user);
                        $entityManager->persist($lastPassword);
                        $entityManager->flush();

                        $this->addFlash('message', $translator->trans('Votre mot de passe a été mis à jour avec succès !'));
                        return $this->redirectToRoute('app_home');
                    }
                } else {
                    $this->addFlash('error', $translator->trans('Votre mot de passe actuel est incorrect !'));
                    return $this->redirectToRoute('profil_edit_password');
                }


            }
            return $this->render('edit_password/edit_password.html.twig', [
                'editPasswordForm' => $form->createView(),
            ]);
        }


    #[Route('/delete_account', name: 'delete_account')]
    public function deleteAccount(ControllerSecurityController $secu ,TranslatorInterface $translator, LastPasswordRepository $LPRepo, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response {

        $user = $this->security->getUser();
        $passwords = $LPRepo->findOneBy(['user' => $user->getId()]);
        $form = $this->createForm(DeleteAccountType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $oldPassword = $form->get('oldPassword')->getData();

            if($userPasswordHasher->isPasswordValid($user, $oldPassword)) {

                $entityManager->remove($user);
                $entityManager->remove($passwords);
                $entityManager->flush();

                $this->addFlash('success', $translator->trans('Votre profil a bien été supprimé !'));
                return $this->redirectToRoute('app_delete_success');
                // Déconnexion de l'usager 







            }
            else {
                $this->addFlash('error', $translator->trans('Votre mot de passe actuel est incorrect !'));
                return $this->redirectToRoute('profil_delete_account');
            }


        }
        return $this->render('delete_account/delete_account.html.twig', [
                    'deleteAccountForm' => $form->createView(),
        ]);
    }

   
        /**
         * @Route("/profil/download", name="profil_data")
         */
        public function downloadProfileData(): Response
        {
            $user = $this->security->getUser();
            $data = [
                $this->translator->trans('Nom') => $user->getNom(),
                $this->translator->trans('Prénom') => $user->getPrenom(),
                $this->translator->trans('Email') => $user->getEmail(),
                $this->translator->trans('Date de naissance') => $user->getDateNaissance()->format('d/m/Y'),
                $this->translator->trans('Fonction') => $user->getFonction()->getLibelle(),
                $this->translator->trans('Service') => $user->getService()->getLibelle(),
            ];

            $response = new Response();
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="'.$this->translator->trans("profil").'_'.$user->getNom().'_'.$user->getPrenom().'.csv"');

            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_keys($data));
            fputcsv($handle, $data);
            fclose($handle);

            return $response;
        }
}

?>