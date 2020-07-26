<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\DateTrait;
use App\Util\SnapshotableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BalanceRepository")
 * @UniqueEntity("date")
 */
class Balance implements \JsonSerializable, SnapshotableInterface
{
    use IdTrait;
    use AmountTrait;
    use DateTrait;
    use DeletedAtTrait;

    private $recalculatedAmount;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function amountHasChanged(): bool {
        return $this->amount !== $this->recalculatedAmount;
    }

    public function jsonSerialize()
    {
        return [
            'cantitate' => $this->amount,
            'data' => $this->date->format('Y-m-d')
        ];
    }

    public function getRecalculatedAmount(): ?float
    {
        return $this->recalculatedAmount;
    }

    public function setRecalculatedAmount(float $recalculatedAmount): void
    {
        $this->recalculatedAmount = $recalculatedAmount;
    }
}
