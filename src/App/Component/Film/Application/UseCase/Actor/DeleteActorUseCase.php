<?php

namespace App\Component\Film\Application\UseCase\Actor;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Bundle\FilmBundle\EventSubscriber\DeleteCache;

class DeleteActorUseCase
{
    private $entityManager;
    private $dispatcher;

    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    public function execute(int $actorId)
    {
        $actor = $this->entityManager->getReference('\App\Component\Film\Domain\Actor', $actorId);

        $this->entityManager->remove($actor);
        $this->entityManager->flush();

        $this->dispatcher->dispatch(DeleteCache::TOPIC, new DeleteCache());
    }
}