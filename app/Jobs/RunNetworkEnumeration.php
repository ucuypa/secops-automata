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
        $this->scanJob->update(['status' => 'RUNNING']);

        $targetDomain = $this->scanJob->target->domain;

        // Execute the Nmap command natively
        $result = \Illuminate\Support\Facades\Process::timeout(300)->run("nmap -F {$targetDomain}");

        if ($result->successful()) {
            $output = $result->output();

            // Log the output for debugging
            info("Scan completed for {$targetDomain}:\n" . $output);

            // Parse the Nmap output using Regex
            // This looks for lines matching: "80/tcp open http"
            $pattern = '/^(\d+)\/(tcp|udp)\s+open\s+(.+)$/mi';

            if (preg_match_all($pattern, $output, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $port = $match[1];
                    $protocol = $match[2];
                    $service = trim($match[3]);

                    \App\Models\Vulnerability::create([
                        'target_id' => $this->scanJob->target_id,
                        'scan_job_id' => $this->scanJob->id,
                        'title' => "Open Port: {$port}/{$protocol} ({$service})",
                        'description' => "Automated scan discovered an open {$protocol} port running the {$service} service.",
                        'severity' => 'INFO',
                        'status' => 'OPEN',
                    ]);
                }
            }

            $this->scanJob->update(['status' => 'DONE']);
        } else {
            info("Scan failed:\n" . $result->errorOutput());
            $this->scanJob->update(['status' => 'FAILED']);
        }
    }
}