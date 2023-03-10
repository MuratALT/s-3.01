<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Entity\Vente;
use App\Entity\DocumentVente;
use App\Repository\VenteRepository;
use App\Form\ImportType;
use App\Form\VenteFormType;
use App\Form\DocumentsVenteFormType;
use App\Form\CategorieFormType;
use App\Form\SearchSaleFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Validator\Constraints\NotBlank;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class AccountingController extends AbstractController
{
    public function __construct(TranslatorInterface $translator, Security $security)
    {
        $this->translator = $translator;
        $this->security = $security;
    }
    
    #[Route('/categorie', name: 'app_categorie_index', methods: ['GET'])]
    public function indexCategorie(CategorieRepository $categorieRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if ($this->getUser()->getService()->getLibelle() != "Comptabilit√©")
        {
            throw $this->createAccessDeniedException();
        }

        $donnees = $categorieRepository->findAll();
        
        $categorie = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),15
        );

        return $this->render('category/index.html.twig', [
            'categories' => $categorie,
        ]);
    }

    #[Route('/categorie/nouveau', name: 'app_categorie_new', methods: ['GET', 'POST'])]
    #[Route('/categorie/{id}/edit', name: 'app_categorie_edit', methods: ['GET', 'POST'])]

    public function newCategorie(Categorie $categorie = null , TranslatorInterface $translator , Request $request, CategorieRepository $categorieRepository, EntityManagerInterface $em): Response
    {
        if ($this->getUser()->getService()->getLibelle() != "Comptabilit√©")
        {
            throw $this->createAccessDeniedException();
        }

        if(!$categorie){
            $categorie = new Categorie();
        }
        $form = $this->createFormBuilder($categorie)
            ->add('libelle',TextType::class,[
                    'attr' => [
                        'placeholder' => $this->translator->trans('Saisir un libell√©'),
                        'class' => 'form-control',
                    ],
                    'label'=> $translator->trans('Lib√©ll√©'),
                    'required'=>true
            ])             
            ->getForm();

   $form->handleRequest($request);

   if($form->isSubmitted() && $form->isValid())
   {
            if($categorieRepository->findOneBy(['libelle' => $categorie->getLibelle()]) != null && $categorie->getId() == null){
                $this->addFlash('error',  $translator->trans("Le libell√© existe d√©j√† !"));
            }
            else
            {
                {

                    if($categorie->getId() == null)
                    {
                        $this->addFlash('success',  $translator->trans("La cat√©gorie a bien √©t√© cr√©√©e !"));
                    }
                    else
                    {
                        $this->addFlash('success',  $translator->trans("La cat√©gorie a bien √©t√© modifi√©e !"));
                    }
                
                    $em->persist($categorie);
                    $em->flush();
                    
                    return $this->redirectToRoute("app_categorie_index");
                }
                
                
    }}

        return $this->render('category/new.html.twig',[
            'form' => $form->createView(),
            'editMode' => $categorie->getId() !== null
        ]);
    }

    #[Route('/categorie/{id}/delete', name: 'app_categorie_delete', methods: ['GET', 'POST'])]

    public function deleteCategorie(Categorie $categorie, EntityManagerInterface $em, TranslatorInterface $translator): Response {
        if ($this->getUser()->getService()->getLibelle() != "Comptabilit√©")
        {
            throw $this->createAccessDeniedException();
        }
        $em->remove($categorie);
        $em->flush();
        $this->addFlash('success', $translator->trans("La cat√©gorie a bien √©t√© supprim√©e !"));
        return $this->redirectToRoute('app_categorie_index');
    }

    // Les Ventes

    #[Route('/vente', name: 'app_vente_index', methods: ['GET', 'POST'])]
    public function index(VenteRepository $venteRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if ($this->getUser()->getService()->getLibelle() != "Comptabilit√©")
        {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(SearchSaleFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $libelle = $form->get('q')->getData();
            $categ = $form->get('categories')->getData();

            $donnees_vente = $venteRepository->findBySearch($libelle, $categ);

            $ventes = $paginator->paginate(
                $donnees_vente,
                $request->query->getInt('page', 1),
                10
            );

            return $this->render('sale/index.html.twig', [
                'ventes' => $ventes,
                'form' => $form->createView(),
            ]);
        }

        $donnees = $venteRepository->findAll();

        $vente = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),15
        );

        return $this->render('sale/index.html.twig', [
            'ventes' => $vente,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/vente/nouveau', name: 'app_vente_new', methods: ['GET', 'POST'])]
    public function new(Vente $vente = null , TranslatorInterface $translator , Request $request, VenteRepository $venteRepository, EntityManagerInterface $em): Response
    {
        if ($this->getUser()->getService()->getLibelle() != "Comptabilit√©")
        {
            throw $this->createAccessDeniedException();
        }

        if (!$vente) {
            $vente = new Vente();
        }
        
        $form = $this->createForm(VenteFormType::class, $vente);
        $form->handleRequest($request);

        $documentuser = new DocumentVente();

        $form2 = $this->createForm(DocumentsVenteFormType::class, $documentuser);
        $form2->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if($venteRepository->findOneBy(['libelle' => $vente->getLibelle()]) != null && $vente->getId() == null)
                $this->addFlash('error',  $translator->trans("Le libell√© existe d√©j√† !"));
            else
            {
                $document = $form->get('documentsVente')->getData();
                $nom_fichier = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
                $fichier = $vente->getCategorie()->getLibelle() . '-' . $vente->getLibelle() . '-' . $nom_fichier . '.' . $document->guessExtension();
                    
                    $libelle = $form2->get('libelle')->getData();
                    $commentaire = $form2->get('commentaire')->getData() != null ? $form2->get('commentaire')->getData() : "";

                    try {
                        // On copie le fichier dans le dossier uploads
                        $document->move(
                            $this->getParameter('sale_documents_directory'),
                            $fichier
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $doc = new DocumentVente();
                    $doc->setName($fichier);

                    /* set libell√© */
                    $doc->setLibelle($libelle);

                    /* set commentaire */
                    $doc->setCommentaire($commentaire);

                    /* set date_ajout */
                    $doc->setDateAjout(new \DateTime());
                    // On met √† jour le nom du fichier dans la base de donn√©es
                    $vente->addDocumentVente($doc);
                }
                    $this->addFlash('success',  $translator->trans("La vente a bien √©t√© cr√©√©e !"));
                    $em->persist($vente);
                    $em->flush();
                    return $this->redirectToRoute("app_vente_show", ['id' => $vente->getId()]);

        }


        return $this->render('sale/new.html.twig',[
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'vente' => $vente,
        ]);
    }

    #[Route('/vente/{id}/edit', name: 'app_vente_edit', methods: ['GET', 'POST'])]
    public function edit(TranslatorInterface $translator, Request $request, Vente $vente, VenteRepository $venteRepository, EntityManagerInterface $em): Response
    {

        if ($this->getUser()->getService()->getLibelle() != "Comptabilit√©")
        {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(VenteFormType::class, $vente);
        $form->handleRequest($request);

        $documentuser = new DocumentVente();

        $form2 = $this->createForm(DocumentsVenteFormType::class, $documentuser);
        $form2->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $document = $form->get('documentsVente')->getData();
            // On v√©rifie si un document a √©t√© upload


                // On g√©n√®re un nouveau nom de fichier
            if($document != null){
                $nom_fichier = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
                $fichier = $vente->getCategorie()->getLibelle() . '-' . $vente->getLibelle() . '-' . $nom_fichier . '.' . $document->guessExtension();
            
            
                $libelle = $form2->get('libelle')->getData();
                $commentaire = $form2->get('commentaire')->getData() != null ? $form2->get('commentaire')->getData() : "";

                try {
                    // On copie le fichier dans le dossier uploads
                    $document->move(
                        $this->getParameter('sale_documents_directory'),
                        $fichier
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $doc = new DocumentVente();
                $doc->setName($fichier);

                /* set libell√© */
                $doc->setLibelle($libelle);

                /* set commentaire */
                $doc->setCommentaire($commentaire);

                /* set date_ajout */
                $doc->setDateAjout(new \DateTime());
                // On met √† jour le nom du fichier dans la base de donn√©es
                $vente->addDocumentVente($doc);
            }
                $em->persist($vente);
                $em->flush();
                $this->addFlash('success', 'Les modifications ont bien √©t√© enregistr√©es');
                return $this->redirectToRoute('app_vente_show', ['id' => $vente->getId()]);
        }


        return $this->renderForm('sale/edit.html.twig', [
            'form' => $form,
            'form2' => $form2,
            'vente' => $vente,
        ]);
    }

    #[Route('/vente/{id}', name: 'app_vente_show', methods: ['GET'])]
    public function show(Vente $vente, VenteRepository $venteRepository, Request $request): Response
    {

        $vente = $venteRepository->find($vente->getId());
        $categ = $vente->getCategorie();

        $form = $this->createForm(VenteFormType::class, $vente);
        $form->handleRequest($request);

        return $this->render('sale/show.html.twig', [
            'VenteForm' => $form->createView(),
            'vente' => $vente,
        ]);
    }

    #[Route('/vente/{id}/delete', name: 'app_vente_delete', methods: ['GET', 'POST'])]

    public function delete(Vente $vente, EntityManagerInterface $em, TranslatorInterface $translator): Response {

        if ($this->getUser()->getService()->getLibelle() != "Comptabilit√©")
        {
            throw $this->createAccessDeniedException();
        }
        $em->remove($vente);
        $em->flush();
        $this->addFlash('success', $translator->trans("La vente a bien √©t√© supprim√©e !"));
        return $this->redirectToRoute('app_vente_index');
    }

    /**
     * @Route("/import/dl", name="sale_model")
     */

    public function saleModel(CategorieRepository $categories)
    {
        if ($this->getUser()->getService()->getLibelle() != "Comptabilit√©")
        {
            throw $this->createAccessDeniedException();
        }
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Cat√©gorie');
        // D√©finition de la largeur de la colonne
        $sheet->getColumnDimension('A')->setWidth(20);
        // D√©finition de la hauteur de la ligne
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->setCellValue('B1', 'Libelle');
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->setCellValue('C1', 'Commentaire');
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getRowDimension('1')->setRowHeight(30);

        $categoriess = $categories->findAll();


        $categoryNames = array_map(function ($category) {
            // On ajoute des gullets pour chaque cat√©gorie
            return '"' . $category->getLibelle() . '"';
        }, $categoriess);


        $protection = $sheet->getProtection();


        $dataValidation = $sheet->getCell('A2')->getDataValidation();

        $dataValidation->setType(DataValidation::TYPE_LIST)
            ->setAllowBlank(false)
            ->setShowDropDown(true)
            ->setFormula1(implode(',', $categoryNames));

        // Garder la majuscule pour les noms de colonnes
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);



        for ($row = 2; $row <= 20; $row++) {
            $sheet->getCell('A' . $row)->setDataValidation($dataValidation);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'model_import' . date('d-m-Y_H-i-s') . '.xlsx';
        $writer->save(__DIR__ . '/../../public/export/Model/' . $filename);
        $response = new BinaryFileResponse(__DIR__ . '/../../public/export/Model/' . $filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        // Suppression du fichier apr√®s t√©l√©chargement
        $response->deleteFileAfterSend(true);
        return $response;

    }

    #[Route('/vente/import/fichier', name: 'app_vente_import')]
    public function importSales(TranslatorInterface $translator, Request $request, VenteRepository $venteRepository, EntityManagerInterface $em, CategorieRepository $categs): Response
    {
        if ($this->getUser()->getService()->getLibelle() != "Comptabilit√©")
        {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            
            // On ne stocke pas le fichier sur le serveur

            // On r√©cup√®re le contenu du fichier

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            // On boucle sur les lignes du fichier

            $contenu = '';
            $i = 0;

           $headers = $sheetData[0];


            if ($headers !== ['Cat√©gorie', 'Libelle', 'Commentaire']) {
                $this->addFlash('error', $translator->trans('Le fichier XLSX est invalide, Il manque des colonnes').'.');
                return $this->redirectToRoute('app_vente_import');
            }


            $content = array_slice($sheetData, 1);

            $XLSX = array();
            foreach ($content as $row) {
                $XLSX[] = array_combine($headers, $row);
            }

            $errors = "";

            // On boucle sur les lignes du CSV pour v√©rifier qu'aucun champ n'est vide
            foreach ($XLSX as $data) {
                if (
                    (!((empty($data['Cat√©gorie']) &&
                        empty($data['Libelle']) &&
                        empty($data['Commentaire']))))
                    && (empty($data['Cat√©gorie']) || empty($data['Libelle']) || empty($data['Commentaire']))) {
                    $this->addFlash('error', $translator->trans('Le fichier CSV est invalide'));
                    return $this->redirectToRoute('app_vente_import');
                }
            }

            // On r√©cup√®re seulements les ventes ayant toutes les informations
            $res = [];
            foreach ($XLSX as $data)
            {
                if(!empty($data['Cat√©gorie']) && !empty($data['Libelle']) && !empty($data['Commentaire']))
                {
                    $res[] = $data;
                }
            }


            // On boucle sur les lignes du CSV

             foreach ($res as $data) {
                // On v√©rifie si la vente existe d√©j√†
                $vente = $venteRepository->findOneBy(['libelle' => $data['Libelle']]);
                    if (!$vente) {
                        $vente = new Vente();
                    }

                    $libelle = (string)$data['Libelle'];
                    $commentaire = (string)$data['Commentaire'];


                // On hydrate l'objet avec les donn√©es du CSV


                    $vente->setLibelle($libelle);
                    $vente->setCommentaire($commentaire);
                
                    $categ = $categs->findOneBy(['libelle' => (string)$data['Cat√©gorie']]);

                    $vente->setCategorie($categ);

                    $em->persist($vente);
                    $em->flush();


            } 

            $this->addFlash('success', 'Importation r√©ussie');
            return $this->redirectToRoute('app_vente_index');
        }

        return $this->render('sale/import.html.twig', [
            'form' => $form->createView(),
        
        ]);

    }

    #[Route('/vente/delete-document/{id}', name: 'app_vente_delete_document', methods: ['GET', 'POST'])]
    public function deleteDocument(Request $request, DocumentVente $documentVente, EntityManagerInterface $em): Response
    {
        if ($this->getUser()->getService()->getLibelle() != "Comptabilit√©")
        {
            throw $this->createAccessDeniedException();
        }

        $em->remove($documentVente);
        $em->flush();


        $this->addFlash('success', 'Le document a bien √©t√© supprim√©');
        return $this->redirectToRoute('app_vente_edit', ['id' => $documentVente->getVente()->getId()], Response::HTTP_SEE_OTHER);
    }

}