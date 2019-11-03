<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController as BaseController;
use App\Services\ParkingService;
use Illuminate\Http\Response;
use App\Discount;
use App\Record;

class ParkingController extends BaseController
{
    private $parkingService;

    public function __construct(ParkingService $service)
    {
        $this->parkingService = $service;
    }

    /**
     * Return current free spaces on the parking
     *
     * @return \Illuminate\Http\Response
     */
    public function getFreeSpaces()
    {
        $freeSpaces = $this->parkingService->getFreeSpaces();

        return $this->sendResponse(['freeSpaces' => $freeSpaces], Response::HTTP_OK);
    }

    /**
     * Return available discounts
     *
     * @return \Illuminate\Http\Response
     */
    public function getDiscounts()
    {
        $discounts = Discount::all();
        return $this->sendResponse(['discounts' => $discounts], Response::HTTP_OK);
    }
}
