<?php

use App\Participant;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('draw_id')->unsigned();
            $table->string('name');
            $table->string('email_address');
            $table->string('email_id')->nullable();
            $table->enum('delivery_status', Participant::$deliveryStatuses);
            $table->string('dear_santa_link')->nullable();
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
