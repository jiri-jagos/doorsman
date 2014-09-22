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
            ->add('desc', 'filter_text', array('label' => 'desc'))
            ->add('code', 'filter_text', array('label' => 'code'))
            ->add('house', 'filter_entity', array('class' => 'Globalcom\DoormanBundle\Entity\House', 'label' => 'house', 'empty_value' => 'house'))
            ->add('keyGroups', 'filter_entity', array('class' => 'Globalcom\DoormanBundle\Entity\KeyGroup', 'label' => 'keyGroup', 'empty_value' => 'keyGroup'))
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
            'translation_domain' => 'admin',
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
