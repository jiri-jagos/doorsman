<?php

namespace Globalcom\DoormanBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntranceFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('ip', 'filter_text')
            ->add('desc', 'filter_text')
            ->add('code', 'filter_text')
            ->add('house', 'filter_entity', array('class' => 'Globalcom\DoormanBundle\Entity\House'))
            ->add('keyGroups', 'filter_entity', array('class' => 'Globalcom\DoormanBundle\Entity\KeyGroup'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Globalcom\DoormanBundle\Entity\Entrance',
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
        return 'entrance_filter';
    }
}
