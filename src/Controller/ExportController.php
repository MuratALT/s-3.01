<?php

namespace App\Controller;

use App\Form\SearchProductFormType;
use App\Form\SearchSaleFormType;
use App\Repository\ProduitRepository;
use App\Repository\VenteRepository;
use App\Repository\PieceRepository;
use App\Repository\TicketRepository;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExportController extends AbstractController
{
    #[Route('/export/produit', name: 'app_export_produit')]
    public function indexProduit(ProduitRepository $produitRepository, PaginatorInterface $paginator, Request $request): Response
    {

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

            return $this->render('produit/export.html.twig', [
                'form' => $form->createView(),
                'produits' => $produits,
            ]);
        }




        $donnees = $produitRepository->findAll();

        $produit = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),10
        );

        return $this->render('produit/export.html.twig', [
            'form' => $form->createView(),
            'produits' => $produit,
        ]);
    }

    /**
     * @Route("/export/produits", name="export_produits")
     */
    public function ExportProduit(Request $request, ProduitRepository $produitRepository, TranslatorInterface $translator)
    {



        // On récupère les données du formulaire
        $data = $request->request->get("checked_items");
        // On récupère tous les ID des produis à exporter
        $ids = explode(",", $data);

        if ($ids[0] == "")
        {
            $this->addFlash("error",$translator->trans("Veuillez sélectionner au moins un produit").'.');
            return $this->redirectToRoute('app_export_produit');
        }

        $produits = [];
        for ($i = 0; $i < count($ids); $i++) {
            $produits[] = $produitRepository->find($ids[$i]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $translator->trans("Référence"));
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->setCellValue('B1', $translator->trans("Libellé"));
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->setCellValue('C1', $translator->trans("Libellé DE"));
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->setCellValue('D1', $translator->trans("Libellé EN"));
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->setCellValue('E1', $translator->trans('Prix unitaire'));
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->setCellValue('F1', $translator->trans('Catégorie'));
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->setCellValue('G1',$translator->trans("Garantie"));
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->setCellValue('H1',$translator->trans("Est archivé"));
        $sheet->getColumnDimension('H')->setWidth(15);

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:H1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->setTitle('Produits');



        // On va créer une seconde feuille réservée aux Infos Marketing

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet();
        $sheet2->setTitle('Infos Marketing');

        $sheet2->setCellValue('A1', $translator->trans("Référence"));
        $sheet2->getColumnDimension('A')->setWidth(20);
        $sheet2->setCellValue('B1', $translator->trans("Description"));
        $sheet2->getColumnDimension('B')->setWidth(100);
        $sheet2->setCellValue('C1', $translator->trans("Fonctionnalités"));
        $sheet2->getColumnDimension('C')->setWidth(100);
        $sheet2->setCellValue('D1', $translator->trans("Bénéfices"));
        $sheet2->getColumnDimension('D')->setWidth(100);
        $sheet2->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet2->getStyle('A1:D1')->getAlignment()->setHorizontal('center');
        $sheet2->getStyle('A1:D1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);


        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet3 = $spreadsheet->getActiveSheet();
        $sheet3->setTitle('Infos Techniques');

        $sheet3->setCellValue('A1', $translator->trans("Référence"));
        $sheet3->getColumnDimension('A')->setWidth(20);
        $sheet3->setCellValue('B1', $translator->trans("Durée de vie"));
        $sheet3->getColumnDimension('B')->setWidth(30);
        $sheet3->setCellValue('C1', $translator->trans("Compatibilité"));
        $sheet3->getColumnDimension('C')->setWidth(40);
        $sheet3->setCellValue('D1', $translator->trans("Hauteur"));
        $sheet3->getColumnDimension('D')->setWidth(15);
        $sheet3->setCellValue('E1', $translator->trans("Longueur"));
        $sheet3->getColumnDimension('E')->setWidth(15);
        $sheet3->setCellValue('F1', $translator->trans("Largeur"));
        $sheet3->getColumnDimension('F')->setWidth(15);
        $sheet3->setCellValue('G1', $translator->trans("Profondeur"));
        $sheet3->getColumnDimension('G')->setWidth(15);
        $sheet3->setCellValue('H1', $translator->trans("Poids"));
        $sheet3->getColumnDimension('H')->setWidth(15);
        $sheet3->setCellValue('I1', $translator->trans("Puissance sonore"));
        $sheet3->getColumnDimension('I')->setWidth(20);
        $sheet3->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet3->getStyle('A1:I1')->getAlignment()->setHorizontal('center');
        $sheet3->getStyle('A1:I1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);


        for ($i = 0; $i < count($produits); $i++) {
            $sheet->setCellValue('A' . ($i + 2), $produits[$i]->getReference());
            $sheet->setCellValue('B' . ($i + 2), $produits[$i]->getLibelle());
            $sheet->setCellValue('C' . ($i + 2), $produits[$i]->getLabelDE());
            $sheet->setCellValue('D' . ($i + 2), $produits[$i]->getLabelEN());
            $sheet->setCellValue('E' . ($i + 2), $produits[$i]->getPu());
            $sheet->setCellValue('F' . ($i + 2), $produits[$i]->getTypeProd()->getLibelle());
            $sheet->setCellValue('G' . ($i + 2), $produits[$i]->getGarantie());
            $sheet->setCellValue('H' . ($i + 2), $produits[$i]->isIsArchived());

            $infoMarketing = $produits[$i]->getInfomarket();
            $sheet2->setCellValue('A' . ($i + 2), $produits[$i]->getReference());
            // On redirige vers la première feuille
            $link = new Hyperlink();
            $link->setUrl("sheet://'Produits'!A" . ($i + 2));
            $sheet2->getCell('A'.($i + 2))->setHyperlink($link);

            // On enlève les balises HTML
            $sheet2->setCellValue('B' . ($i + 2), strip_tags($infoMarketing->getDescription()));
            // Afficher tous les caractères dans la cellule
            $sheet2->getStyle('B'.($i + 2))->getAlignment()->setWrapText(true);
            // Largeur de la ligne
            $sheet2->getRowDimension($i + 2)->setRowHeight(50);
            // Vertical alignement : center
            $sheet2->getStyle('B'.($i + 2))->getAlignment()->setVertical('center');
            $sheet2->setCellValue('C' . ($i + 2), strip_tags($infoMarketing->getFonctionnalites()));
            $sheet2->getStyle('C'.($i + 2))->getAlignment()->setWrapText(true);
            $sheet2->getStyle('C'.($i + 2))->getAlignment()->setVertical('center');
            $sheet2->setCellValue('D' . ($i + 2), strip_tags($infoMarketing->getBenefices()));
            $sheet2->getStyle('D'.($i + 2))->getAlignment()->setVertical('center');
            $sheet2->getStyle('D'.($i + 2))->getAlignment()->setVertical('center');


            $infoTech = $produits[$i]->getInfotech();
            $sheet3->setCellValue('A' . ($i + 2), $produits[$i]->getReference());
            // On redirige vers la première feuille
            $link = new Hyperlink();
            $link->setUrl("sheet://'Produits'!A" . ($i + 2));
            $sheet3->getCell('A'.($i + 2))->setHyperlink($link);

            $sheet3->setCellValue('B' . ($i + 2), $infoTech->getDureeVie());
            $sheet3->setCellValue('C' . ($i + 2), $infoTech->getCompatibilite());
            $sheet3->setCellValue('D' . ($i + 2), $infoTech->getHauteur());
            $sheet3->setCellValue('E' . ($i + 2), $infoTech->getLongueur());
            $sheet3->setCellValue('F' . ($i + 2), $infoTech->getLargeur());
            $sheet3->setCellValue('G' . ($i + 2), $infoTech->getProfondeur());
            $sheet3->setCellValue('H' . ($i + 2), $infoTech->getPoids());
            $sheet3->setCellValue('I' . ($i + 2), $infoTech->getPuissSon());

            // On met en forme les cellules
            $sheet->getStyle('A' . ($i + 2) . ':I' . ($i + 2))->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . ($i + 2) . ':I' . ($i + 2))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet2->getStyle('A' . ($i + 2) . ':D' . ($i + 2))->getAlignment()->setHorizontal('center');
            $sheet2->getStyle('A' . ($i + 2) . ':D' . ($i + 2))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet3->getStyle('A' . ($i + 2) . ':I' . ($i + 2))->getAlignment()->setHorizontal('center');
            $sheet3->getStyle('A' . ($i + 2) . ':I' . ($i + 2))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // On met en Grans les colonnes A:
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet2->getColumnDimension('A')->setAutoSize(true);
            $sheet3->getColumnDimension('A')->setAutoSize(true);

            // On met en gras les colonnes A:
            $sheet->getStyle('A' . ($i + 2) . ':A' . ($i + 2))->getFont()->setBold(true);
            $sheet2->getStyle('A' . ($i + 2) . ':A' . ($i + 2))->getFont()->setBold(true);
            $sheet3->getStyle('A' . ($i + 2) . ':A' . ($i + 2))->getFont()->setBold(true);

            //On bloque la colonne A pour qu'elle soit toujours visible
            $sheet->freezePane('B1');
            $sheet2->freezePane('B1');
            $sheet3->freezePane('B1');


        }



        $writer = new Xlsx($spreadsheet);
        $filename = 'export' . date('d-m-Y_H-i-s') . '.xlsx';
        $writer->save($filename);
        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->deleteFileAfterSend(true);
        return $response;


    }

    #[Route('/export/vente', name: 'app_export_vente')]
    public function indexVente(VenteRepository $venteRepository, PaginatorInterface $paginator, Request $request): Response
    {

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

            return $this->render('sale/export.html.twig', [
                'form' => $form->createView(),
                'ventes' => $ventes,
            ]);
        }

        $donnees = $venteRepository->findAll();

        $vente = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),10
        );

        return $this->render('sale/export.html.twig', [
            'ventes' => $vente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/export/ventes", name="export_ventes")
     */
    public function ExportVente(Request $request, VenteRepository $venteRepository, TranslatorInterface $translator)
    {



        // On récupère les données du formulaire
        $data = $request->request->get("checked_items");
        // On récupère tous les ID des produis à exporter
        $ids = explode(",", $data);

        if ($ids[0] == "")
        {
            $this->addFlash("error",$translator->trans("Veuillez sélectionner au moins un produit").'.');
            return $this->redirectToRoute('app_export_vente');
        }

        $ventes = [];
        for ($i = 0; $i < count($ids); $i++) {
            $ventes[] = $venteRepository->find($ids[$i]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $translator->trans("Catégorie"));
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->setCellValue('B1', $translator->trans("Libellé"));
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->setCellValue('C1', $translator->trans('Commentaire'));
        $sheet->getColumnDimension('C')->setWidth(50);
        

        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:C1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->setTitle('Ventes');

        // On met en forme les cellules
        

        for ($i = 0; $i < count($ventes); $i++) {
            $sheet->setCellValue('A' . ($i + 2), $ventes[$i]->getCategorie()->getLibelle());
            $sheet->setCellValue('B' . ($i + 2), $ventes[$i]->getLibelle());
            $sheet->setCellValue('C' . ($i + 2), strip_tags($ventes[$i]->getCommentaire()));

            $sheet->getStyle('C'.($i + 2))->getAlignment()->setWrapText(true);
            $sheet->getStyle('C'.($i + 2))->getAlignment()->setVertical('center');

            $sheet->getStyle('A' . ($i + 2) . ':C' . ($i + 2))->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . ($i + 2) . ':C' . ($i + 2))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
            // On met en Grans les colonnes A:
            $sheet->getColumnDimension('A')->setAutoSize(true);

            //On bloque la colonne A pour qu'elle soit toujours visible
            $sheet->freezePane('B1');
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'export' . date('d-m-Y_H-i-s') . '.xlsx';
        $writer->save($filename);
        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->deleteFileAfterSend(true);
        return $response;


    }

    #[Route('/export/piece', name: 'app_export_piece')]
    public function indexPiece(PieceRepository $pieceRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $donnees = $pieceRepository->findAll();

        $piece = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),10
        );

        return $this->render('spare_parts/export.html.twig', [
            'pieces' => $piece,
        ]);
    }

    /**
     * @Route("/export/pieces", name="export_pieces")
     */
    public function ExportPiece(Request $request, PieceRepository $pieceRepository, TranslatorInterface $translator)
    {



        // On récupère les données du formulaire
        $data = $request->request->get("checked_items");
        // On récupère tous les ID des produis à exporter
        $ids = explode(",", $data);

        if ($ids[0] == "")
        {
            $this->addFlash("error",$translator->trans("Veuillez sélectionner au moins une pièce détachée").'.');
            return $this->redirectToRoute('app_export_piece');
        }

        $pieces = [];
        for ($i = 0; $i < count($ids); $i++) {
            $pieces[] = $pieceRepository->find($ids[$i]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $translator->trans("Libellé"));
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->setCellValue('B1', $translator->trans("Longueur"));
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->setCellValue('C1', $translator->trans('Profondeur'));
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->setCellValue('D1', $translator->trans('Hauteur'));
        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->setCellValue('E1', $translator->trans('Poids'));
        $sheet->getColumnDimension('E')->setWidth(50);


        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:E1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->setTitle('Pieces');

        
        for ($i = 0; $i < count($pieces); $i++) {
            $sheet->setCellValue('A' . ($i + 2), $pieces[$i]->getLibelle());
            $sheet->setCellValue('B' . ($i + 2), $pieces[$i]->getLongueur());
            $sheet->setCellValue('C' . ($i + 2), $pieces[$i]->getProfondeur());
            $sheet->setCellValue('D' . ($i + 2), $pieces[$i]->getHauteur());
            $sheet->setCellValue('E' . ($i + 2), $pieces[$i]->getPoids());

            // On met en forme les cellules
            $sheet->getStyle('A' . ($i + 2) . ':E' . ($i + 2))->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . ($i + 2) . ':E' . ($i + 2))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
            // On met en Grans les colonnes A:
            $sheet->getColumnDimension('A')->setAutoSize(true);

            //On bloque la colonne A pour qu'elle soit toujours visible
            $sheet->freezePane('B1');
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'export' . date('d-m-Y_H-i-s') . '.xlsx';
        $writer->save($filename);
        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->deleteFileAfterSend(true);
        return $response;


    }

    #[Route('/export/ticket', name: 'app_export_ticket')]
    public function indexTicket(TicketRepository $ticketRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $donnees = $ticketRepository->findAll();

        $ticket = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),10
        );

        return $this->render('ticket/export.html.twig', [
            'tickets' => $ticket,
        ]);
    }

    /**
     * @Route("/export/tickets", name="export_tickets")
     */
    public function ExportTicket(Request $request, TicketRepository $ticketRepository, TranslatorInterface $translator)
    {



        // On récupère les données du formulaire
        $data = $request->request->get("checked_items");
        // On récupère tous les ID des produis à exporter
        $ids = explode(",", $data);

        if ($ids[0] == "")
        {
            $this->addFlash("error",$translator->trans("Veuillez sélectionner au moins un ticket").'.');
            return $this->redirectToRoute('app_export_ticket');
        }

        $tickets = [];
        for ($i = 0; $i < count($ids); $i++) {
            $tickets[] = $ticketRepository->find($ids[$i]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $translator->trans("Client"));
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->setCellValue('B1', $translator->trans("Produit"));
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->setCellValue('C1', $translator->trans('Commentaire'));
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->setCellValue('D1', $translator->trans('Statut'));
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->setCellValue('E1', $translator->trans('Date de création'));
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->setCellValue('F1', $translator->trans('Date de prise en charge'));
        $sheet->getColumnDimension('F')->setWidth(50);
        $sheet->setCellValue('G1', $translator->trans('Date de clôture'));
        $sheet->getColumnDimension('G')->setWidth(50);


        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:G1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->setTitle('Tickets');

        for ($i = 0; $i < count($tickets); $i++) {
            $sheet->setCellValue('A' . ($i + 2), $tickets[$i]->getUser()->getNom());
            $sheet->setCellValue('B' . ($i + 2), $tickets[$i]->getProduit()->getLibelle());
            $sheet->setCellValue('C' . ($i + 2), html_entity_decode(strip_tags($tickets[$i]->getCommentaire())));
            $sheet->setCellValue('D' . ($i + 2), $tickets[$i]->getStatus());
            $sheet->setCellValue('E' . ($i + 2), $tickets[$i]->getDateCreation());

            if ($tickets[$i]->getDatePrise()!=null)
            {
                $sheet->setCellValue('F' . ($i + 2), $tickets[$i]->getDatePrise());
            }
            else
            {
                $sheet->setCellValue('F' . ($i + 2), $translator->trans("Non pris en charge"));
            }
            if ($tickets[$i]->getDateResolution()!=null)
            {
                $sheet->setCellValue('G' . ($i + 2), $tickets[$i]->getDateResolution());
            }
            else
            {
                $sheet->setCellValue('G' . ($i + 2), $translator->trans("Non clos"));
            }

            $sheet->getStyle('C'.($i + 2))->getAlignment()->setVertical('center');

            // On met en forme les cellules
            $sheet->getStyle('A' . ($i + 2) . ':G' . ($i + 2))->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . ($i + 2) . ':G' . ($i + 2))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
            // On met en Grans les colonnes A:
            $sheet->getColumnDimension('A')->setAutoSize(true);

            //On bloque la colonne A pour qu'elle soit toujours visible
            $sheet->freezePane('B1');
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'export' . date('d-m-Y_H-i-s') . '.xlsx';
        $writer->save($filename);
        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->deleteFileAfterSend(true);
        return $response;


    }

}
