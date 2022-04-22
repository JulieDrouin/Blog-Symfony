<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;

class PostController extends AbstractController
{
    /**
     * @Route("/{category_slug}/{slug}", name="app_post_read")
     */
    public function read($slug, PostRepository $postRepository): Response
    {
        $post = $postRepository->findOneBy([
            'slug' => $slug
        ]);

        // if(!$post) {
        //     throw $this->createNotFoundException("L'article demandé n'existe pas");
        // }

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/{slug}", name="app_post_category" )
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        // if(!$category) {
        //     throw $this->createNotFoundException("La catégorie n'existe pas");
        // }
        return $this->render('post/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/admin/post/create", name="app_post_create")
     * @param Post $post
     */
    public function create(FormFactoryInterface $factory, CategoryRepository $categoryRepository): Response
    {

        $builder = $factory->createBuilder();

        $builder->add('titre', TextType::class, [
            'label' => "Titre de l'article",
            'attr' => ['class' => 'form-control', 'placeholder' => 'Tapez le titre']
        ])
            ->add('slug', TextType::class, [
                'label' => "Slug de l'article",
                'attr' => ['class' => 'form-control', 'placeholder' => 'Tapez le slug']
            ])
            ->add('shortDescription', TextType::class, [
                'label' => "Résumé de l'article",
                'attr' => ['class' => 'form-control', 'placeholder' => "Tapez un court résumé de votre article"]
            ])
            ->add('content', TextareaType::class, [
                'label' => "Le contenu de l'article",
                'attr' => ['class' => 'form-control', 'placeholder' => 'Tapez le contenu de votre article']
            ])
            ->add('picture', TextType::class, [
                'label' => "Image de l'article",
                'attr' => ['class' => 'form-control', 'placeholder' => "Tapez le lien vers l'image de votre article"]
            ]);

            $options = [];
            foreach ($categoryRepository->findAll() as $category) {
                $options[$category->getName()] = $category->getId();
            };

            $builder->add('category', ChoiceType::class, [
                'label' => "Categorie de l'article",
                'attr' => ['class' => 'form-control'],
                'placeholder' => "--Choisir une catégorie --",
                'choices' => $options
            ]);

        $builder->setMethod('GET')
            ->setAction('/');

        $form = $builder->getForm();

        $formView = $form->createView();

        return $this->render('post/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
