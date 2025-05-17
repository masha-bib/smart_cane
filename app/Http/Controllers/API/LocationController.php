namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all(['id', 'name', 'latitude', 'longitude', 'description']);
        return response()->json($locations);
    }

    // (Opsional) Jika ingin menambahkan lokasi via API dari map click
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
        ]);

        $location = Location::create($validated);
        return response()->json($location, 201);
    }
}