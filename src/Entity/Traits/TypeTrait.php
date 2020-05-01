<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait TypeTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     */
    protected $type;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
