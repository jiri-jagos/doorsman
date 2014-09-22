<?php

namespace Globalcom\DoormanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * KeyGroup
 *
 * @ORM\Table(name="key_groups")
 * @ORM\Entity(repositoryClass="Globalcom\DoormanBundle\Entity\KeyGroupRepository")
 */
class KeyGroup
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
     * @ORM\Column(name="`desc`", type="string", length=50)
     */
    private $desc;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Globalcom\DoormanBundle\Entity\Key", inversedBy="keyGroups")
     * @ORM\JoinTable(
     *      name="keys_in_groups",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="key_id", referencedColumnName="id")}
     * )
     */
    private $keys;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Globalcom\DoormanBundle\Entity\Entrance", inversedBy="keyGroups")
     * @ORM\JoinTable(
     *      name="groups_entrances",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="entrance_id", referencedColumnName="id")}
     * )
     */
    private $entrances;

    function __construct()
    {
        $this->entrances = new ArrayCollection();
        $this->keys = new ArrayCollection();
    }

    function __toString()
    {
        return $this->desc;
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
     * @return KeyGroup
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
     * @return Collection
     */
    public function getEntrances()
    {
        return $this->entrances;
    }

    /**
     * @param Collection $entrances
     */
    public function setEntrances($entrances)
    {
        $this->entrances = $entrances;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * @param Collection $keys
     */
    public function setKeys($keys)
    {
        $this->keys = $keys;

        return $this;
    }

    /**
     * @param Collection|Key[]|Key $keys
     */
    public function addKeys($keys)
    {
        if (is_array($keys) || $keys instanceof Collection) {
            foreach ($keys as $key) {
                $this->safeAddKey($key);
            }
        } elseif ($keys instanceof Key) {
            $this->safeAddKey($keys);
        }

        return $this;
    }

    private function safeAddKey(Key $key)
    {
        if (!$this->keys->contains($key)) {
            $this->keys->add($key);
        }
    }

    /**
     * @param Collection|Key[]|Key $keys
     */
    public function removeKeys($keys)
    {
        if (is_array($keys) || $keys instanceof Collection) {
            foreach ($keys as $key) {
                $this->safeRemoveKey($key);
            }
        } elseif ($keys instanceof Key) {
            $this->safeRemoveKey($keys);
        }

        return $this;
    }

    private function safeRemoveKey(Key $key)
    {
        if ($this->keys->contains($key)) {
            $this->keys->removeElement($key);
        }
    }
}
