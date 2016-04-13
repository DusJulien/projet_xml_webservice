<?php

namespace BikeeShop\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text', array('label' => "Prénom"));
        $builder->add('lastName', 'text', array('label' => "Nom"));
        $builder->add('email', 'email', array('label' => "Email"));

        //$builder->add('phone', 'text', array('label' => "N° de téléphone"));
        //$builder->add('address', 'text', array('label' => "Adresse"));
    }

    public function getName()
    {
        return 'customer_form';
    }
}