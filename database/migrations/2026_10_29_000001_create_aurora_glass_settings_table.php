<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('aurora_glass_settings')) {
            Schema::create('aurora_glass_settings', function (Blueprint $table): void {
                $table->unsignedTinyInteger('id')->primary();
                $table->text('settings');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('aurora_glass_settings');
    }
};
