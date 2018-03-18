<?php

namespace App\Bundle\FilmBundle\Film\Repository\Decorator;

use App\Component\Film\Domain\Film;
use App\Component\Film\Domain\Repository\FilmRepository;
use App\Bundle\FilmBundle\Service\CacheService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Bundle\FilmBundle\EventSubscriber\DeleteCache;

final class CachedFilmRepository implements FilmRepository
{
    private $filmRepository;
    private $cache;
    private $dispatcher;

    public function __construct(FilmRepository $filmRepository, CacheService $cache, EventDispatcherInterface $dispatcher)
    {
        $this->filmRepository = $filmRepository;
        $this->cache = $cache;
        $this->dispatcher = $dispatcher;
    }

    public function findById(int $filmId): Film
    {
        $item = $this->cache->fetch((string) 'Film' . $filmId);

        if (!$item) {
            $item = $this->filmRepository->findById($filmId);

            $this->cache->save((string) 'Film' . $filmId, $item);
        }

        return $item;
    }

    public function findAllOrderedByName()
    {
        $item = $this->cache->fetch((string) 'AllFilms');

        if (!$item) {
            $item = $this->filmRepository->findAllOrderedByName();

            $this->cache->save((string) 'AllFilms', $item);
        }

        return $item;
    }

    public function delete(Film $film): void
    {
        $item = $this->cache->fetch((string) 'Film' . $film->getId());

        if ($item) {
            $this->filmRepository->delete($film);
            $this->dispatcher->dispatch(DeleteCache::TOPIC, new DeleteCache((string) 'Film' . $film->getId()));
        }
    }

    public function save(Film $film): void
    {
        $this->filmRepository->save($film);
    }

    public function update(Film $film): void
    {
        $item = $this->cache->fetch((string) 'Film' . $film->getId());

        if ($item) {
            $this->dispatcher->dispatch(DeleteCache::TOPIC, new DeleteCache((string) 'Film' . $film->getId()));
        }

        $this->filmRepository->update($film);
    }
}