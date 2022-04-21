<?php

namespace App\Controller;

use App\Entity\Post;
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
     * @Route("/post-{id}", name="app_post_read_id")
     * @param Post $post
     */
    public function read(Post $post): Response
    {
        return $this->render('post/index.html.twig', [
            "post" => $post
        ]);
    }
}
