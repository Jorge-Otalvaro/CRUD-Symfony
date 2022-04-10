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
        $products = $em->getRepository(Product::class)->buscarTodosLosProductos(); 

        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('product/index.html.twig', ['pagination' => $pagination]);

        /*
        return $this->render('/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);*/
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
     * @Route("/{id}/pdf", name="sandbox_pdf", methods={"GET"})
     */
    public function pdfAction(\Knp\Snappy\Pdf $snappy, Product $product): Response
    {
        $html = $this->renderView('pdf/index.html.twig', [
            'product' => $product,
        ]);

        $filename = 'product';

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
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
