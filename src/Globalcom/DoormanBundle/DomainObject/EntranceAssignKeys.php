<?php

namespace Globalcom\DoormanBundle\DomainObject;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class EntranceAssignKeys
{
    /** @var  Collection */
    private $keyGroupsToAdd;

    /** @var  Collection */
    private $keyGroupsToRemove;

    /** @var  Collection */
    private $keysToAdd;

    /** @var  Collection */
    private $keysToRemove;

    function __construct()
    {
        $this->keyGroupsToAdd = new ArrayCollection();
        $this->keyGroupsToRemove = new ArrayCollection();
        $this->keysToAdd = new ArrayCollection();
        $this->keysToRemove = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getKeyGroupsToAdd()
    {
        return $this->keyGroupsToAdd;
    }

    /**
     * @param Collection $keyGroupsToAdd
     */
    public function setKeyGroupsToAdd($keyGroupsToAdd)
    {
        $this->keyGroupsToAdd = $keyGroupsToAdd;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getKeyGroupsToRemove()
    {
        return $this->keyGroupsToRemove;
    }

    /**
     * @param Collection $keyGroupsToRemove
     */
    public function setKeyGroupsToRemove($keyGroupsToRemove)
    {
        $this->keyGroupsToRemove = $keyGroupsToRemove;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getKeysToAdd()
    {
        return $this->keysToAdd;
    }

    /**
     * @param Collection $keysToAdd
     */
    public function setKeysToAdd($keysToAdd)
    {
        $this->keysToAdd = $keysToAdd;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getKeysToRemove()
    {
        return $this->keysToRemove;
    }

    /**
     * @param Collection $keysToRemove
     */
    public function setKeysToRemove($keysToRemove)
    {
        $this->keysToRemove = $keysToRemove;

        return $this;
    }


}
