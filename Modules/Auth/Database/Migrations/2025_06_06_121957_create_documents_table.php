<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    public function up()
    {

{
    Schema::create('documents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('file_path');
        $table->text('encrypted_original_filename');
        $table->string('tujuan')->nullable();
        $table->uuid('access_token')->unique();

        // --- TAMBAHKAN DUA BARIS INI ---
        $table->string('status')->default('pending'); 
        $table->timestamp('verified_at')->nullable();
        // -------------------------------

        $table->timestamps();
        $table->softDeletes();
    });
}
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
