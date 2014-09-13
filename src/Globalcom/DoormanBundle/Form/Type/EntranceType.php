<?php

namespace Globalcom\DoormanBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntranceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('ip', 'text', array('label' => 'ip'))
            ->add('desc', 'text', array('label' => 'desc'))
            ->add('code', 'text', array('label' => 'code'))
            ->add('house', 'entity', array('class' => 'Globalcom\DoormanBundle\Entity\House', 'label' => 'house'))
            ->add('keyGroups', null, array('label' => 'keyGroups'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Globalcom\DoormanBundle\Entity\Entrance',
            'translation_domain' => 'admin',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entrance';
    }
}
