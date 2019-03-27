<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Junker\Symfony\JSendFailResponse;
use Junker\Symfony\JSendSuccessResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Product controller.
 * @Route("/api", name="api_")
 */
class ProductController extends AbstractFOSRestController
{
    /**
     * List all products.
     *
     * @Rest\Get("/products")
     *
     * @return mixed
     */
    public function listAllProductsAction(EntityManagerInterface $em)
    {
        try {
            $products = $em->getRepository(Product::class)->listAll();
            return new JSendSuccessResponse($products);
        }
        catch(\Exception $e) {
            return new JSendFailResponse("error");
        }
    }

    /**
     * Get product.
     *
     * @Rest\Get("/products/{id}", requirements={"id" = "\d+"})
     *
     * @return Response
     */
    public function getProductAction(Product $product, Request $request, EntityManagerInterface $em)
    {
        return new JSendSuccessResponse($product->serialized());
    }

    /**
     * Delete product.
     *
     * @Rest\Delete("/products/{id}", requirements={"id" = "\d+"})
     *
     * @return Response
     */
    public function deleteProductAction(Product $product, EntityManagerInterface $em)
    {
        try {
            $em->remove($product);
            $em->flush();
            return new JSendSuccessResponse();
        }
        catch(\Exception $e) {
            return new JSendFailResponse();
        }
    }

    /**
     * Update product.
     *
     * @Rest\Put("/products/{id}", requirements={"id" = "\d+"})
     *
     * @return Response
     */
    public function updateProductAction(Product $product, Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);

        $em->getConnection()->beginTransaction();

        try {
            /* Find existing category, else create a new one */
            $category = $this->findOrCreateNewCategory($data['category'], $em);
            unset($data['category']);
            $product->setCategory($category);

            $form = $this->createForm(ProductType::class, $product);

            $form->submit($data, false);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                $em->commit();
                return new JSendSuccessResponse($product->serialized());
            }
            return new JSendFailResponse($form->getErrors(true, false), Response::HTTP_BAD_REQUEST);
        }
        catch(\Exception $e) {
            $em->rollback();
            return new JSendFailResponse("error", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create product.
     *
     * @Rest\Post("/products")
     *
     * @return Response
     */
    public function postProductAction(Request $request, EntityManagerInterface $em)
    {
        $em->getConnection()->beginTransaction();

        try {
            $data = json_decode($request->getContent(), true);
            $categoryName = $data['category'];

            /* Find existing category, else create a new one */
            $category = null;
            if (!empty($categoryName))  {
                $category = $this->findOrCreateNewCategory($categoryName, $em);
                unset($data['category']);
            }

            /* Create new product */
            $product = new Product();
            $product->setCategory($category);

            $form = $this->createForm(ProductType::class, $product);

            /* Validate product */
            $form->submit($data, false);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($product);
                $em->flush();
                $em->commit();
                return new JSendSuccessResponse($product->serialized(), Response::HTTP_CREATED);
            }
            return new JSendFailResponse($form->getErrors(true, false), Response::HTTP_BAD_REQUEST);
        }
        catch(\Exception $e) {
            $em->rollback();
            return new JSendFailResponse($form->getErrors(true, false), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Tries to find an existing category else creates a new one from new product data.
     *
     * @param null $name
     * @param EntityManagerInterface $em
     * @return Category|null|object
     */
    private function findOrCreateNewCategory($name = null, EntityManagerInterface $em) {
        $category = $em->getRepository(Category::class)->findOneBy(['name' => $name]);
        if (empty($category)) {
            $category = new Category();
            $form = $this->createForm(CategoryType::class, $category);

            /* Validate new category */
            $form->submit(["name" => $name]);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($category);
                $em->flush();
            }
        }

        return $category;
    }
}
