<?php

namespace App\Form;

use App\Entity\Piece;
use App\Entity\Produit;
use App\Entity\Ticket;
use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class TicketFormType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commentaire',CKEditorType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Commentaire',
                ],
                'label' => $this->translator->trans('Commentaire'),
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' =>  $this->translator->trans('Veuillez renseigner un commentaire'),
                    ]),
                ],
            ])
            ->add('produit',EntityType::class,[
                'label' => $this->translator->trans('Produit concerné'),
                'class'=> Produit::class,
                'choice_label' => function ($reference) {
                    // fonction qui retourne le libellé à afficher pour chaque référence
                    return $reference->getReference() . ' - ' . $reference->getLibelle();
                },
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('user',EntityType::class,[
                'class'=> User::class,
                'label'=>false,
                'choice_label' => 'nom',
                'disabled' => true,
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'attr' => [
                    'style' => 'display:none',
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
