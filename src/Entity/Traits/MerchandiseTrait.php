<?php

namespace App\Entity\Traits;

use App\Entity\Merchandise;
use Doctrine\ORM\Mapping as ORM;

trait MerchandiseTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Merchandise")
     */
    private $merchandise;

    public function getMerchandise(): ?Merchandise
    {
        return $this->merchandise;
    }

    public function setMerchandise(?Merchandise $merchandise): self
    {
        $this->merchandise = $merchandise;

        return $this;
    }
}
