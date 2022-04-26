<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{
    /**
     * @Route("/{slug}", name="post_category", priority=-1)
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie n'existe pas");
        }
        return $this->render('post/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="post_show", priority=-1)
     */
    public function show($slug, PostRepository $postRepository): Response
    {
        $post = $postRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$post) {
            throw $this->createNotFoundException("L'article demandé n'existe pas");
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/admin/dashboard/post", name="post_dashboard")
     */
    public function showAll(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        if (!$posts) {
            throw $this->createNotFoundException("Il y a aucun article !");
        }

        return $this->render('admin/dashboard_post.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/admin/post/create", name="post_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $property = $error->getCause()->getPropertyPath();
                    $property = str_replace('data.', '', $property);
                    $errors[$property] = $error->getMessage();
                }
                return new Response(json_encode(["errors" => $errors]), 400);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug(strtolower($slugger->slug($post->getTitle())));
            $post->setCreatedAt(new \DateTimeImmutable());

            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('post_dashboard');
        }
        $formView = $form->createView();

        return $this->render('post/create.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/post/{id}/edit", name="post_edit")
     */
    public function edit($id, PostRepository $postRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $post = $postRepository->find($id);
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $form->submit($data);

            if (!$form->isValid()) {
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $property = $error->getCause()->getPropertyPath();
                    $property = str_replace('data.', '', $property);
                    $errors[$property] = $error->getMessage();
                }
                return new Response(json_encode(["errors" => $errors]), 400);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug(strtolower($slugger->slug($post->getTitle())));
            $post->setUpdatedAt(new \DateTimeImmutable());

            $em->flush();

            return $this->redirectToRoute('post_dashboard');
        }
        $formView = $form->createView();

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/post/{id}/delete", name="post_delete")
     */
    public function delete($id, PostRepository $postRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $post = $postRepository->find($id);

        if (!$post) {
            return $this->redirectToRoute('post_dashboard');
        }
            $em->remove($post);
            $em->flush();

            return $this->redirectToRoute('post_dashboard');
    }
}
