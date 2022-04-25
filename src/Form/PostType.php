<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre de l'article",
                'attr' => ['placeholder' => 'Tapez le titre']
            ])
            ->add('shortDescription', TextType::class, [
                'label' => "Résumé de l'article",
                'attr' => ['placeholder' => "Tapez un court résumé de votre article"]
            ])
            ->add('content', TextareaType::class, [
                'label' => "Le contenu de l'article",
                'attr' => ['placeholder' => 'Tapez le contenu de votre article']
            ])
            ->add('picture', UrlType::class, [
                'label' => "Image de l'article",
                'attr' => ['placeholder' => 'Tapez une url d\'image']
            ])
            ->add('category', EntityType::class, [
                'label' => "Catégorie",
                'placeholder' => "--Choisir une catégorie --",
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getName());
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
