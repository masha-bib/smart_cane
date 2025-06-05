<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LocationController extends Controller
{
    public function updateLocation(Request $request)
    {
        $validatedData = $request->validate([
            'device_id' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90', 
            'longitude' => 'required|numeric|between:-180,180', 
            'satellites' => 'sometimes|nullable|integer|min:0',
            'hdop' => 'sometimes|nullable|numeric|min:0',
        ]);

        try {
            $location = Location::create([
                'device_id' => $validatedData['device_id'],
                'latitude' => $validatedData['latitude'],
                'longitude' => $validatedData['longitude'],
                'satellites' => $validatedData['satellites'] ?? null, 
                'hdop' => $validatedData['hdop'] ?? null,             
            ]);

            Log::info('LocationAPI: Location data stored successfully for device: ' . $validatedData['device_id'], ['data' => $location->toArray()]);
            return response()->json(['message' => 'Location data received and stored successfully.', 'data' => $location], 201); // 201 Created

        } catch (\Illuminate\Database\QueryException $qe) {
            Log::error('LocationAPI: Database query error while storing location data. Error: ' . $qe->getMessage(), ['sql' => $qe->getSql(), 'bindings' => $qe->getBindings()]);
            return response()->json(['error' => 'Failed to store location data due to a database issue.', 'details' => $qe->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('LocationAPI: General error while storing location data. Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to store location data.', 'details' => $e->getMessage()], 500);
        }
    }

    public function getLatestLocation($deviceId)
    {
        Log::info("LocationAPI: Fetching latest location for device_id: {$deviceId}");
        try {
            $location = Location::where('device_id', $deviceId)
                                ->orderBy('created_at', 'desc')
                                ->first();

            $pythonDetectedImageUrl = null;
            $pythonDetectedObject = null;
            $pythonDetectedTimestamp = null;

            try {
                $latestDetectionFromPython = DB::table('deteksi_objek') 
                                            ->orderBy('waktu', 'desc')    
                                            ->first();

                if ($latestDetectionFromPython) {
                    if (!empty($latestDetectionFromPython->nama_file) && isset($latestDetectionFromPython->kategori)) {
                        $pythonDetectedImageUrl = route('serve.detected.image', ['filename' => $latestDetectionFromPython->nama_file]);
                        $pythonDetectedObject = $latestDetectionFromPython->kategori; 
                        $pythonDetectedTimestamp = $latestDetectionFromPython->waktu ? Carbon::parse($latestDetectionFromPython->waktu)->isoFormat('D MMMM YYYY, HH:mm:ss') : null;

                        Log::info("LocationAPI: Found latest Python detection from 'deteksi_objek': Filename - {$latestDetectionFromPython->nama_file}, Object - {$pythonDetectedObject}");
                    } else {
                        Log::warning("LocationAPI: Latest detection found in 'deteksi_objek', but 'nama_file' or 'kategori' is missing/empty.", (array) $latestDetectionFromPython);
                    }
                } else {
                    Log::info("LocationAPI: No recent Python detection found in 'deteksi_objek'.");
                }
            } catch (\Exception $e_db_python_img) {
                Log::error("LocationAPI: Error fetching latest Python detection from 'deteksi_objek': " . $e_db_python_img->getMessage());
            }


            if ($location) { 
                $responseData = [
                    'latitude'    => (float) $location->latitude,
                    'longitude'   => (float) $location->longitude,
                    'accuracy'    => $location->hdop ? round((float) $location->hdop * 5, 0) : 50, 
                    'timestamp'   => $location->created_at->toIso8601String(),
                    'device_id'   => $location->device_id,
                    'image_url'   => $pythonDetectedImageUrl, 
                    'objects'     => $pythonDetectedObject ? [$pythonDetectedObject] : [], 
                    'detection_timestamp' => $pythonDetectedTimestamp, 
                    'hdop'        => $location->hdop ? (float) $location->hdop : null,
                    'satellites'  => $location->satellites ? (int) $location->satellites : null,
                ];
                Log::info("LocationAPI: Returning combined location and latest detection data for device {$deviceId}.");
                return response()->json($responseData);
            } else { 
                Log::warning("LocationAPI: No location data found for device_id: {$deviceId}. Returning latest Python detection if available.");
                return response()->json([
                    'message'     => 'Belum ada data lokasi dari SmartCane untuk device ini.',
                    'latitude'    => null,
                    'longitude'   => null,
                    'accuracy'    => null,
                    'timestamp'   => now()->toIso8601String(), 
                    'device_id'   => $deviceId,
                    'image_url'   => $pythonDetectedImageUrl,
                    'objects'     => $pythonDetectedObject ? [$pythonDetectedObject] : [],
                    'detection_timestamp' => $pythonDetectedTimestamp,
                    'hdop'        => null,
                    'satellites'  => null,
                ], 200); 
            }
        } catch (\Exception $e) {
            Log::error("LocationAPI: General error in getLatestLocation for {$deviceId}: ".$e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            return response()->json(['error' => 'Gagal mengambil data lokasi.', 'details' => 'Server error, please check logs.'], 500);
        }
    }
}