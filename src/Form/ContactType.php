<?php

namespace App\Form;

use App\Dto\contactDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Pour extraire les traductions à faire , passer la commande :
        // symfony console translation:extract --dump-messages fr (dry-run)
        // symfony console translation:extract --force fr --format=yaml
        $builder
            ->add('name', TextType::class, [
                'empty_data' => '',
                'label' => 'contactForm.name'
            ])
            ->add('email', EmailType::class, [
                'empty_data' => '',
                'label' => 'contactForm.email'
            ])
            ->add('message', TextareaType::class, [
                'empty_data' => '',
                'label' => 'contactForm.message'
            ])
            ->add('service', ChoiceType::class, [
                'choices' => [
                    'Comptabilité' => 'contact@pitette.fr',
                    'Support' => 'contact@pitette.fr',
                    'Commercial' => 'contact@pitette.fr',
                ],
                'label' => 'contactForm.service'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'contactForm.submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => contactDto::class,
        ]);
    }
}
