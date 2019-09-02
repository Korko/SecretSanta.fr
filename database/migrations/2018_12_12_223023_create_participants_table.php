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
            $table->mediumText('name');
            $table->mediumText('email_address')->nullable();
            $table->string('email_id')->nullable();
            $table->enum('delivery_status', Participant::$deliveryStatuses);
            $table->mediumText('target');
            $table->timestamps();

            $table->foreign('draw_id')
                ->references('id')->on('draws')->onDelete('cascade');
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