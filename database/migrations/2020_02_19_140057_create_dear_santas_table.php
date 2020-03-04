<?php

use App\DearSanta;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDearSantasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dear_santas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sender_id');
            $table->longText('email_body');
            $table->enum('delivery_status', DearSanta::$deliveryStatuses);
            $table->timestamps();

            $table->foreign('sender_id')
                ->references('id')->on('participants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dear_santas');
    }
}
