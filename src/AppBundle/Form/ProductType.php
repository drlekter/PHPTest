<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'label' => 'Prodotto: '
            ))
            ->add('tagString', null, array(
                'label' => 'Tags: ',
                'required' => true
            ))
            ->add('uploaded_image','file', array(
                'label' => 'Image: ',
                'required' => false
            ))
            ->add('description', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
                'label' => 'Descrizione: ',
                'required' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product',
        ));
    }
}
