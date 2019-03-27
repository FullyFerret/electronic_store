<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Product;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
//            ->add('category', EntityType::class, [
//                'class' => Category::class,
//                'choice_label' => 'name'
//            ])
            ->add('sku')
            ->add('price')
            ->add('quantity')
            ->add('save', SubmitType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Product::class,
            'csrf_protection' => false
        ));
    }
}