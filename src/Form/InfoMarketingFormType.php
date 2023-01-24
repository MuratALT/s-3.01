<?php

namespace App\Form;

use App\Entity\InfoMarketing;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class InfoMarketingFormType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', CKEditorType::class, [
                "required" => true ,
                'attr' => [
                    'placeholder' => $this->translator->trans('Entrez une description...'),
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner une fonctionnalité valide'),
                    ]),
                ],
            ])

            ->add('fonctionnalites', CKEditorType::class, [
                "required" => true ,
                'attr' => [
                    'placeholder' => $this->translator->trans('Entrez une brève étendue des fonctionnalités...'),
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner une fonctionnalité valide'),
                    ]),
                ],
            ])

            ->add('benefices', CKEditorType::class, [
                "required" => true ,
                'attr' => [
                    'placeholder' => $this->translator->trans('Entrez une brève étendue des bénéfices...'),
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Veuillez renseigner une fonctionnalité valide'),
                    ]),
                ],
            ]);           
                
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoMarketing::class,
        ]);
    }
}
