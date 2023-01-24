<?php

namespace App\Form;

use App\Entity\Fonction;
use App\Entity\Service;
use App\Entity\TypeProd;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchUserFormType extends AbstractType
{
    private $translator;
    public function __construct(TranslatorInterface $trans)
    {
        $this->translator = $trans;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => $this->translator->trans('Rechercher un utilisateur'),
                    'style'=>'width : 300px',
                    'class' => 'form-control'
                ],
            ])
            ->add('service',EntityType::class,[
                'label' => false,
                'required' => false,
                'class' => Service::class,
                'choice_label' => 'libelle',
                'multiple' => false,
                'attr' => [
                    'class' => 'form-select',
                    'style' => 'color: black'
                ],
                'placeholder' => $this->translator->trans('Veuillez choisir un service')
            ])
            ->add('fonction',EntityType::class,[
                'label' => false,
                'required' => false,
                'class' => Fonction::class,
                'choice_label' => 'libelle',
                'multiple' => false,
                'attr' => [
                    'class' => 'form-select',
                    'style' => 'color: black'
                ],
                'placeholder' => $this->translator->trans('Veuillez choisir une fonction')
            ])
            ->add('Rechercher',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-info',
                    'style'=>'font-family : Sen-Regular-Bold',

                ],
                'label' => $this->translator->trans("FILTRER")
            ])
            ->add('archive', CheckboxType::class, [
                'label' => $this->translator->trans('Afficher les utilisateurs archivés'),
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                    'style' => 'margin: 5px',
                    'data-id' => ''
                ]
                // Data id => 'archive'

            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'my_custom_option' => 'default_value',
            'csrf_protection' => false,
        ]);
    }
}
