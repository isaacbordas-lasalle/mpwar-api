<?php

namespace App\Bundle\FilmBundle\Film\API\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Component\Film\Application\Exception\InvalidFilmIdException;

class DeleteFilmController extends Controller
{
    public function execute(Request $request)
    {
        $json = json_decode($request->getContent(), true);
        $id = filter_var($json['id'] ?? '', FILTER_SANITIZE_NUMBER_INT);

        try {
            $deleteFilmUseCase = $this->get('app.film.usecase.delete');
            $deleteFilmUseCase->execute($id);
            return new JsonResponse('', 204);
        } catch (InvalidFilmIdException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}