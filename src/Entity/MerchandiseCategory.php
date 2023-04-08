<?php

namespace App\Entity;

use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\NameTrait;
use App\Util\SnapshotableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MerchandiseCategoryRepository")
 * @UniqueEntity("name")
 */
class MerchandiseCategory implements \JsonSerializable, SnapshotableInterface
{
    use IdTrait;
    use NameTrait;
    use DeletedAtTrait;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Merchandise", mappedBy="category")
     */
    private $merchandise;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     *
     */
    private $code;

    public function __construct()
    {
        $this->merchandise = new ArrayCollection();
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name
        ];
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
        return sprintf('%s (%s)', $this->code, $this->name);
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
