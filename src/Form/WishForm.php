<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Wish;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WishForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label' => 'Title',
                'attr' => [
                    'class' => 'form-control mb-2',
                ]
            ])
            ->add('description', TextareaType::class,[
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control mb-2',
                ]
            ])
            ->add('author', TextType::class,[
                'label' => 'Author',
                'attr' => [
                    'class' => 'form-control mb-1',
                ]
            ])
            ->add('category', EntityType::class,[
                'label' => 'Category',
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control mb-1',
                ],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wish::class,
        ]);
    }
}
