<?php

namespace App\Util;

interface SnapshotableInterface {
    public function getId(): ?int;
    public function isDeleted(): bool;
}
