<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditPasswordFormType extends AbstractType
{

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [

                    'attr' => [
                        'autocomplete' => $this->translator->trans('Ancien mot de passe'),
                        'class' => 'form-control',
                    ],
                

                    'constraints' => [
                        new NotBlank([
                            'message' =>('Veuillez entrer un mot de passe'),
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' =>  $this->translator->trans('Votre mot de passe doit comporter au moins {{ limit }} caractères'),
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ]])


            
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => $this->translator->trans('Nouveau mot de passe'),
                        'class' => 'form-control',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' =>('Veuillez entrer un mot de passe'),
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' =>  $this->translator->trans('Votre mot de passe doit comporter au moins {{ limit }} caractères'),
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => $this->translator->trans('Nouveau mot de passe'),
                ],
                'second_options' => [
                    'label' => $this->translator->trans('Répéter le mot de passe'),
                ],
                'invalid_message' => $this->translator->trans('Les mot de passe doivent correspondre.'),
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
