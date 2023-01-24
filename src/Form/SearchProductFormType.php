<?php

namespace App\Form;

use App\Entity\TypeProd;
use App\Model\SearchDataProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchProductFormType extends AbstractType
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
                    'placeholder' => $this->translator->trans('Rechercher un produit'),
                    'style'=>'width : 300px',
                    'class' => 'form-control'
                ],
            ])
            ->add('categories',EntityType::class,[
                'label' => false,
                'required' => false,
                'class' => TypeProd::class,
                'choice_label' => 'libelle',
                'multiple' => false,
                'attr' => [
                    'class' => 'form-select',
                    'style' => 'color: black'
                ],
                'placeholder' => $this->translator->trans('Veuillez choisir une catégorie')
            ])
            ->add('Rechercher',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-info',
                    'style'=>'font-family : Sen-Regular-Bold',

                ],
                'label' => $this->translator->trans("FILTRER")
            ])
            ->add('archive', CheckboxType::class, [
                'label' => $this->translator->trans('Afficher les produits archivés'),
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                    'style' => 'margin: 5px',
                    'data-id' => ''
                ]
                // Data id => 'archive'

            ])
        ;
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
