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
 * @ORM\Entity(repositoryClass="App\Repository\MoneyRepository")
 */
class Money implements \JsonSerializable, SnapshotableInterface
{
    use IdTrait;
    use AmountTrait;
    use NotesTrait;
    use DateTrait;
    use DeletedAtTrait;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function jsonSerialize()
    {
        return [
            'amount' => $this->amount,
            'date' => $this->date->format('Y-m-d'),
            'notes' => $this->notes
        ];
    }
}
