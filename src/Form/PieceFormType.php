<?php

namespace App\Form;

use App\Entity\Piece;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class PieceFormType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle',TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('Saisir un libellé'),
                ],
                'required'=>true
            ])
            ->add('hauteur',NumberType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('mm'),
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ],
                'required'=>true
            ])
            ->add('poids',NumberType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('g'),
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ],
                'required'=>true
            ])
            ->add('longueur',NumberType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('mm'),
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ],
                'required'=>true
            ])
            ->add('profondeur',NumberType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $this->translator->trans('mm'),
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ],
                'required'=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Piece::class,
        ]);
    }
}
?>
