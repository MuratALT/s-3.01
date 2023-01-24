<?php

namespace App\Form;

use App\Entity\Vente;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class VenteFormType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('categorie', EntityType::class, [
            'attr' => [ 
                'class' => 'form-control',
                'style' => 'color : black ;',
                
            ],
            'class' => Categorie::class,
            'choice_label' => 'libelle',
            'constraints' => [
                new NotBlank([
                    'message' => $this->translator->trans('Veuillez renseigner une catégorie'),
                ]),
            ],
        ]) 
        ->add('libelle',TextType::class,[
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir un libellé'),
                    'class' => 'form-control',
                ],
                'required'=>true
        ])
        ->add('commentaire', CKEditorType::class, [
            "required" => true ,
            'attr' => [
                'placeholder' => $this->translator->trans('Entrez un commentaire...'),
                'class' => 'form-control',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => $this->translator->trans('Veuillez renseigner un commentaire'),
                ]),
            ],
        ])
        ->add('documentsVente', FileType::class, [
            'attr' => [
                'class' => 'form-control',
                'accept' => '.pdf',
            ],
            'required' => false,

            // Uniquement les fichiers XLSX
            'data_class' => null,
            'mapped' => false,
            'label' => $this->translator->trans('Documents'),
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'application/pdf',

                    ],
                    'mimeTypesMessage' => 'Veuillez sélectionner un fichier pdf valide',
                ])
            ],

        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vente::class,
        ]);
    }
}