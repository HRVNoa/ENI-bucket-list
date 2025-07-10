<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Wish;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('category', EntityType::class,[
                'label' => 'Category',
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'required' => true,
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'required' => false,
                'mapped' => false,
            ])
            ->add('doDelete', CheckboxType::class, [
                'label' => 'Delete image',
                'attr' => [
                    'class' => 'form-check-input mb-2',
                ],
                'mapped' => false,
                'required' => false,
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
