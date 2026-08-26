<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class ScanController
{
    public function launch()
    {
        $scriptPath = base_path('script/secops_scanner.py');
        
        $result = Process::run("python3 {$scriptPath}");

        if ($result->successful()) {
            $scanData = json_decode($result->output(), true);

            if ($scanData === null) {
                $scanData = ['raw_text' => $result->output(), 'json_error' => json_last_error_msg()];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Infrastructure scan executed successfully via Python/Nmap!',
                'findings' => $scanData 
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Scan execution failed.',
                'error' => $result->errorOutput()
            ], 500);
        }
    }
}