<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="app_category_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request, CategoryRepository $categoryRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->buscarTodasLasCategorias(); 

        $pagination = $paginator->paginate(
            $categories,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('category/index.html.twig', ['pagination' => $pagination]);

        /*return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);*/
    }

    /**
     * @Route("/new", name="app_category_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $category->setCreatedAt(new \DateTime());
        $category->setUpdatedAt(new \DateTime());
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->add($category);
            $this->addFlash(
                'success',
                'Proceso exitoso'
            );
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_category_show", methods={"GET"})
     */
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_category_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setUpdatedAt(new \DateTime());

            $categoryRepository->add($category);

            $this->addFlash(
                'success',
                'Proceso exitoso'
            );
            
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_category_delete", methods={"POST"})
     */
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category);
        }

        $this->addFlash(
            'success',
            'Proceso exitoso'
        );

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
