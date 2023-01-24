<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Typeprod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ShowProductFormType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', NumberType::class,[
                'attr' => [
                    'placeholder' => $this->translator->trans('Référence'),
                    'class' => 'form-control',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner la référence'),
                    ]),
                ]
            ])

            ->add('libelle', TextType::class, [
                'attr' => [
                    'placeholder' => 'Libellé',
                    'class' => 'form-control',
                ],

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

            ->add('pu', MoneyType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('Prix unitaire'),
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner le prix unitaire'),
                    ]),
                ]
            ])

            ->add('garantie', TextType::class, [
                'attr' => [
                    'placeholder' => 'Libellé',
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => ('Veuillez renseigner la durée de la garantie'),
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
