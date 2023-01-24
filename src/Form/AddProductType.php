<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\TypeProd;
use App\Form\InfoTechniqueFormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class AddProductType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'attr' => [
                    'placeholder' => 'Caméra connectée XE406',
                    'class' => 'form-control',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => ('Veuillez renseigner le libellé'),
                    ]),

                    new Length([
                        'min' => 3,
                        'minMessage' => $this->translator->trans('Le libellé doit contenir au moins {{ limit }} caractères', ['{{ limit }}' => 3]),
                        // max length allowed by the database for this field
                        'max' => 255,
                    ]),
                ]
            ])

            ->add('reference', NumberType::class,[
                'attr' => [
                    'placeholder' => $this->translator->trans('1234567'),
                    'class' => 'form-control',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner la référence'),
                    ]),
                ]
            ])
            

            ->add('pu', NumberType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('40,00'),
                    'class' => 'form-control',
                    
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner le prix unitaire'),
                    ]),
                ]
            ])

            ->add('typeprod', EntityType::class, [
                'class' => Typeprod::class,
                'choice_label' => 'libelle',
                'label'=> "Type de produit",
                'attr' => [ 
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                    
                ],
            ])

            ->add('garantie', TextType::class, [
                'attr' => [
                    'placeholder' => '4',
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => ('Veuillez renseigner la durée de la garantie'),
                    ]),
                ]
            ])

            ->add("valider", SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success',
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
