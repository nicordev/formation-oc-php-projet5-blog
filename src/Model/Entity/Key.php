<?php

namespace Model\Entity;


class Key extends Entity
{
    protected $value = null;

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}
