<?php

namespace App\Controller;

use App\Request\NextDrawDayRequest;
use App\Service\NextDrawDayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class NextDrawDayController extends AbstractController
{

    public function index(
        Request $request,
        NextDrawDayRequest $exchangeRatesRequest,
        NextDrawDayService $service

    ): Response {
        try { 
            
        $validatedData  = $exchangeRatesRequest->validated($request);
        $result         = $service->nextLotteryDate($validatedData['date'], 'API');
        return new JsonResponse(['data' => $result]);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(["message" => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
