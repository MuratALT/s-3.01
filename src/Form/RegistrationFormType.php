<?php

namespace App\Form;

use App\Entity\Fonction;
use App\Entity\Service;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
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
                ]]) 
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
                ]])
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
            ->add('service', EntityType::class, [
                'attr' => [ 
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                    
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
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes ne correspondent pas.',
                'label'=> false,
                'mapped' => false,
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe' , 'attr' =>[     'class' => 'form-control',
                ]],
                'second_options' => ['label' => $this->translator->trans('Répétez le mot de passe'), 'attr' =>[     'class' => 'form-control','translate' => 'yes',]],
                'attr' => [
                    'autocomplete' => 'new-password',
                    
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit avoir une taille minimum de  {{ limit }} charactères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
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

            ->add('submit', SubmitType::class, [
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
        ]);
    }
}
