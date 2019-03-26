<?php

namespace Model\Entity;


class ConnectionTry extends Entity
{
    protected $count;
    protected $lastTry;
    protected $user;

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count): void
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getLastTry()
    {
        return $this->lastTry;
    }

    /**
     * @param mixed $lastTry
     */
    public function setLastTry($lastTry): void
    {
        $this->lastTry = $lastTry;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }
}
