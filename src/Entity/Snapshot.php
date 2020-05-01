<?php

namespace App\Entity;

use App\Entity\Traits\AuthorTrait;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\TypeTrait;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Snapshot.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SnapshotRepository")
 */
class Snapshot
{
    use IdTrait;
    use AuthorTrait;
    use CreatedAtTrait;
    use TypeTrait;

    public const TYPE_CREATE = 'create';
    public const TYPE_UPDATE = 'update';
    public const TYPE_DELETE = 'delete';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $entityId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $class;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $content;

    public function __construct(UserInterface $author, string $content, string $class, int $objectId, string $type)
    {
        $this->author = $author;
        $this->content = $content;
        $this->class = $class;
        $this->entityId = $objectId;
        $this->createdAt = new \DateTimeImmutable();
        $this->type = $type;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
