<?php

namespace Globalcom\DoormanBundle\DomainObject;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class KeyGroupAssignKeys
{
    /** @var  Collection */
    private $keysToAdd;

    /** @var  Collection */
    private $keysToRemove;

    function __construct()
    {
        $this->keysToAdd = new ArrayCollection();
        $this->keysToRemove = new ArrayCollection();
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
