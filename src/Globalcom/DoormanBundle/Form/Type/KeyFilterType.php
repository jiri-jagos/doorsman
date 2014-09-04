<?php

namespace Globalcom\DoormanBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('code', 'filter_text')
            ->add('desc', 'filter_text')
            ->add('color', 'filter_text')
            ->add('number', 'filter_text')
            ->add('text', 'filter_text')
            ->add('createdAt', 'filter_date')
            ->add('keyGroups', 'filter_entity', array('class' => 'Globalcom\DoormanBundle\Entity\KeyGroup'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Globalcom\DoormanBundle\Entity\Key',
            'csrf_protection'   => false,
            'validation_groups' => array('filter'),
            'method'            => 'GET',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'key_filter';
    }
}
