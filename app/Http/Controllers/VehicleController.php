<?php

namespace App\Http\Controllers;

use App\Discount;
use App\Exceptions\MissingVehicleException;
use App\Exceptions\NoFreeSpacesException;
use App\Exceptions\VehicleAlreadyRegisteredException;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\RegisterRequest;
use App\Record;
use App\Services\ParkingService;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Response;

class VehicleController extends BaseController
{
    private $parkingService;

    public function __construct(ParkingService $service)
    {
        $this->parkingService = $service;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\RegisterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request)
    {
        //check if vehicle is already in the parking
        try {
            $this->parkingService->isVehicleAlreadyRegistered($request['plateNumber']);
        } catch (VehicleAlreadyRegisteredException $exception) {
            return $this->sendError($exception->getMessage(), ["Vehicle already in"]);
        }

        $vehicleType = Vehicle::where('category', '=', $request['vehicleType'])->first();

        $vehicle = new Vehicle([
            'category' => $vehicleType->category,
            'size' => $vehicleType->size,
            'tariffs' => [
                'daily' => $vehicleType->tariffs['daily'],
                'nightly' => $vehicleType->tariffs['nightly'],
            ],
        ]);

        //check if parking is full
        try {
            $this->parkingService->checkForFreeSpaces($vehicle);
        } catch (NoFreeSpacesException $exception) {
            return $this->sendError($exception->getMessage(), ["Parking is full"]);
        }

        $record = Record::create([
            'plateNumber' => $request['plateNumber'],
            'entryTime' => Carbon::now(),
        ]);

        if ($request['discount']) {
            $discountType = Discount::where('type', '=', $request['discount'])->first();
            $discount = new Discount(['type' => $discountType->type, 'percent' => $discountType->percent]);
            $record->discount()->save($discount);
        }

        $record->vehicleType()->save($vehicle);
        $record->save();

        return $this->sendResponse([], Response::HTTP_CREATED, Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $plateNumber
     * @return \Illuminate\Http\Response
     */
    public function destroy($plateNumber)
    {
        try {
            $record = $this->parkingService->vehicleExists($plateNumber);
        } catch (MissingVehicleException $exception) {
            return $this->sendError($exception->getMessage(), ["Vehicle not found"]);
        }

        $fee = $this->calculateFee($record);
        $record->delete();

        return $this->sendResponse(['fee' => $fee], Response::HTTP_OK);
    }

    public function getVehicleTypes()
    {
        $vehicleTypes = Vehicle::all();
        return $this->sendResponse(['vehicleTypes' => $vehicleTypes], Response::HTTP_OK);
    }

    /**
     * Return current fee of a vehicle.
     *
     * @param  string  $plateNumber
     * @return \Illuminate\Http\Response
     */
    public function getCurrentFee($plateNumber)
    {
        try {
            $record = $this->parkingService->vehicleExists($plateNumber);
        } catch (MissingVehicleException $exception) {
            return $this->sendError($exception->getMessage(), ["Vehicle not found"]);
        }

        $fee = $this->calculateFee($record);

        return $this->sendResponse(['fee' => $fee], Response::HTTP_OK);
    }

    /**
     * Return current fee of a vehicle.
     *
     * @param  App\Record $record
     * @return double
     */
    private function calculateFee(Record $record)
    {
        $entry = new Carbon($record->entryTime['date']);
        $entry->minute(0)->second(0)->microsecond(0);

        $now = Carbon::now();

        if ($now->minute != 0) {
            $now->addHour();
        }
        $now->minute(0)->second(0)->microsecond(0);

        $totalStayHours = $entry->diffInHours($now, true);

        $dailyTariffHours = $entry->diffInHoursFiltered(function (Carbon $date) {
            $first = $date->copy()->hour(config('constants.parking.daily_tariff_start_hour'));
            $second = $date->copy()->hour(config('constants.parking.daily_tariff_end_hour'))->subMinute();

            return $date->isBetween($first, $second, true);
        }, $now);

        $discount = 0;
        if ($record->discount) {
            $discount = $record->discount->percent;
        }
        $fee = (1 - $discount) * ($dailyTariffHours * $record->vehicleType->tariffs['daily'] + max(($totalStayHours - $dailyTariffHours), 0) * $record->vehicleType->tariffs['nightly']);

        return $fee;
    }
}
