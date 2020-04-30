<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\NotesTrait;
use App\Util\SnapshotableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExpenseRepository")
 */
class Expense implements \JsonSerializable, SnapshotableInterface
{
    use IdTrait;
    use AmountTrait;
    use DateTrait;
    use NotesTrait;
    use DeletedAtTrait;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * @ORM\ManyToOne(targetEntity="ExpenseCategory", inversedBy="expenses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    public function getCategory(): ?ExpenseCategory
    {
        return $this->category;
    }

    public function setCategory(?ExpenseCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'descriere' => $this->notes,
            'categorie' => $this->category->getName(),
            'cantitate' => $this->amount,
            'data' => $this->date->format('Y-m-d')
        ];
    }
}
