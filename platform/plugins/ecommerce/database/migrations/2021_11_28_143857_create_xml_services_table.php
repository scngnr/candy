<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXmlServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xml_services', function (Blueprint $table) {
            $table->id();
            $table->string('xmlAdi');
            $table->string('hata')->nullable();
            $table->string('xmlLinki');
            $table->string('xmlDurum')->default('aktif değil');
            $table->string('xmlUrunAdet')->nullable();

            $table->json('urunBilgileri');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xml_services');
    }
}
