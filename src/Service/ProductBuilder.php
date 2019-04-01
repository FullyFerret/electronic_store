<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class ProductBuilder
{
    private $logger;

    public function __construct($data = null, $setCategoryToNull = false, EntityManagerInterface $em, FormFactoryInterface $formFactory, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->formFactory = $formFactory;
        $this->data = $data;
        $this->setCategoryToNull = $setCategoryToNull;
    }

    /**
     * Extracts category data and tries to find existing entity.
     *
     * @return null|object
     */
    private function extractCategory() {
        $this->setCategoryToNull = array_key_exists('category', $this->data) && $this->data['category'] === null;

        $this->logger->info("Checking for existing category by name...");
        $categoryName = empty($data['category']) ? null : $data['category'];
        $existingCategory = $this->setCategoryToNull ? null :
            $this->em->getRepository(Category::class)->findOneBy(["name" => $categoryName]);
        $this->data['category'] = $existingCategory === null ? $categoryName : null;
        return $existingCategory;
    }

    /**
     * Builds a ProductType form for updating or creating a new Product.
     *
     * @param Product $product
     * @return Category|null|object
     */
    public function build($data, Product $product = null) {

        $product = $product ?? new Product();
        $this->data = $data;

        /* Find existing category or add new category name to form */
        $existingCategory = $this->extractCategory();
        $this->logger->info("Creating Product form...");
        $form = $this->formFactory->create(ProductType::class, $product);

        /* Validate product update */
        $this->logger->info("Validating Product properties...");
        $form->submit($this->data, false);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->setCategoryToNull) {
                $product->setCategory(null);
            }
            else if (!empty($existingCategory)) {
                $product->setCategory($existingCategory);
            }
            $this->logger->info("Product built.");
            return $product;
        }
        throw new ValidatorException((string) $form->getErrors(true, false));
    }
}