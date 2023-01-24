<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\DocumentProduit;
use App\Entity\Images;
use App\Entity\InfoMarketing;
use App\Entity\InfoTechnique;
use App\Entity\Produit;
use App\Entity\ProduitPiece;
use App\Entity\TypeProd;
use App\Form\ImportType;
use App\Form\InfoMarketingFormType;
use App\Form\InfoTechniqueFormType;
use App\Form\LmediaType;
use App\Form\PiecesFormType;
use App\Form\ProduitType;
use App\Form\SearchProductFormType;
use App\Model\SearchDataProduit;
use App\Repository\CategorieRepository;
use App\Repository\ProduitPieceRepository;
use App\Repository\ProduitRepository;
use App\Repository\TypeAlimRepository;
use App\Repository\TypeProdRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Image;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

// Si l'utilisateur a pour ŝERVICE "Comptabilité" il peut accéder à la page





#[Route('/produit')]
class ProduitController extends AbstractController
{
    public function __construct(TranslatorInterface $translator, Security $security)
    {
        $this->translator = $translator;
        $this->security = $security;
    }

    #[Route('/import', name: 'app_produit_import')]
    public function import(TranslatorInterface $translator, Request $request, ProduitRepository $produitRepository, EntityManagerInterface $em, TypeProdRepository $categs, TypeAlimRepository $alims): Response
    {
        if ($this->getUser()->getRoles()[0] != "ROLE_ADMIN")
        {
            if($this->getUser()->getService()->getLibelle() != "Product Owner") throw $this->createAccessDeniedException();
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


            if ($headers !== ['Reference', 'Libelle', 'Garantie', 'Prix unitaire', 'Categorie', 'Alimentation']) {
                $this->addFlash('error', $translator->trans('Le fichier XLSX est invalide, Il manque des colonnes').'.');
                return $this->redirectToRoute('app_produit_import');
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
                    (!((empty($data['Reference']) &&
                        empty($data['Libelle']) &&
                        empty($data['Garantie']) &&
                        empty($data['Prix unitaire']) &&
                        empty($data['Categorie']) &&
                        empty($data['Alimentation']))))
                    && (empty($data['Reference']) || empty($data['Libelle']) || empty($data['Garantie']) || empty($data['Prix unitaire']) || empty($data['Categorie']) || empty($data['Alimentation']))) {
                    $this->addFlash('error', $translator->trans('Le fichier CSV est invalide'));
                    return $this->redirectToRoute('app_produit_import');
                }
            }

            // On récupère seulements les produits ayant toutes les informations
            $res = [];
            foreach ($XLSX as $data)
            {
                if(!empty($data['Reference']) && !empty($data['Libelle']) && !empty($data['Garantie']) && !empty($data['Prix unitaire']) && !empty($data['Categorie']) && !empty($data['Alimentation']))
                {
                    $res[] = $data;
                }
            }


            // On boucle sur les lignes du CSV

             foreach ($res as $data) {
                // On vérifie si le produit existe déjà
                 $produit = $produitRepository->findOneBy(['reference' => $data['Reference']]);
                    if (!$produit) {
                        $produit = new Produit();
                    }

                    $ref = $data['Reference'];
                    $ref_int = intval($ref);
                    $libelle = (string)$data['Libelle'];
                    $garantie = intval($data['Garantie']);
                    $prix = floatval($data['Prix unitaire']);

                // On hydrate l'objet avec les données du CSV

                    $produit->setReference($ref_int);
                    $produit->setLibelle($libelle);
                    $produit->setGarantie($garantie);
                    $produit->setPu($prix);
                
                    $categ = $categs->findOneBy(['libelle' => (string)$data['Categorie']]);

                    $produit->setTypeprod($categ);

                    $infomarket = new InfoMarketing();
                    $infomarket->setDescription("");
                    $infomarket->setFonctionnalites("");
                    $infomarket->setBenefices("");

                    $infotech = new InfoTechnique();
                    $infotech->setHauteur(0);
                    $infotech->setPoids(0);
                    $infotech->setLargeur(0);
                    $infotech->setProfondeur(0);
                    $infotech->setLongueur(0);
                    $infotech->setCompatibilite("");
                    $infotech->setDureeVie(0);
                    $infotech->setPuissSon(0);

                    $produit->setInfomarket($infomarket);
                    $produit->setInfotech($infotech);

                    $alimentation = $alims->findOneBy(['libelle' => (string)$data['Alimentation']]);

                    $infotech->setInfoalim($alimentation);
                    // On persiste l'objet

                    $produit->setIsArchived(false);
                    $em->persist($produit);
                    $em->persist($infomarket);
                    $em->persist($infotech);
                    $em->flush();


            } 

            $this->addFlash('success', 'Importation réussie');
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/import.html.twig', [
            'form' => $form->createView(),
        
        ]);

    }

    
    #[Route('/', name: 'app_produit_index', methods: ['GET', 'POST'])]
    public function index(ProduitRepository $produitRepository, PaginatorInterface $paginator, Request $request): Response
    {


        $user = $this->security->getUser();
        $form = $this->createForm(SearchProductFormType::class);

        // On vérifie si le formulaire est vide


        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $form->get('q')->getData();
            $categ = $form->get('categories')->getData();
            $archive = $form->get('archive')->getData();

            $donnees_produit = $produitRepository->findBySearch($nom, $categ, $archive);

            $produits = $paginator->paginate(
                $donnees_produit,
                $request->query->getInt('page', 1),
                10
            );

            return $this->render('produit/index.html.twig', [
                'produits' => $produits,
                'form' => $form->createView(),
            ]);
        }

        
        $donnees = $produitRepository->findAll();

        $produit = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),10
        );

        return $this->render('produit/index.html.twig', [
            'form' => $form->createView(),
            'produits' => $produit,
        ]);
    }

    #[Route('/nouveau', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProduitRepository $produitRepository, EntityManagerInterface $em, ProduitPieceRepository $produitPieceRepo): Response
    {
        if ($this->getUser()->getService()->getLibelle() == "Product Owner" || $this->getUser()->getRoles()[0] == "ROLE_ADMIN")
        {
            $aa = "";
        }
        else{
            throw $this->createAccessDeniedException();
        }

        $produit = new Produit();
        $infotech = new InfoTechnique();
        $infomarket = new InfoMarketing();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        $form2 = $this->createForm(InfoTechniqueFormType::class, $infotech);
        $form2->handleRequest($request);
        $form3 = $this->createForm(InfoMarketingFormType::class, $infomarket);
        $form3->handleRequest($request);
        $form4 = $this->createForm(PiecesFormType::class);
        $form4->handleRequest($request);

        $pieces = $produitPieceRepo->findBy(['produit' => $produit]);

        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère les images 

            $images = $form->get('images')->getData();


            foreach ($images as $image) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $fichier
                    );
                } catch (FileException $e) {
                }

                $img = new Images();
                $img->setName($fichier);
                $produit->addImage($img);
            }


         
            $produit->setInfotech($infotech);

            $produit->setInfomarket($infomarket);

            $em->persist($produit);
            $em->persist($infotech);
            $em->persist($infomarket);

            $em->flush();

            
          //  $produitRepository->save($produit, true);

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'form2'=>$form2,
            'form3'=>$form3,
            'form4'=>$form4,
            'pieces'=>$pieces,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit, ProduitRepository $ProduitRepo, Request $request, ProduitPieceRepository $prodpieceRepo): Response
    {

        $produit = $ProduitRepo->find($produit->getId());
        $infotech = $produit->getInfotech();
        $infomarket = $produit->getInfomarket();

        $form = $this->createForm(ProduitType::class, $produit);
        $form2 = $this->createForm(InfoTechniqueFormType::class, $infotech);
        $form3 = $this->createForm(InfoMarketingFormType::class, $infomarket);
        $form->handleRequest($request);
        $form2->handleRequest($request);
        $form3->handleRequest($request);
        $pieces = $prodpieceRepo->findBy(['produit' => $produit->getId()]);


        return $this->render('produit/show.html.twig', [
            'showProductForm' => $form->createView(),
            'form2'=> $form2->createView(), 
            'form3' => $form3->createView(),
            'produit' => $produit,
            'pieces' => $pieces,
        ]);
    }

    #[Route('/{id}/edit', name: 'produit_edit', methods: ['GET', 'POST'])]
    public function edit(TranslatorInterface $translator, Request $request, Produit $produit, ProduitRepository $produitRepository, EntityManagerInterface $em, ProduitPieceRepository $piecesRepo): Response
    {
        $infotech = $produit->getInfotech();
        $infomarket = $produit->getInfomarket();

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        $form2 = $this->createForm(InfoTechniqueFormType::class, $infotech);
        $form2->handleRequest($request);

        $form3 = $this->createForm(InfoMarketingFormType::class, $infomarket);
        $form3->handleRequest($request);

        $form4 = $this->createForm(PiecesFormType::class);
        $form4->handleRequest($request);
        /* Si le collectionType est submitted */

        $piecess = $piecesRepo->findBy(['produit' => $produit->getId()]);
        
        if ($form->isSubmitted() && $form->isValid()) {


            
            
            $images = $form->get('images')->getData();
            $docs = $form->get('documentsProduit')->getData();
            $pieces = $form4->get('pieces')->getData();
            $qte = $form4->get('quantite')->getData();

            $piece = new ProduitPiece();
           if ($qte != null) {

               // on vérifie si la pièce existe déjà dans la base de données
                $piece = $piecesRepo->findOneBy(['produit' => $produit->getId(), 'piece' => $pieces->getId()]);

                if ($piece == null) {
                    $piece = new ProduitPiece();
                    $piece->setProduit($produit);
                    $piece->setPiece($pieces);
                    $piece->setQuantite($qte);
                    $em->persist($piece);
                }
                else{
                    $piece->setQuantite($qte);
                    $em->persist($piece);
                }
                

               if ($piece->getPiece() != null && $piece->getQuantite() != null) {
                   $em->persist($piece);
                   $em->flush();
                   return $this->redirectToRoute('produit_edit', ['id' => $produit->getId()]);
               }
           }


            if ($images != null || $docs != null) $this->addImage($infotech, $infomarket,$translator, $images,$docs, $request, $produit, $em);
            else {
                $produit->setInfotech($infotech);
                $produit->setInfomarket($infomarket);


                $em->persist($produit);
                $em->persist($infotech);
                $em->persist($infomarket);
                $em->flush();
                return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'form2'=>$form2,
            'form3'=>$form3,
            'form4'=>$form4,
            'pieces'=>$piecess,
           
        ]);
    }

    public function addImage(InfoTechnique $infotech,InfoMarketing $infomarket ,TranslatorInterface $translator, $images,$docs ,Request $request, Produit $produit, EntityManagerInterface $em): Response
    {

            if ($docs != null) {
                    $nomFichier = pathinfo($docs->getClientOriginalName(), PATHINFO_FILENAME);
                    // On génère un nouveau nom de fichier : ID du produit + nom du fichier
                    $fichier = $produit->getId() . '-' . $nomFichier . '.' . $docs->guessExtension();
                    try {
                        $docs->move(
                            $this->getParameter('documents_directory'),
                            $fichier
                        );
                    } catch (FileException $e) {
                    }

                    $docu = new DocumentProduit();
                    $docu->setName($fichier);
                    $produit->addDocumentProduit($docu);

            }



            if ($images != null)
            {
                foreach ($images as $image) {
                    $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                    try {
                        $image->move(
                            $this->getParameter('images_directory'),
                            $fichier
                        );
                    } catch (FileException $e) {
                    }

                    $img = new Images();
                    $img->setName($fichier);
                    $produit->addImage($img);

                }
            }


            $produit->setInfotech($infotech);
            $produit->setInfomarket($infomarket);
            $em->persist($produit);
            $em->persist($infotech);
            $em->persist($infomarket);
            $em->flush();
            return $this->redirectToRoute('produit_edit', ['id' => $produit->getId()], Response::HTTP_SEE_OTHER);
    }



    #[Route('/delete/{id}', name: 'app_produit_delete')]
    public function delete(Request $request, Produit $produit, ProduitRepository $produitRepository, EntityManagerInterface $em): Response
    {
       /*$produit = $produitRepository->find($produit->getId());

        $images = $produit->getImages();

        foreach ($images as $image) {
            $nom = $image->getName();
            unlink($this->getParameter('images_directory').'/'.$nom);
        }

        $em->remove($produit);*/

        // On ne supprime pas le produit mais on change le booléen isArchived à true

        $produit->setIsArchived(true);
        $em->persist($produit);
        $em->flush();
        $this->addFlash('success', 'Produit archivé avec succès !');
            

        

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/undelete/{id}', name: 'app_produit_undelete')]
    public function undelete(Request $request, Produit $produit, ProduitRepository $produitRepository, EntityManagerInterface $em): Response
    {

        $produit->setIsArchived(false);
        $em->persist($produit);
        $em->flush();
        $this->addFlash('success', 'Produit désarchivé avec succès !');
        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }



    


    /**
 * @Route("/supprime/image/{id}", name="produit_delete_image", methods={"DELETE"})
 */
    public function deleteImage(Images $image, Request $request, EntityManagerInterface $em){
    $data = json_decode($request->getContent(), true);

    // On vérifie si le token est valide
    if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
        // On récupère le nom de l'image
        $nom = $image->getName();
        // On supprime le fichier
        unlink($this->getParameter('images_directory').'/'.$nom);

        // On supprime l'entrée de la base
       
        $em->remove($image);
        $em->flush();

        // On répond en json
        return new JsonResponse(['success' => 1]);
    }else{
        return new JsonResponse(['error' => 'Token Invalide'], 400);
    }
}


    /**
     * @Route("/supprime/docs/{id}", name="produit_delete_doc", methods={"DELETE"})
     */
    public function deleteDocument(DocumentProduit $doc, Request $request, EntityManagerInterface $em){
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$doc->getId(), $data['_token'])){
            // On récupère le nom de l'image
            $nom = $doc->getName();
            // On supprime le fichier
            unlink($this->getParameter('documents_directory').'/'.$nom);

            // On supprime l'entrée de la base

            $em->remove($doc);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
    /**
     * @Route("/supprime/pieces/{id}", name="produit_delete_pieces")
     */
    public function deletePiece(ProduitPiece $piece, Request $request, EntityManagerInterface $em){
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
            $em->remove($piece);
            $em->flush();
            return $this->redirectToRoute('produit_edit', ['id' => $piece->getProduit()->getId()], Response::HTTP_SEE_OTHER);
    }



   






}
