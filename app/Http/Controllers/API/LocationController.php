<?php
namespace App\Http\Controllers\Api; // Namespace sudah benar

use App\Http\Controllers\Controller;
use App\Models\Location; // Pastikan model diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;     // TAMBAHKAN INI
use Illuminate\Support\Facades\Log;      // Untuk logging
use Illuminate\Support\Facades\Storage;  // TAMBAHKAN INI
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;              // TAMBAHKAN INI

class LocationController extends Controller
{
    // Metode index() dan store() Anda yang sudah ada
    public function index()
    {
        // Jika Anda masih menggunakan 'name' dan 'description' dari tabel lama, sertakan di sini
        $locations = Location::orderBy('created_at', 'desc')->take(100)->get(['id', /*'name',*/ 'device_id', 'latitude', 'longitude', /*'description',*/ 'image_url']);
        return response()->json($locations);
    }

    public function store(Request $request) // Untuk input manual jika masih dipakai
    {
        $validated = $request->validate([
            // Sesuaikan field ini dengan $fillable di Model Location Anda jika store() masih dipakai
            // 'name' => 'required|string|max:255',
            'device_id' => 'required|string|max:50', // Tambahkan device_id jika store() juga untuk IoT
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            // 'description' => 'nullable|string',
        ]);
        // Jika Anda ingin mengambil gambar juga saat store manual, panggil _captureAndSaveImage()
        $location = Location::create($validated);
        return response()->json($location, 201);
    }

    /**
     * Menerima data dari SmartCane (ESP32 GPS), mengambil gambar dari ESP32-CAM,
     * dan menyimpannya. INI YANG AKAN DIPANGGIL OLEH ESP32 (GPS) ANDA.
     */
    public function updateLocation(Request $request) // Nama method tetap sama
    {
        Log::info('SmartCane updateLocation request received: ', $request->all());

        // Validasi data dari ESP32 (GPS)
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:50',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'satellites' => 'nullable|integer|min:0',
            'hdop' => 'nullable|numeric|min:0',
            'event_detected' => 'nullable|string|max:255', // Opsional, jika ESP32 mengirim
            'voice_alert' => 'nullable|string|max:255',  // Opsional, jika ESP32 mengirim
        ]);

        if ($validator->fails()) {
            Log::error('SmartCane validation failed: ', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();
        $deviceId = $validatedData['device_id'];

        // Panggil helper untuk mengambil dan menyimpan gambar
        $imageData = $this->_captureAndSaveImage($deviceId);

        try {
            // Buat record baru di tabel locations
            $location = Location::create([
                'device_id'   => $deviceId,
                'latitude'    => $validatedData['latitude'],
                'longitude'   => $validatedData['longitude'],
                'satellites'  => $validatedData['satellites'] ?? null,
                'hdop'        => $validatedData['hdop'] ?? null,
                'image_url'   => $imageData['url'] ?? null,    // Simpan URL gambar
                'image_path'  => $imageData['path'] ?? null,   // Simpan path gambar
                'event_detected' => $validatedData['event_detected'] ?? null, // Simpan jika ada
                'voice_alert'  => $validatedData['voice_alert'] ?? null,    // Simpan jika ada
                // 'name' dan 'description' tidak diisi dari sini, karena ini dari IoT device
            ]);

            Log::info("SmartCane location data (ID: {$location->id}) and image (if any) saved for device {$deviceId}");
            return response()->json([
                'message' => 'Location and image (if any) updated successfully',
                'data' => $location
            ], 201);
        } catch (\Exception $e) {
            Log::error("SmartCane failed to save location for device {$deviceId}: ".$e->getMessage(), ['exception_trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to save location data', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper method (private) untuk mengambil gambar dari ESP32-CAM dan menyimpannya.
     * Mengembalikan array ['url' => ..., 'path' => ...] atau null jika gagal.
     */
    private function _captureAndSaveImage(string $deviceId): ?array
    {
        $esp32CamIp = env('ESP32_CAM_IP');
        $esp32CamEndpoint = env('ESP32_CAM_CAPTURE_ENDPOINT', '/capture');

        if (!$esp32CamIp) {
            Log::warning("ESP32_CAM_IP not configured. Skipping image capture for device {$deviceId}.");
            return null;
        }

        $captureUrl = "http://{$esp32CamIp}{$esp32CamEndpoint}";
        Log::info("Attempting to capture image for device {$deviceId} from: {$captureUrl}");

        try {
            // Timeout disesuaikan (misal 3-4 detik jika update GPS setiap 5 detik)
            $response = Http::timeout(4)->retry(1, 100)->get($captureUrl);

            if ($response->successful()) {
                $imageContents = $response->body();
                $imageFileName = 'captures/' . $deviceId . '_' . time() . '_' . Str::random(6) . '.jpg';
                
                if (Storage::disk('public')->put($imageFileName, $imageContents)) {
                    Log::info("Image captured and saved for device {$deviceId} to: {$imageFileName}");
                    return [
                        'url' => Storage::url($imageFileName),
                        'path' => $imageFileName,
                    ];
                } else {
                    Log::error("Failed to save image to storage for device {$deviceId}. Check permissions for storage/app/public/captures.");
                }
            } else {
                Log::error("Failed to capture image from ESP32-CAM for device {$deviceId}. HTTP Status: " . $response->status() . " - Response: " . substr($response->body(), 0, 200));
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Connection to ESP32-CAM (device {$deviceId}) failed: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Generic error during image capture for device {$deviceId}: " . $e->getMessage());
        }
        return null;
    }


    /**
     * Mengambil data lokasi terbaru untuk device tertentu untuk ditampilkan di Leaflet.
     */
    public function getLatestLocation($deviceId)
    {
        Log::info("API Request: Fetching latest location for device_id: {$deviceId}");
        try {
            $location = Location::where('device_id', $deviceId)
                                ->orderBy('created_at', 'desc')
                                ->first();

            if ($location) {
                $responseData = [
                    'latitude'    => (float) $location->latitude,
                    'longitude'   => (float) $location->longitude,
                    'accuracy'    => $location->hdop ? round((float) $location->hdop * 5, 0) : 50,
                    'hdop'        => $location->hdop ? (float) $location->hdop : null,
                    'satellites'  => $location->satellites ? (int) $location->satellites : null,
                    'timestamp'   => $location->created_at->toIso8601String(),
                    'device_id'   => $location->device_id,
                    'image_url'   => $location->image_url, // DIAMBIL DARI DATABASE
                    'event_detected' => $location->event_detected, // Jika sudah ada di tabel
                    'voice_alert' => $location->voice_alert,   // Jika sudah ada di tabel
                    'objects'     => [], // Default, bisa diisi dari field lain jika ada
                ];
                return response()->json($responseData);
            } else {
                Log::warning("API Request: No location data found for device_id: {$deviceId}");
                return response()->json([
                    'message'     => 'Belum ada data dari SmartCane untuk device ini.',
                    'latitude'    => null, 'longitude'   => null, 'accuracy'    => null,
                    'hdop'        => null, 'satellites'  => null, 'image_url'   => null,
                    'event_detected'=> null, 'voice_alert' => null, 'objects'     => [],
                    'timestamp'   => now()->toIso8601String(), 'device_id'   => $deviceId
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error("API Request: Error fetching latest location for {$deviceId}: ".$e->getMessage() . ' - Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Gagal mengambil data lokasi.', 'details' => $e->getMessage()], 500);
        }
    }
}