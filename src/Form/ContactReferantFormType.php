<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class ContactReferantFormType extends AbstractType
{

    private $translator;
    private $user;

    public function __construct(TranslatorInterface $translator, Security $security)
    {
        $this->translator = $translator;
        $this->user = $security->getUser();
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('referent',EntityType::class,[
                'class'=>User::class,
                'label' => $this->translator->trans("Référent"),
                'attr' => [
                    'class' => 'form-control'
                ],
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->Where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_VALID%')
                        ->leftJoin('u.fonction', 'f')
                        ->andWhere('f.libelle LIKE :fonction')
                        ->leftJoin("u.service", "s")
                        ->andWhere('s.libelle LIKE :service')
                        ->setParameter('fonction', '%Référent%')
                        ->setParameter('service', '%'.$this->user->getService()->getLibelle().'%');

                },
                // Query =>ListALLRéférent


                'choice_label' => 'nom',
            ])
            ->add('sujet',TextType::class,[
                'label' => $this->translator->trans("Sujet"),
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir le sujet'),
                    'class' => 'form-control',
                ]
            ])
            ->add('message',TextareaType::class,[
                'label' => $this->translator->trans("Message"),
                'attr' => [
                    'placeholder' => $this->translator->trans('Saisir un message'),
                    'class' => 'form-control',
                    'rows' => 5,
                ]
            ])
            ->add('submit',SubmitType::class,[
                'label' => $this->translator->trans("Envoyer"),
                'attr' => [
                    'class' => 'btn btn-primary m-2 submit'
                ]
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
