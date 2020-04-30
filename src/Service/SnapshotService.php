<?php

namespace App\Service;

use App\Entity\Snapshot;
use App\Repository\SnapshotRepository;
use App\Util\SnapshotableInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SnapshotService.
 */
class SnapshotService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SnapshotRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em, SnapshotRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * @param UserInterface         $author
     * @param SnapshotableInterface $snapshotable
     *
     * @return Snapshot
     */
    public function createSnapshot(UserInterface $author, SnapshotableInterface $snapshotable, string $type): Snapshot
    {
        $content = json_encode($snapshotable);
        $class = $this->getSnapshotableClass($snapshotable);

        // TODO type: creation, modified, deleted (isDeleted)

        return new Snapshot($author, $content, $class, $snapshotable->getId(), $type);
    }

    /**
     * @param Snapshot $snapshot
     */
    public function persistSnapshot(Snapshot $snapshot): void
    {
        $this->em->persist($snapshot);
        $this->em->flush();
    }

    /**
     * @param SnapshotableInterface $snapshotable
     * @param int                   $maxResults
     *
     * @return array
     */
    public function getSnapshotList(SnapshotableInterface $snapshotable, int $maxResults = 4): array
    {
        return $this->repository->findByIdAndClass(
            $snapshotable->getSnapshotObjectId(),
            $this->getSnapshotableClass($snapshotable),
            $maxResults
        );
    }

    /**
     * @param SnapshotableInterface $snapshotable
     *
     * @return string
     */
    private function getSnapshotableClass(SnapshotableInterface $snapshotable): string
    {
        return \get_class($snapshotable);
    }
}
