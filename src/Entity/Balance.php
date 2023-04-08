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
class Balance implements \JsonSerializable
{
    use IdTrait;
    use AmountTrait;
    use DateTrait;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function jsonSerialize(): array
    {
        return [
            'cantitate' => $this->amount,
            'data' => $this->date->format('Y-m-d')
        ];
    }
}
