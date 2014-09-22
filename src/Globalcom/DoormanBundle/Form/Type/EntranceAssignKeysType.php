<?php

namespace Globalcom\DoormanBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Globalcom\DoormanBundle\Entity\KeyGroupRepository;
use Globalcom\DoormanBundle\Entity\KeyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntranceAssignKeysType extends AbstractType
{
    /** @var ObjectManager */
    private $om;

    function __construct($om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var KeyGroupRepository $keyGroupRepo */
        $keyGroupRepo = $this->om->getRepository('GlobalcomDoormanBundle:KeyGroup');

        /** @var KeyRepository $keyRepo */
        $keyRepo = $this->om->getRepository('GlobalcomDoormanBundle:Key');

        $entrance = $options['entrance'];
        $builder
            ->add('addToEntrance', 'submit')
            ->add('removeFromEntrance', 'submit')
            ->add(
                'keyGroupsToRemove',
                'entity',
                array(
                    'class' => 'Globalcom\DoormanBundle\Entity\KeyGroup',
                    'property' => 'desc',
                    'query_builder' => $keyGroupRepo->getQbAllInEntrance($entrance),
                    'required' => false,
                    'label' => 'Active KeyGroups',
                    'multiple' => true,
                    'expanded' => false,
                    'attr' => array('size' => '10'),
                )
            )
            ->add(
                'keyGroupsToAdd',
                'entity',
                array(
                    'class' => 'Globalcom\DoormanBundle\Entity\KeyGroup',
                    'property' => 'desc',
                    'query_builder' => $keyGroupRepo->getQbAllInEntrance($entrance),
                    'required' => false,
                    'label' => 'All KeyGroups',
                    'multiple' => true,
                    'expanded' => false,
                    'attr' => array('size' => '10'),
                )
            )
            ->add(
                'keysToRemove',
                'entity',
                array(
                    'class' => 'Globalcom\DoormanBundle\Entity\Key',
                    'property' => 'fullName',
                    'query_builder' => $keyRepo->getQbAllInEntrance($entrance),
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
                    'query_builder' => $keyRepo->getQbAllNotInEntrance($entrance),
                    'required' => false,
                    'label' => 'All Keys',
                    'multiple' => true,
                    'expanded' => false,
                    'attr' => array('size' => '20')
                )
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                array(
                    'data_class' => 'Globalcom\DoormanBundle\DomainObject\EntranceAssignKeys',
                    'translation_domain' => 'admin',
                    'entrance' => null,
                )
            )
            ->setAllowedTypes(array('entrance' => 'Globalcom\DoormanBundle\Entity\Entrance'))
            ->setRequired(array('entrance'));
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entranceAssignKeysType';
    }
}
