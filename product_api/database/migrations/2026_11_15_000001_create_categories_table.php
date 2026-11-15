<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('category_id');                 // bigint(20) UNSIGNED AUTO_INCREMENT PK
            $table->string('category_name', 50);                 // varchar(50) NOT NULL
            $table->string('description', 255)->nullable();      // varchar(255) NULL
            $table->string('image', 255);                        // varchar(255) NOT NULL
            $table->timestamp('created_at')->useCurrent();       // NOT NULL DEFAULT current_timestamp()
            $table->timestamp('updated_at')->nullable();         // NULL
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
