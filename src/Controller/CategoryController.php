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
class CategoryController extends AbstractFOSRestController
{
    /**
     * List all categories.
     *
     * @Rest\Get("/categories")
     *
     * @return mixed
     */
    public function listAllCategoriesAction(EntityManagerInterface $em)
    {
        try {
            $categories = $em->getRepository(Category::class)->listAll();
            return new JSendSuccessResponse($categories);
        }
        catch(\Exception $e) {
            return new JSendFailResponse("error");
        }
    }
}
