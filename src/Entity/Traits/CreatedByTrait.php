<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CreatedByTrait
{
    /**
     * @ORM\Column(type="integer")
     */
    private $createdBy;

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy)
    {
        $this->createdBy = $createdBy;
    }
}
