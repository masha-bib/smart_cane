<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon; 

class SmartCaneDataController extends Controller
{
    public function getLatestSmartCaneData()
    {
        try {
            $latestDbRecord = DB::table('detections')
                                ->orderBy('timestamp', 'desc') 
                                ->first();

            if (!$latestDbRecord) {
                return response()->json([
                    'message'     => 'Belum ada data dari SmartCane.',
                    'latitude'    => null,
                    'longitude'   => null,
                    'accuracy'    => null,
                    'objects'     => [],
                    'voice_alert' => null,
                    'timestamp'   => Carbon::now()->toIso8601String(),
                    'image_url'   => null
                ], 200); 
            }

            $objectsArray = json_decode($latestDbRecord->detected_objects_json, true); 

            $imageUrl = null;

            $responseData = [
                'latitude'    => (float) $latestDbRecord->latitude,
                'longitude'   => (float) $latestDbRecord->longitude,
                'accuracy'    => (float) $latestDbRecord->accuracy,
                'objects'     => $objectsArray ?: [], 
                'voice_alert' => $latestDbRecord->voice_alert_text,
                'timestamp'   => Carbon::parse($latestDbRecord->timestamp)->toIso8601String(), 
                'image_url'   => $imageUrl
            ];

            return response()->json($responseData);

        } catch (\Exception $e) {
            \Log::error("Error fetching SmartCane data: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            return response()->json(['error' => 'Gagal mengambil data SmartCane.', 'details' => $e->getMessage()], 500);
        }
    }
}