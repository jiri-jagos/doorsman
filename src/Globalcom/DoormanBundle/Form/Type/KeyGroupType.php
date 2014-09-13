<?php

namespace Globalcom\DoormanBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyGroupType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('desc', 'text', array('label' => 'desc'))
            ->add(
                'keys', 'entity', array(
                    'class' => 'Globalcom\DoormanBundle\Entity\Key',
                    'property' => 'fullName',
                    'label' => 'keys',
                    'multiple' => true,
                    'expanded' => false
                )
            )
            ->add(
                'entrances', 'entity', array(
                    'class' => 'Globalcom\DoormanBundle\Entity\Entrance',
                    'property' => 'fullName',
                    'label' => 'entrances',
                    'multiple' => true,
                    'expanded' => true
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Globalcom\DoormanBundle\Entity\KeyGroup',
                'translation_domain' => 'admin',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'keygroup';
    }
}
