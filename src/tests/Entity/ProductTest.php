<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * @var Product
     */
    protected $product;

    protected function setUp()
    {
        $this->product = new Product();
    }

    public function testGetterAndSetter() {

        $this->assertNull($this->product->getId());
        $this->assertNull($this->product->getModifiedAt());

        $date = new \DateTime();

        $this->product->setCreatedAt($date);
        $this->assertEquals($date, $this->product->getCreatedAt());

        $this->product->setName("Fony UHD HDR 55\" 4k TV");
        $this->assertEquals("Fony UHD HDR 55\" 4k TV", $this->product->getName());

        $this->product->setSku("A0004");
        $this->assertEquals("A0004", $this->product->getSku());

        $this->product->setPrice(1399.99);
        $this->assertEquals(1399.99, $this->product->getPrice());

        $this->product->setQuantity(5);
        $this->assertEquals(5, $this->product->getQuantity());
    }
}