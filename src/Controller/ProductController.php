<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\ProductType;
use App\Service\ProductBuilder;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Junker\Symfony\JSendFailResponse;
use Junker\Symfony\JSendSuccessResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\Exception\ValidatorException;

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
    public function listAllProductsAction(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $logger->info("Entering listAllProductsAction...");
        try {
            $products = $em->getRepository(Product::class)->listAll();

            $logger->info("Successfully retrieved all products.");
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
    public function getProductAction(Product $product, LoggerInterface $logger)
    {
        $logger->info("Entering getProductAction...");
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
     *
     * @throws \Exception
     */
    public function deleteProductAction(Product $product, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $logger->info("Entering deleteProductAction...");

        try {
            $logger->info("Removing product...");
            $em->remove($product);
            $em->flush();

            $logger->info("Successfully removed product.");
            return new JSendSuccessResponse();
        }
        catch(\Exception $e) {
            throw $e;
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
    public function updateProductAction(Product $product, Request $request, EntityManagerInterface $em, ProductBuilder $productBuilder, LoggerInterface $logger)
    {
        $logger->info("Entering updateProductAction...");
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            $logger->notice("Received empty product PUT data.");
            return new JSendFailResponse("You must specify at least one product property value to update.", Response::HTTP_BAD_REQUEST);
        }

        $em->getConnection()->beginTransaction();

        try {
            $logger->info("Preparing to rebuild product...");
            $productBuilder->build($data, $product);
            $em->flush();
            $em->commit();
            return new JSendSuccessResponse($product->serialized(), Response::HTTP_OK);
        }
        catch(InvalidArgumentException $e) {
            $em->rollback();
            return new JSendFailResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        catch(ValidatorException $e) {
            $em->rollback();
            return new JSendFailResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        catch(UniqueConstraintViolationException $e) {
            $logger->notice("New product unique constraint violation");
            $em->rollback();
            return new JSendFailResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
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
    public function postProductAction(Request $request, EntityManagerInterface $em, ProductBuilder $productBuilder, LoggerInterface $logger)
    {
        $logger->info("Entering postProductAction...");
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            $logger->notice("Received empty product POST data.");
            return new JSendFailResponse("Cannot submit empty product", Response::HTTP_BAD_REQUEST);
        }

        $em->getConnection()->beginTransaction();

        try {
            $logger->info("Preparing to build product...");
            $product = $productBuilder->build($data);
            $em->persist($product);
            $em->flush();
            $em->commit();
            return new JSendSuccessResponse($product->serialized(), Response::HTTP_CREATED);
        }
        catch(InvalidArgumentException $e) {
            $em->rollback();
            return new JSendFailResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        catch(ValidatorException $e) {
            $em->rollback();
            return new JSendFailResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        catch(UniqueConstraintViolationException $e) {
            $logger->notice("New product unique constraint violation");
            $em->rollback();
            return new JSendFailResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
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
}
