<?php

namespace Shopsys\ShopBundle\Form\Admin\Product;

use Shopsys\FrameworkBundle\Form\Admin\Product\ProductEditFormType;
use Shopsys\FrameworkBundle\Form\Admin\Product\ProductFormType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Shopsys\ProductFeed\GoogleBundle\GoogleProductFormType;
use Shopsys\FormTypesBundle\MultidomainType;

class GoogleProductFormTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('priority', MultidomainType::class, [
            'label' => 'Priority',
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return GoogleProductFormType::class;
    }
}