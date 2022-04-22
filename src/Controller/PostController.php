<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

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

        if(!$post) {
            throw $this->createNotFoundException("L'article demandé n'existe pas");
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/{slug}", name="app_post_category", priority=-1)
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if(!$category) {
            throw $this->createNotFoundException("La catégorie n'existe pas");
        }
        return $this->render('post/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/admin/post/create", name="app_post_create")
     */
    public function create(FormFactoryInterface $factory, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $builder = $factory->createBuilder(FormType::class, null, [
            'data_class' => Post::class
        ]);

        $builder->add('titre', TextType::class, [
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
            ]);

        $builder->add('category', EntityType::class, [
            'label' => "Catégorie",
            'placeholder' => "--Choisir une catégorie --",
            'class' => Category::class,
            'choice_label' => 'name',
        ]);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $post = $form->getData();
            $post->setSlug(strtolower($slugger->slug($post->getName())));
            $post->setCreatedAt(new \DateTimeImmutable());

            $em->persist($post);
            $em->flush();
        }

        $formView = $form->createView();

        return $this->render('post/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
