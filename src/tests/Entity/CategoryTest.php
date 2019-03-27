<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /**
     * @var Category
     */
    protected $category;

    protected function setUp()
    {
        $this->category = new Category();
    }

    public function testGetterAndSetter()
    {
        $this->assertNull($this->category->getId());
        $this->assertNull($this->category->getModifiedAt());

        $date = new \DateTime();

        $this->category->setCreatedAt($date);
        $this->assertEquals($date, $this->category->getCreatedAt());

        $this->category->setName("TVs and Accessories");
        $this->assertEquals("TVs and Accessories", $this->category->getName());
    }
}