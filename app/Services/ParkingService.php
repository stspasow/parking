<?php
namespace App\Services;

use App\Record;
use App\Vehicle;
use App\Exceptions\NoFreeSpacesException;
use App\Exceptions\VehicleAlreadyRegisteredException;
use App\Exceptions\MissingVehicleException;

class ParkingService
{
    public function isVehicleAlreadyRegistered($plateNumber)
    {
        $record = Record::where('plateNumber', '=', $plateNumber)->get();
        if ($record->isNotEmpty()) {
            throw new VehicleAlreadyRegisteredException('Vehicle with plate number ' . $plateNumber . 'is already registered.');
        }
        return $record;
    }

    public function vehicleExists($plateNumber)
    {
        $record = Record::where('plateNumber', '=', $plateNumber)->get();
        if ($record->isEmpty()) {
            throw new MissingVehicleException('Vehicle not found by plate number ' . $plateNumber);
        }
        return $record->first();
    }

    public function checkForFreeSpaces(Vehicle $vehicle) {
        $freeSpaces = $this->getFreeSpaces();
        if ($freeSpaces < $vehicle->size) {
            throw new NoFreeSpacesException('No free spaces');
        }
    }

    public function getFreeSpaces()
    {
        $takenSpaces = Record::all()->sum('vehicleType.size');
        $freeSpaces = config('constants.parking.total_spaces') - $takenSpaces;

        return max($freeSpaces, 0);
    }

}