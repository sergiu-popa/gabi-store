<?php

namespace App\Entity\Traits;

use App\Entity\User;

trait DeleteTrait
{
    public function delete(User $user)
    {
        $this->deletedAt = new \DateTime();
        $this->deletedBy = $user->getId();
    }
}
