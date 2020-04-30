<?php

namespace App\EventListener;

use App\Entity\Snapshot;
use App\Service\SnapshotService;
use App\Util\SnapshotableInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

/**
 * Class SnapshotListener.
 */
class SnapshotListener
{
    /** @var Security */
    protected $security;

    /** @var SnapshotService */
    protected $snapshotService;

    public function __construct(
        Security $security,
        SnapshotService $snapshotService
    ) {
        $this->security = $security;
        $this->snapshotService = $snapshotService;
    }

    /**
     * @param $object
     *
     * @return bool
     */
    private function canCreateSnapshot($object): bool
    {
        return $this->isAuthenticated() && $this->isSnapshotable($object);
    }

    /**
     * @return bool
     */
    private function isAuthenticated(): bool
    {
        $token = $this->security->getToken();

        return $token ? $token->isAuthenticated() : false;
    }

    /**
     * @param $object
     *
     * @return bool
     */
    private function isSnapshotable($object): bool
    {
        return $object instanceof SnapshotableInterface;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        if ($this->canCreateSnapshot($args->getObject())) {
            $this->createEntitySnapshot($args->getObject(), Snapshot::TYPE_CREATE);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        if ($this->canCreateSnapshot($args->getObject())) {
            $entity = $args->getObject();
            $type = $entity->isDeleted() ? Snapshot::TYPE_DELETE : Snapshot::TYPE_UPDATE;
            $this->createEntitySnapshot($entity, $type);
        }
    }

    /**
     * @param mixed $entity
     */
    private function createEntitySnapshot($entity, string $type): void
    {
        $author = $this->security->getToken()->getUser();
        $snapshot = $this->snapshotService->createSnapshot($author, $entity, $type);
        $this->snapshotService->persistSnapshot($snapshot);
    }
}
