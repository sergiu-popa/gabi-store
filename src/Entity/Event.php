<?php

namespace App\Entity;

use App\Entity\Traits\AuthorTrait;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\TypeTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    public const TYPE_LOGIN = 'login';
    public const TYPE_LOGOUT = 'logout';

    use IdTrait;
    use AuthorTrait;
    use CreatedAtTrait;
    use TypeTrait;

    public function __construct(UserInterface $author, string $type)
    {
        $this->author = $author;
        $this->type = $type;
        $this->createdAt = new \DateTimeImmutable();
    }
}
