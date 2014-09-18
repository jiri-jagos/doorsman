<?php

namespace Globalcom\DoormanBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Key
 *
 * @ORM\Table(name="keys")
 * @ORM\Entity(repositoryClass="Globalcom\DoormanBundle\Entity\KeyRepository")
 */
class Key
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
     * @ORM\Column(name="keycode", type="string", length=16)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="key_desc", type="string", length=50)
     */
    private $desc;

    /**
     * @var string
     *
     * @ORM\Column(name="key_color", type="string", length=50)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="key_number", type="string", length=50)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="key_text", type="string", length=100)
     */
    private $text;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="insert_date", type="datetime"  )
     */
    private $createdAt;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Globalcom\DoormanBundle\Entity\KeyGroup", mappedBy="keys")
     */
    private $keyGroups;

    public function __toString()
    {
        return $this->desc . ' - ' . $this->code;
    }

    public function getFullName()
    {
        return $this->desc . ' - ' . $this->color;
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
     * Set code
     *
     * @param string $code
     * @return Key
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set desc
     *
     * @param string $desc
     * @return Key
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
     * Set color
     *
     * @param string $color
     * @return Key
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return Key
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Key
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Key
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return Collection
     */
    public function getKeyGroups()
    {
        return $this->keyGroups;
    }

    /**
     * @param Collection $keyGroups
     */
    public function setKeyGroups($keyGroups)
    {
        $this->keyGroups = $keyGroups;

        return $this;
    }
}
