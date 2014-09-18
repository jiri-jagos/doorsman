<?php

namespace Globalcom\DoormanBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyGroupFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('desc', 'filter_text', array('label' => 'desc'))
            ->add('keys', 'filter_entity', array('class' => 'Globalcom\DoormanBundle\Entity\Key', 'label' => 'keys', 'empty_value' => 'keys'))
            ->add('entrances', 'filter_entity', array('class' => 'Globalcom\DoormanBundle\Entity\Entrance', 'property' => 'fullName', 'label' => 'entrances', 'empty_value' => 'entrances'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Globalcom\DoormanBundle\Entity\KeyGroup',
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
        return 'keygroup_filter';
    }
}
