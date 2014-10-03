<?php

namespace Globalcom\DoormanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entrance
 *
 * @ORM\Table(name="entrances")
 * @ORM\Entity(repositoryClass="Globalcom\DoormanBundle\Entity\EntranceRepository")
 */
class Entrance
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
     * @ORM\Column(name="ip", type="string", length=20)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Globalcom\DoormanBundle\Entity\House", inversedBy="entrances")
     * @ORM\JoinColumn(name="house")
     */
    private $house;

    /**
     * @var string
     *
     * @ORM\Column(name="`desc`", type="string", length=50)
     */
    private $desc;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50)
     */
    private $code;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Globalcom\DoormanBundle\Entity\KeyGroup", mappedBy="entrances")
     * @ORM\JoinTable(
     *      name="groups_entrances",
     *      joinColumns={@ORM\JoinColumn(name="entrance_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     *
     * #ORM\OneToMany(targetEntity="Globalcom\DoormanBundle\Entity\KeyGroupHasEntrance", mappedBy="entrance")
     */
    private $keyGroups;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Globalcom\DoormanBundle\Entity\Key", inversedBy="entrances", cascade={"all"})
     * @ORM\JoinTable(
     *      name="keys_entrances",
     *      joinColumns={@ORM\JoinColumn(name="entrance_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="key_id", referencedColumnName="id")}
     * )
     */
    private $keys;

    function __construct()
    {
        $this->keyGroups = new ArrayCollection();
        $this->keys = new ArrayCollection();
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
     * Set ip
     *
     * @param string $ip
     * @return Entrance
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set house(int)
     *
     * @param string $house (int)
     * @return Entrance
     */
    public function setHouse($house)
    {
        $this->house = $house;

        return $this;
    }

    /**
     * Get house(int)
     *
     * @return string
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * Set desc
     *
     * @param string $desc
     * @return Entrance
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
     * Set code
     *
     * @param string $code
     * @return Entrance
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

    public function addKeyGroups($keyGroups)
    {
        if (is_array($keyGroups) || $keyGroups instanceof Collection) {
            foreach ($keyGroups as $keyGroup) {
                $this->safeAddKeyGroup($keyGroup);
            }
        } elseif (1) {
//            TODO: Finish or delete
        }

        return $this;
    }

    private function safeAddKeyGroup(KeyGroup $keyGroup)
    {
        if (!$this->keyGroups->contains($keyGroup)) {
            $this->keyGroups->add($keyGroup);
            $keyGroup->addEntrances($this);
        }

        return $this;
    }

    public function removeKeyGroups($keyGroups)
    {
        if (is_array($keyGroups) || $keyGroups instanceof Collection) {
            foreach ($keyGroups as $keyGroup) {
                $this->safeRemoveKeyGroup($keyGroup);
            }
        } elseif ($keyGroups instanceof KeyGroup) {
            $this->safeRemoveKeyGroup($keyGroups);
        }

        return $this;
    }

    private function safeRemoveKeygroup(KeyGroup $keyGroup)
    {
        if ($this->keyGroups->contains($keyGroup)) {
            $this->keyGroups->removeElement($keyGroup);
            $keyGroup->removeEntrances($this);
        }

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

        return $this;
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

    public function getFullName()
    {
        return trim($this->house->getDesc()) . ' / ' . $this->desc . '(' . $this->code . ')';
    }
}
