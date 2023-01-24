<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Fonction;
use App\Entity\Service;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EmployeeFormType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Mail',
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => ('Veuillez renseigner votre email'),
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('Votre email doit contenir au moins {{ limit }} caractères', ['{{ limit }}' => 6]),
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],

            ])

            ->add('nom', TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'form-control',
                ],

                'constraints' => [
                    
                    new NotBlank([
                        'message' => ('Veuillez renseigner votre nom'),
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => $this->translator->trans('Votre nom doit contenir au moins {{ limit }} caractères', ['{{ limit }}' => 2]),
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ]
            ])

            ->add('prenom', TextType::class, [
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => ('Veuillez renseigner votre prénom'),
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => $this->translator->trans('Votre prénom doit contenir au moins {{ limit }} caractères', ['{{ limit }}' => 2]),
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ]
            ])
            ->add('service', EntityType::class, [
                'attr' => [ 
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                    
                ],
                'class' => Service::class,
                'choice_label' => 'libelle',
                'label'=> "Département",
                'disabled' => true,
            ])

            ->add('fonction', EntityType::class, [
                'disabled' => true,
                'attr' => [ 
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                    
                ],
                'class' => Fonction::class,
                'choice_label' => 'libelle',
                'label'=> "Fonction",
            ])

            ->add('documentsUser', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'accept' => '.pdf',
                ],
                'label' => $this->translator->trans('Documents'),
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
            'data_class' => User::class,
        ]);
    }
}
