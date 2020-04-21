<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\NotesTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExpenseRepository")
 */
class Expense
{
    use IdTrait;
    use AmountTrait;
    use DateTrait;
    use NotesTrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="expenses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
