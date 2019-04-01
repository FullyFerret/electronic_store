<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Product;
use Symfony\Component\Validator\Constraints\Valid;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('category', CategoryType::class, [
                'required' => false,
                'constraints' => [new Valid()]
            ])
            ->add('sku')
            ->add('price')
            ->add('quantity')
            ->add('save', SubmitType::class);

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            [$this, 'onPreSubmitData']
        );
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Product::class,
            'csrf_protection' => false
        ));
    }

    public function onPreSubmitData(FormEvent $event)
    {
        $eventData = $event->getData();
        $eventData['category'] = empty($eventData['category']) ? null : ["name" => $eventData['category']];
        $event->setData($eventData);
    }
}