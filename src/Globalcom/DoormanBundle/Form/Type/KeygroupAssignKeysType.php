<?php

namespace Globalcom\DoormanBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Globalcom\DoormanBundle\Entity\Key;
use Globalcom\DoormanBundle\Entity\KeyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeygroupAssignKeysType extends AbstractType
{
    /** @var ObjectManager */
    private $om;

    function __construct($om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var KeyRepository $keyRepo */
        $keyRepo = $this->om->getRepository('Globalcom\DoormanBundle\Entity\Key');

        $keyGroup = $options['keyGroup'];
        $builder
            ->add('addToGroup', 'submit')
            ->add('removeFromGroup', 'submit')
            ->add(
                'keysToRemove',
                'entity',
                array(
                    'class' => 'Globalcom\DoormanBundle\Entity\Key',
                    'property' => 'fullName',
                    'query_builder' => $keyRepo->getQbAllInKeygroup($keyGroup),
                    'required' => false,
                    'label' => 'Active Keys',
                    'multiple' => true,
                    'expanded' => false,
                    'attr' => array('size' => '20'),
                )
            )
            ->add(
                'keysToAdd',
                'entity',
                array(
                    'class' => 'Globalcom\DoormanBundle\Entity\Key',
                    'property' => 'fullName',
                    'query_builder' => $keyRepo->getQbAllNotInKeygroup($keyGroup),
                    'required' => false,
                    'label' => 'All Keys',
                    'multiple' => true,
                    'expanded' => false,
                    'attr' => array('size' => '20')
                )
            )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                array(
                    'data_class' => 'Globalcom\DoormanBundle\DomainObject\KeyGroupAssignKeys',
                    'keyGroup' => null,
                    'translation_domain' => 'admin',
                )
            )
            ->setAllowedTypes(array('keyGroup' => 'Globalcom\DoormanBundle\Entity\KeyGroup'))
            ->setRequired(array('keyGroup'));
    }

    public function getName()
    {
        return 'keygroupAssignKeysType';
    }

} 
