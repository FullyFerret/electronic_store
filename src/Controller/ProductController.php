<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\ProductType;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
     *
     * @throws \Exception
     */
    public function listAllProductsAction(EntityManagerInterface $em)
    {
        try {
            $products = $em->getRepository(Product::class)->listAll();
            return new JSendSuccessResponse($products);
        }
        catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get product.
     *
     * @Rest\Get("/products/{id}", requirements={"id" = "\d+"})
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function getProductAction(Product $product, Request $request, EntityManagerInterface $em)
    {
        try {
            return new JSendSuccessResponse($product->serialized());
        }
        catch(\Exception $e) {
            throw $e;
        }
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
     *
     * @throws \Exception
     */
    public function updateProductAction(Product $product, Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JSendFailResponse("You must specify at least one product property value to update", Response::HTTP_BAD_REQUEST);
        }

        $categoryName = empty($data['category']) ? null : $data['category'];

        $em->getConnection()->beginTransaction();

        try {
            /* Find existing category, else create a new one */
            $category = null;
            if (!empty($categoryName)) {
                $category = $this->findOrCreateNewCategory($categoryName, $em);
                if ($category instanceof JSendFailResponse) {
                    return $category;
                }
                else {
                    $product->setCategory($category);
                }
                unset($data['category']);
            }

            $form = $this->createForm(ProductType::class, $product);

            /* Validate product update */
            $form->submit($data, false);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                $em->commit();
                return new JSendSuccessResponse($product->serialized());
            }
            return new JSendFailResponse((string) $form->getErrors(true, false), Response::HTTP_BAD_REQUEST);
        }
        catch(UniqueConstraintViolationException $e) {
            $em->rollback();
            return new JSendFailResponse("Cannot change product name to one that already exists", Response::HTTP_BAD_REQUEST);
        }
        catch(DBALException $e) {
            $em->rollback();
            throw $e;
        }
        catch(\Exception $e) {
            $em->rollback();
            throw $e;
        }
    }

    /**
     * Create product.
     *
     * @Rest\Post("/products")
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function postProductAction(Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JSendFailResponse("Cannot submit empty product", Response::HTTP_BAD_REQUEST);
        }

        $categoryName = empty($data['category']) ? null : $data['category'];

        $em->getConnection()->beginTransaction();

        try {
            /* Create new product */
            $product = new Product();

            /* Find existing category, else create a new one */
            if (!empty($categoryName)) {
                $category = $this->findOrCreateNewCategory($categoryName, $em);
                if ($category instanceof JSendFailResponse) {
                    return $category;
                }
                else {
                    $product->setCategory($category);
                }
                unset($data['category']);
            }

            $form = $this->createForm(ProductType::class, $product);

            /* Validate product */
            $form->submit($data, false);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($product);
                $em->flush();
                $em->commit();
                return new JSendSuccessResponse($product->serialized(), Response::HTTP_CREATED);
            }
            return new JSendFailResponse((string) $form->getErrors(true, false), Response::HTTP_BAD_REQUEST);
        }
        catch(UniqueConstraintViolationException $e) {
            $em->rollback();
            return new JSendFailResponse("Product name already exists, try updating with PUT", Response::HTTP_BAD_REQUEST);
        }
        catch(DBALException $e) {
            $em->rollback();
            throw $e;
        }
        catch(\Exception $e) {
            $em->rollback();
            throw $e;
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
                return $category;
            }
            return new JSendFailResponse((string) $form->getErrors(true, false), Response::HTTP_BAD_REQUEST);
        }

        return $category;
    }
}
