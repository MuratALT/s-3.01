<?php

namespace App\Controller;

use App\Entity\Fonction;
use App\Form\FonctionType;
use App\Repository\FonctionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/fonction')]
class FonctionController extends AbstractController
{
    #[Route('/', name: 'app_fonction_index', methods: ['GET'])]
    public function index(FonctionRepository $fonctionRepository): Response
    {
        return $this->render('fonction/index.html.twig', [
            'fonctions' => $fonctionRepository->findAll(),
        ]);
    }

    #[Route('/ajout', name: 'app_fonction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FonctionRepository $fonctionRepository, TranslatorInterface $trans): Response
    {
        $fonction = new Fonction();
        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($fonctionRepository->findOneBy(['libelle' => $fonction->getLibelle()])) {
                $this->addFlash('error', $trans->trans('Ce libellé est déja utilisé'));
                return $this->redirectToRoute('app_fonction_new', [], Response::HTTP_SEE_OTHER);
            }
            $fonctionRepository->save($fonction, true);

            return $this->redirectToRoute('app_fonction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fonction/new.html.twig', [
            'fonction' => $fonction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fonction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fonction $fonction, FonctionRepository $fonctionRepository): Response
    {
        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fonctionRepository->save($fonction, true);

            return $this->redirectToRoute('app_fonction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fonction/edit.html.twig', [
            'fonction' => $fonction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fonction_delete')]
    public function delete(Request $request, Fonction $fonction, FonctionRepository $fonctionRepository, EntityManagerInterface $em, TranslatorInterface $trans): Response
    {
        $em->remove($fonction);
        $em->flush();

        $this->addFlash('success', $trans->trans('Suppression effectuée avec succès'));
        return $this->redirectToRoute('app_fonction_index', [], Response::HTTP_SEE_OTHER);
    }
}
