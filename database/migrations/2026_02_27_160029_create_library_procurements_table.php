<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('library_procurements', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 255);
            $table->string('vendor', 255)->nullable()->default(null);
            $table->decimal('total_amount', 15, 2)->default('0.00');
            $table->dateTime('procurement_date');
            $table->unsignedBigInteger('staff_id')->nullable()->default(null);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('set null');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('library_procurements'); } 
};
