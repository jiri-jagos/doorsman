<?php

namespace Globalcom\DoormanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * House
 *
 * @ORM\Table(name="houses")
 * @ORM\Entity(repositoryClass="Globalcom\DoormanBundle\Entity\HouseRepository")
 */
class House
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
     * @var string
     *
     * @ORM\Column(name="house_desc", type="string", length=50)
     */
    private $desc;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=50)
     */
    private $town;

    public function __toString()
    {
        return $this->town . ' - ' . $this->desc;
    }

    public function getFullName()
    {
        return $this->town . ' - ' . $this->desc;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set desc
     *
     * @param string $desc
     * @return House
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;

        return $this;
    }

    /**
     * Get desc
     *
     * @return string 
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * Set town
     *
     * @param string $town
     * @return House
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string 
     */
    public function getTown()
    {
        return $this->town;
    }
}
