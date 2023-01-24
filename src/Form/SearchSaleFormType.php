<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Model\SearchDataProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchSaleFormType extends AbstractType
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
                    'placeholder' => $this->translator->trans('Rechercher une vente'),
                    'style'=>'width : 300px',
                    'class' => 'form-control'
                ],
            ])
            ->add('categories',EntityType::class,[
                'label' => false,
                'required' => false,
                'class' => Categorie::class,
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
