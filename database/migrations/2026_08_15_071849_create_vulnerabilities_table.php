<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vulnerabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scan_job_id')->constrained()->cascadeOnDelete();
            $table->string('cve_id')->nullable()->index();
            $table->string('title');
            $table->enum('severity', ['INFO', 'LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('INFO')->index();
            $table->decimal('cvss_score', 3, 1)->nullable();
            $table->text('description')->nullable();
            $table->text('remediation')->nullable();
            $table->string('status')->default('OPEN')->index();
            $table->jsonb('evidence')->nullable();
            $table->timestamps();
            
            $table->index(['target_id', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vulnerabilities');
    }
};