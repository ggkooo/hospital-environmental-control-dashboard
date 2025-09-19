<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('noise_minutes', function (Blueprint $table) {
            $table->id();
            $table->float('average_value');
            $table->timestamp('minute');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noise_minutes');
    }
};
