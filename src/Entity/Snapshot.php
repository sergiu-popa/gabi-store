<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Snapshot.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SnapshotRepository")
 */
class Snapshot
{
    public const TYPE_CREATE = 'create';
    public const TYPE_UPDATE = 'update';
    public const TYPE_DELETE = 'delete';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $objectId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $class;
    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    protected $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $content;


    /**
     * Snapshot constructor.
     *
     * @param UserInterface $author
     * @param string        $content
     * @param string        $class
     * @param int           $objectId
     */
    public function __construct(
        UserInterface $author,
        string $content,
        string $class,
        int $objectId,
        string $type
    ) {
        $this->author = $author;
        $this->content = $content;
        $this->class = $class;
        $this->objectId = $objectId;
        $this->createdAt = new \DateTimeImmutable();
        $this->type = $type;
    }

    /**
     * @return UserInterface
     */
    public function getAuthor(): UserInterface
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return int
     */
    public function getObjectId(): int
    {
        return $this->objectId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
