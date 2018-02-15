<?php

declare(strict_types=1);

namespace Rinvex\Auth\Traits;

trait HasHashables
{
    /**
     * Get the current hashables for the model.
     *
     * @return array|null
     */
    public function getHashables(): ?array
    {
        return $this->hashables ?? null;
    }

    /**
     * Set the hashables associated with the model.
     *
     * @param array $hashables
     *
     * @return $this
     */
    public function setHashables(array $hashables)
    {
        $this->hashables = $hashables;

        return $this;
    }
}
