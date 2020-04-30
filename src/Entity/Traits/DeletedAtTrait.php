<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait DeletedAtTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    public function delete(): void
    {
        $this->deletedAt = new \DateTime();
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
}
