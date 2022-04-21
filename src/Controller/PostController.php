<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;

class PostController extends AbstractController
{
    /**
     * @Route("/post-{id}", name="app_post_read_id")
     * @param Post $post
     */
    public function read(Post $post): Response
    {
        return $this->render('post/index.html.twig', [
            "post" => $post
        ]);
    }

    /**
     * @Route("/admin/post/create", name="app_post_create")
     * @param Post $post
     */
    public function create(FormFactoryInterface $factory): Response
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
            ->add('content', TextareaType::class, [
                'label' => "Le contenu de l'article",
                'attr' => ['class' => 'form-control', 'placeholder' => 'Tapez le contenu de votre article']
            ])
            ->add('picture', TextType::class, [
                'label' => "Image de l'article",
                'attr' => ['class' => 'form-control', 'placeholder' => "Tapez le lien vers l'image de votre article"]
            ]);

        $form = $builder->getForm();

        $formView = $form->createView();

        return $this->render('post/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
