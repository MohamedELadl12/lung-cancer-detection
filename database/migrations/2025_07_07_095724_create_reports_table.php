<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->tinyInteger('age');
            $table->enum('gender',['male','female']);
            $table->boolean('smoker')->default(false);
            $table->boolean('yellow_fingers')->default(false);
            $table->boolean('anxiety')->default(false);
            $table->boolean('peer_pressure')->default(false);
            $table->boolean('chronic_disease')->default(false);
            $table->boolean('fatigue')->default(false);
            $table->boolean('allergy')->default(false);
            $table->boolean('wheezing')->default(false);
            $table->boolean('alcohol_consuming')->default(false);
            $table->boolean('coughing')->default(false);
            $table->boolean('shortness_of_breath')->default(false);
            $table->boolean('swallowing_difficulty')->default(false);
            $table->boolean('chest_pain')->default(false);
            $table->float('probability')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
