<?php

namespace App\Form;

use App\Entity\DocumentVente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class DocumentsVenteFormType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir un libellé'),
                    'class' => 'form-control',
                ],

                'constraints' => [
                    
                    new NotBlank([
                        'message' => ('Veuillez renseigner votre nom'),
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => $this->translator->trans('Le libellé doit contenir au moins {{ limit }} caractères', ['{{ limit }}' => 3]),
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ]
            ])

            ->add('commentaire', CKEditorType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('Entrez votre commentaire ici'),
                    'class' => 'form-control',
                ],
            ])

            /* ->add('documentsUser', FileType::class, [
                'multiple' => true,
                'label' => 'Documents',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'color : black ;',
                ],
                // Only PDF files allowed

            ]) */

            /* Add submit button */

            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('Ajouter le document'),
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DocumentVente::class,
        ]);
    }
}
