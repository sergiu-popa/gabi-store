<?php

namespace App\Entity\Traits;

use App\Entity\Snapshot;

trait SnapshotsTrait
{
    /** @var Snapshot[] */
    private $snapshots;

    /**
     * @return Snapshot[]
     */
    public function getSnapshots(): array
    {
        return $this->snapshots;
    }

    /**
     * @param Snapshot[] $snapshots
     */
    public function setSnapshots(array $snapshots): void
    {
        $this->snapshots = $snapshots;
    }
}
