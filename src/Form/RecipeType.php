<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function __construct(
        private FormListenerFactory $listenerFactory,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => '',
            ])
            ->add('slug', TextType::class, [
                'empty_data' => '',
                'required' => false,
                // 'constraints' => [
                //     new Sequentially([
                //         new Length(min: 10),
                //         new Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: 'Ce slug est invalide')
                //     ])
                // ],
            ])
            ->add('thumbnailFile', FileType::class, [
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                // 'expanded' remplace la liste par des boutons radio
                // 'expanded' => true,
            ])
            ->add('content', TextareaType::class, [
                'empty_data' => '',
            ])
            ->add('duration')
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
            ])
            // (...) => On crée un callable à partir de la fontion autoSlug
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoSlug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
