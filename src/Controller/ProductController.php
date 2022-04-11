<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/**
 * @Route("/")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="app_product_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request, ProductRepository $productRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository(Product::class)->buscarTodosLosProductos();

        if ($request->query->getAlnum('filter')) {
            $queryBuilder->andwhere('prod.name LIKE :name')
            ->setParameter('name', '%' . $request->query->getAlnum('filter') . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 2)
        );

        return $this->render('product/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @Route("/new", name="app_product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductRepository $productRepository): Response
    {        
        $product = new Product();        
        $product->setCreatedAt(new \DateTime());
        $product->setUpdatedAt(new \DateTime());     
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $product->getCategory();
            $product->setCategory($category);
            $productRepository->add($product);  
                      
            $this->addFlash(
                'success',
                'Proceso exitoso'
            );

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUpdatedAt(new \DateTime());
            $category = $product->getCategory();
            $product->setCategory($category);
            $productRepository->add($product);

            $this->addFlash(
                'success',
                'Proceso exitoso'
            );

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Export to PDF
     * 
     * @Route("/{id}/pdf", name="sandbox_pdf", methods={"GET"})
     */
    public function pdfAction(\Knp\Snappy\Pdf $snappy, Product $product): Response
    {
        $html = $this->renderView('pdf/index.html.twig', [
            'product' => $product,
        ]);
        
        $filename = sprintf('product-%s.pdf', date('Y-m-d'));

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => sprintf('attachment; filename="%s"', $filename),
            )
        );
    }

    /**
     * @Route("/buscar", name="app_product_search",  methods={"GET"})
     */
    public function search(PaginatorInterface $paginator, Request $request, ProductRepository $productRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(Product::class)->findFilter($request); 

        /* 

        Terminar validacion para que sea dinamica y se actualice en tiempo real no estar precionando el boton buscar 

        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            5
        );

        */

        return $this->render('product/index.html.twig', ['pagination' => $pagination]);
    }
}
