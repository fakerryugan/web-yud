<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignaturesTable extends Migration
{
   public function up()
{
    Schema::create('signatures', function (Blueprint $table) {
        $table->id();
        $table->foreignId('document_id')->constrained()->onDelete('cascade');
        $table->foreignId('signer_id'); // Sesuaikan jika nama kolom Anda user_id
        $table->string('sign_token')->unique();
        $table->string('status')->default('pending');

        // --- TAMBAHKAN DUA BARIS INI ---
        $table->timestamp('signed_at')->nullable(); // Wajib untuk Approve
        $table->text('comment')->nullable();        // Wajib untuk Reject
        // -------------------------------

        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('signatures');
    }
}
