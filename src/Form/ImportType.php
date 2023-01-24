<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'accept' => '.xlsx',
                ],
                'required' => true,
                
                // Uniquement les fichiers XLSX
                'data_class' => null,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                        ],
                        'mimeTypesMessage' => 'Veuillez sélectionner un fichier XLSX valide',
                    ])
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Importer',
                'attr' => [
                    'class' => 'btn btn-lg btn-success m-2',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
