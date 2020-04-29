<?php

namespace App\Entity;

use App\Entity\Traits\DeleteTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MerchandiseCategoryRepository")
 * @UniqueEntity("name")
 */
class MerchandiseCategory
{
    use IdTrait;
    use NameTrait;
    use DeleteTrait;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Merchandise", mappedBy="category")
     */
    private $merchandise;

    public function __construct()
    {
        $this->merchandise = new ArrayCollection();
    }

    /**
     * @return Collection|Merchandise[]
     */
    public function getMerchandise(): Collection
    {
        return $this->merchandise;
    }

    public function addMerchandise(Merchandise $merchandise): self
    {
        if (!$this->merchandise->contains($merchandise)) {
            $this->merchandise[] = $merchandise;
            $merchandise->setCategory($this);
        }

        return $this;
    }

    public function removeMerchandise(Merchandise $merchandise): self
    {
        if ($this->merchandise->contains($merchandise)) {
            $this->merchandise->removeElement($merchandise);
            // set the owning side to null (unless already changed)
            if ($merchandise->getCategory() === $this) {
                $merchandise->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
