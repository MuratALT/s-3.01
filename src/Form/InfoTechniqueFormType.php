<?php
namespace App\Form;

use App\Entity\InfoTechnique;
use App\Entity\TypeAlim;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class InfoTechniqueFormType extends AbstractType {
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('longueur', NumberType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('mm'),
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner une longueur'),
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ],
            ])

            ->add('hauteur', NumberType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('mm'),
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner une hauteur'),
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ],
            ])

            ->add('profondeur', NumberType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('mm'),
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner une profondeur'),
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ],
            ])

            ->add('largeur', NumberType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('mm'),
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner une largeur'),
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ],
            ])

            ->add('poids', NumberType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('g'),
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner un poids'),
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ],
            ])



             ->add('infoalim', EntityType::class, [
                'attr' => [ 
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                    
                ],
                'class' => TypeAlim::class,
                'choice_label' => 'libelle',
                'label'=> "Type d'alimentation",
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner un type d\'alimentation'),
                    ]),
                ],
            ])

            ->add('duree_vie', NumberType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('Durée de vie en années'),
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner la durée de vie du produit'),
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ],
            ])

            ->add('compatibilite', TextType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir les compatibilités'),
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner compatibilité destinée au produit'),
                    ]),
                ],
            ])

            ->add('puissSon', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => $this->translator->trans('db'),
                    'class' => 'form-control',
                ],

                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => ('Veuillez entrer un nombre positif'),
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoTechnique::class,
        ]);
    }
}

?>