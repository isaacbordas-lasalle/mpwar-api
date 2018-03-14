<?php

namespace App\Component\Film\Application\UseCase\Film;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Bundle\FilmBundle\EventSubscriber\DeleteCache;
use App\Component\Film\Application\Exception\InvalidFilmIdException;

class DeleteFilmUseCase
{
    private $entityManager;
    private $dispatcher;

    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    public function execute(int $filmId)
    {
        if(empty($filmId)) {
            throw InvalidFilmIdException::empty();
        }

        $film = $this->entityManager->getReference('\App\Component\Film\Domain\Film', $filmId);

        $this->entityManager->remove($film);
        $this->entityManager->flush();

        $this->dispatcher->dispatch(DeleteCache::TOPIC, new DeleteCache());
    }
}