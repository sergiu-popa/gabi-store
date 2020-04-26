<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait AmountTrait
{
    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     * @Assert\Type("numeric")
     * @Assert\Positive()
     */
    private $amount;

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
