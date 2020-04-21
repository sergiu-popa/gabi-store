<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait DeletedByTrait
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $deletedBy;

    public function getDeletedBy(): ?int
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(int $deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }
}
