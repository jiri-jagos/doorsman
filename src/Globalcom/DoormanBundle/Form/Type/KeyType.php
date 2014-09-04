<?php

namespace Globalcom\DoormanBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('code')
            ->add('desc')
            ->add('color')
            ->add('number')
            ->add('text')
            ->add('createdAt')
            ->add('keyGroups')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Globalcom\DoormanBundle\Entity\Key',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'key';
    }
}
