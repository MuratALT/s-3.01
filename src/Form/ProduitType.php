<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\InfoTechnique;
use App\Entity\Piece;
use App\Entity\Produit;
use App\Entity\Reglementation;
use App\Entity\TypeProd;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;

class ProduitType extends AbstractType
{
    private $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pu', NumberType::class, [
                'label' => $this->translator->trans('Prix unitaire'),
                'required' => true,
                'attr' => [
                    'placeholder' => $this->translator->trans('Prix du produit'), 
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                    
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ]
            ])
            ->add('garantie', NumberType::class, [
                'label' => $this->translator->trans('Garantie'),
                'required' => true,
                'attr' => [ 
                    'placeholder' => $this->translator->trans('Garantie en années'),
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                    
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ]
            ])
            ->add('libelle',TextType::class, [
                'label' => $this->translator->trans('Libellé'),
                'required' => true,
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir un libellé'),
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                    
                ],
            ])
            ->add('labelDE',TextType::class, [
                'label' => $this->translator->trans('Libelle DE'),
                'required' => false,
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir un libellé (DE)'),
                    'class' => 'form-control',
                    'style' => 'color : black ;',

                ],
            ])
            ->add('labelEN',TextType::class, [
                'label' => $this->translator->trans('Libelle EN'),
                'required' => false,
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir un libellé (EN)'),
                    'class' => 'form-control',
                    'style' => 'color : black ;',

                ],
            ])
            ->add('reference',NumberType::class, [
                'label' => $this->translator->trans('Référence'),
                'required' => true,
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir la référence'),
                    'class' => 'form-control',
                    'style' => 'color : black ;', 
                ],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('la référence doit contenir au moins {{ limit }} chiffres', ['{{ limit }}' => 6]),
                        'max' => 10,
                        'maxMessage' => $this->translator->trans('la référence doit contenir au plus {{ limit }} chiffres', ['{{ limit }}' => 10]),
                    ]),
                ],
                
            ])
            ->add('typeprod', EntityType::class, [
                'class' => TypeProd::class,
                'required' => true,
                'choice_label' => 'libelle',
                'label'=> "Type de produit",
                'attr' => [ 
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                    
                ],
            ])
            ->add('inforegle', EntityType::class, [
                'label_attr'=>[
                    'class'=>'checkbox-inline',
                ],
                'expanded' => true,
                'class' => Reglementation::class,
                'required' => true,
                'multiple' => true,
                'choice_label' => 'libelle',
                'label'=> false,
                'attr' => [
                    'class' => 'form-check',
                    'style' => 'color : white ; margin-left : 10px ;',
                    
                    
                ],
            ])
            ->add('images',FileType::class, [
                'multiple' => true,
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                ],


            ])
            ->add('documentsProduit',FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'accept' => '.pdf',
                ],
                'required' => false,

                // Uniquement les fichiers XLSX
                'data_class' => null,
                'mapped' => false,
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
            'data_class' => Produit::class,
        ]);
    }
}
