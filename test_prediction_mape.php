<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Http\Controllers\Admin\PrediksiController;
use App\Models\Kelompok;
use Illuminate\Http\Request;

// Simulate the controller logic
$controller = new PrediksiController();

// Get a group
$kelompok = Kelompok::first();
if (!$kelompok) {
    echo "No group found.\n";
    exit;
}

echo "Testing prediction for group: " . $kelompok->nama_kelompok . "\n";

// Create a mock request
$request = new Request([
    'kelompok_id' => $kelompok->id,
    'jenis_kegiatan' => 'all'
]);

// Call the method (we need to make it accessible or simulate the logic)
// Since we can't easily call the protected methods or the full controller flow without auth, 
// let's just use reflection to call getHistoricalData and calculateTripleExponentialSmoothing

$reflection = new ReflectionClass($controller);
$getHistoricalData = $reflection->getMethod('getHistoricalData');
$getHistoricalData->setAccessible(true);

$calculateTES = $reflection->getMethod('calculateTripleExponentialSmoothing');
$calculateTES->setAccessible(true);

$calculateMAPE = $reflection->getMethod('calculateMAPE');
$calculateMAPE->setAccessible(true);

$jenisKegiatanList = [
    'Perbaikan Meteran',
    'Perbaikan Sambungan Rumah',
    'Pemeriksaan Gardu',
    'Jenis Kegiatan lainnya'
];

foreach ($jenisKegiatanList as $jenis) {
    echo "\nTesting: $jenis\n";
    
    $data = $getHistoricalData->invoke($controller, $kelompok->id, $jenis);
    echo "Data points found: " . count($data) . "\n";
    print_r($data);
    
    if (count($data) >= 1) {
        try {
            $prediction = $calculateTES->invoke($controller, $data);
            $mape = $calculateMAPE->invoke($controller, $data, $prediction['forecasts']);
            
            echo "Prediction: " . $prediction['nextForecast'] . "\n";
            echo "MAPE: " . $mape . "%\n";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Not enough data.\n";
    }
}
