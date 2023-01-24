<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Form\ContactReferantFormType;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HomeController extends AbstractController
{
    public function __construct(TranslatorInterface $translator, Security $security)
    {
        $this->translator = $translator;
        $this->security = $security;

        // Fixed locale to fr


    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request, MailerInterface $mailer, TokenStorageInterface $tokenStorage, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $request->getSession()->set('_locale', "fr");
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $user = $this->security->getUser();
        $form=$this->createForm(ContactFormType::class);
        $contact = $form->handleRequest($request);

        if ($user->isIsFreeze())
        {
            $tokenStorage->setToken(null);
            $this->addFlash('error',$translator->trans("Votre compte est bloqué, veuillez contacter l'administrateur"));
            return $this->redirectToRoute('app_login');

        }
        if ($user->isIsArchive())
        {
            $tokenStorage->setToken(null);
            $this->addFlash('error',$translator->trans("Votre compte est archivé, veuillez contacter l'administrateur"));
            return $this->redirectToRoute('app_login');

        }


        if($form->isSubmitted() && $form->isValid()){


            $email = (new TemplatedEmail())
            ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
            ->to(new Address($user->getEmail()))
            ->cc('amarbach5@gmail.com')
            ->subject($translator->trans('[Prise de Contact] Metz Connect'))
            ->htmlTemplate('mail/contact.html.twig')
            ->context([
                'sujet' => $contact->get('sujet')->getData(),
                'message' => $contact->get('message')->getData(),
            ]);

            $mailer->send($email);
            $this->addFlash('message',$translator->trans("Mail envoyé !"));
            return $this->redirect($this->generateUrl('app_home'));
        }



        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'HomeController'
        ]);

    }

    #[Route('/contact', name: 'app_contact')]
    public function indexContact(Request $request, MailerInterface $mailer, TranslatorInterface $translator, UserRepository $er): Response
    {
        $form=$this->createForm(ContactReferantFormType::class);
        $contact = $form->handleRequest($request);

        // Si la liste de référents est vide, on redirige vers la page d'accueil avec un message d'erreur
        if ($er->ListAllReferent($this->security->getUser()->getService()->getId()) == null)
        {
            $this->addFlash('error',$translator->trans("Aucun référent n'est disponible pour votre service"));
            return $this->redirectToRoute('app_home');
        }



        if ($form->isSubmitted() && $form->isValid()){


            $email = (new TemplatedEmail())
                ->from(new Address('ne-pas-repondre@andrew-marbach.fr', 'Metz Connect'))
                ->to(new Address($form->get('referent')->getData()->getEmail()))
                ->cc(new Address($this->security->getUser()->getEmail()))
                ->subject($translator->trans('[Prise de Contact Référent] Metz Connect'))
                ->htmlTemplate('mail/contactReferent.html.twig')
                ->context([
                    'sujet' => $contact->get('sujet')->getData(),
                    'message' => $contact->get('message')->getData(),
                    'user' => $this->security->getUser(),
                ]);

            $mailer->send($email);

            $this->addFlash('message',$translator->trans("Mail envoyé à votre référent !"));
            return $this->redirect($this->generateUrl('app_home'));
        }

        return $this->render('home/contact.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
        ]);
    }





        /**
    * @Route("/change-locale/{locale}", name="change_locale")
    */

    public function changeLocale($locale, Request $request)
    {
        $request->getSession()->set('_locale', $locale);

        return $this->redirect($request->headers->get('referer'));






    }

    /**
     * @Route("/delete_success", name="app_delete_success")
     */

    public function deleteSuccess(Request $request, TranslatorInterface $translator) :Response
    {
        // On reset la session
        $request->getSession()->invalidate();
        // on redirige vers la page d'accueil
        $this->addFlash("message", $translator->trans("Votre compte a bien été supprimé"));
        return $this->redirectToRoute('app_home');
    }
}
