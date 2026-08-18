<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class ScanController
{
public function launch()
{
    $result = Process::run('python3 /path/to/your/secops_scanner.py');

    if ($result->successful()) {
        return response()->json([
            'status' => 'success',
            'message' => 'Scan complete! Vulnerabilities updated.',
            'output' => $result->output()
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Scan failed to execute.'
        ], 500);
    }
}
}