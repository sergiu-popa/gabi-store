<?php

namespace App\Entity\Traits;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

trait AuthorTrait
{
    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

    public function getAuthor(): UserInterface
    {
        return $this->author;
    }

    public function setAuthor(UserInterface $author): void
    {
        $this->author = $author;
    }
}
