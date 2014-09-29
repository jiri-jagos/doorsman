<?php

namespace Globalcom\DoormanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * KeyGroupHasEntrance
 *
 * @ORM\Table(name="groups_entrances")
 * @ORM\Entity()
 * #ORM\Entity(repositoryClass="Globalcom\DoormanBundle\Entity\EntranceRepository")
 */

class KeyGroupHasEntrance
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var KeyGroup
     *
     * @ORM\ManyToOne(targetEntity="Globalcom\DoormanBundle\Entity\KeyGroup", inversedBy="entrances")
     * @ORM\Column(name="group_id")
     */
    private $keyGroup;

    /**
     * @var Entrance
     *
     * @ORM\ManyToOne(targetEntity="Globalcom\DoormanBundle\Entity\Entrance", inversedBy="keyGroups")
     * @ORM\Column(name="entrance_id")
     */
    private $entrance;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Entrance
     */
    public function getEntrance()
    {
        return $this->entrance;
    }

    /**
     * @param Entrance $entrance
     */
    public function setEntrance($entrance)
    {
        $this->entrance = $entrance;

        return $this;
    }

    /**
     * @return KeyGroup
     */
    public function getKeyGroup()
    {
        return $this->keyGroup;
    }

    /**
     * @param KeyGroup $keyGroup
     */
    public function setKeyGroup($keyGroup)
    {
        $this->keyGroup = $keyGroup;

        return $this;
    }
}
