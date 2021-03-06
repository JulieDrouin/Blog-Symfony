<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/dashboard/category", name="category_dashboard")
     */
    public function showAll(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        if (!$categories) {
            throw $this->createNotFoundException("Il y a aucun article !");
        }

        return $this->render('admin/dashboard_category.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

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
            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('category_dashboard');
        }
        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }
    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, CategoryRepository $categoryRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $category = $categoryRepository->find($id);
        $form = $this->createForm(CategoryType::class, $category);

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
            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->flush();

            return $this->redirectToRoute('category_dashboard');
        }
        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/category/{id}/delete", name="category_delete")
     */
    public function delete($id, CategoryRepository $categoryRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            return $this->redirectToRoute('category_dashboard');
        }
            $em->remove($category);
            $em->flush();

            return $this->redirectToRoute('category_dashboard');
    }
}
