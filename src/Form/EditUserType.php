<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Fonction;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Contracts\Translation\TranslatorInterface;


class EditUserType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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

            ->add('date_naissance', BirthdayType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],

                /* Vérification que la date soit inférieur à celle du jour */
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner une date de naissance'),
                    ]),
                    new LessThan([
                        'value' => 'today',
                        'message' => $this->translator->trans('Veuillez renseigner une date de naissance valide'),
                    ]),
                ],
            ])

            ->add('email', EmailType::class, [
                // Only READ
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

            ->add('service', EntityType::class, [
                'attr' => [ 
                    'class' => 'form-control',
                    'style' => 'color : black ;'
                    
                ],
                'class' => Service::class,
                'choice_label' => 'libelle',
                'label'=> "Département",
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner un département'),
                    ]),
                ],
            ])

            ->add('fonction', EntityType::class, [
                'attr' => [ 
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                    
                ],
                'class' => Fonction::class,
                'choice_label' => 'libelle',
                'label'=> "Fonction",
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner votre fonction'),
                    ]),
                ],
            ])

            ->add('submitmodif', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-lg btn-primary submit m-4',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => 'task_item',
        ]);
    }
}
