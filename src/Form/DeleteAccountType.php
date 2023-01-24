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

class DeleteAccountType extends AbstractType
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
                        'autocomplete' => $this->translator->trans('Mot de passe actuel'),
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
                    ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}