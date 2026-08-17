<?php

namespace App\Jobs;

use App\Models\ScanJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;

class RunNetworkEnumeration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $scanJob;

    public function __construct(ScanJob $scanJob)
    {
        $this->scanJob = $scanJob;
    }

    public function handle(): void
    {
        // Mark the job as running in the database
        $this->scanJob->update(['status' => 'RUNNING']);

        // Extract the target's IP/Domain
        $targetDomain = $this->scanJob->target->domain;

        // Execute the Nmap command at the OS level
        $result = Process::run("nmap -F {$targetDomain}");

        // Handle the results
        if ($result->successful()) {
            info("Scan completed for {$targetDomain}:\n" . $result->output());
            
            $this->scanJob->update(['status' => 'DONE']);
        } else {
            // Catch errors if Nmap crashes or isn't installed
            info("Scan failed:\n" . $result->errorOutput());
            
            $this->scanJob->update(['status' => 'FAILED']);
        }
    }
}