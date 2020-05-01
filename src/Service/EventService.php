<?php

namespace App\Service;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class EventService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function login(): void
    {
        $this->em->persist(new Event($this->security->getUser(), Event::TYPE_LOGIN));
        $this->em->flush();
    }

    public function logout(): void
    {
        $this->em->persist(new Event($this->security->getUser(), Event::TYPE_LOGOUT));
        $this->em->flush();
    }
}
