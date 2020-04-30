<?php

namespace App\Entity;

use App\Entity\Traits\DateTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Util\SnapshotableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DayRepository")
 */
class Day implements \JsonSerializable, SnapshotableInterface
{
    use IdTrait;
    use DateTrait;
    use DeletedAtTrait;

    private $startedAt;

    private $endedAt;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->startedAt = new \DateTime();
    }

    // TODO 50 & 100 bils fields (small int unsigned)
    // TODO revised 1

    public function jsonSerialize()
    {
        return [
            'data' => $this->date->format('Y-m-d')
        ];
    }
}
