<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait PaidPartiallyTrait
{
    /**
     * @ORM\Column(type="boolean")
     */
    private $paidPartially;

    public function getPaidPartially(): ?bool
    {
        return $this->paidPartially;
    }

    public function setPaidPartially(bool $paidPartially): self
    {
        $this->paidPartially = $paidPartially;

        return $this;
    }
}
