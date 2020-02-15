<?php

use App\Participant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('draw_id');
            $table->longText('name');
            $table->longText('email_address');
            $table->enum('delivery_status', Participant::$deliveryStatuses);
            $table->unsignedInteger('target_id')->nullable();
            $table->timestamps();

            $table->foreign('draw_id')
                ->references('id')->on('draws')->onDelete('cascade');

            $table->foreign('target_id')
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
        Schema::dropIfExists('participants');
    }
}
