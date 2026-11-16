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
        Schema::create('chat_percakapan', function (Blueprint $table) {
            $table->id('id_percakapan');
            $table->integer('id_pelanggan')->nullable();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('nama_pengirim', 100);
            $table->string('email_pengirim', 100)->nullable();
            $table->string('telepon_pengirim', 20)->nullable();
            $table->integer('id_helm_terkait')->nullable();
            $table->text('pesan_terakhir')->nullable();
            $table->timestamp('pesan_terakhir_at')->nullable();
            $table->integer('unread_karyawan')->default(0);
            $table->integer('unread_pelanggan')->default(0);
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan')->onDelete('cascade');
            $table->foreign('id_helm_terkait')->references('id_helm')->on('helm')->onDelete('set null');
        });

        Schema::create('chat_pesan', function (Blueprint $table) {
            $table->id('id_pesan');
            $table->unsignedBigInteger('id_percakapan');
            $table->enum('pengirim_tipe', ['pelanggan', 'karyawan', 'sistem'])->default('pelanggan');
            $table->integer('id_user')->nullable();
            $table->text('pesan');
            $table->integer('id_helm')->nullable();
            $table->boolean('dibaca')->default(false);
            $table->timestamps();

            $table->foreign('id_percakapan')->references('id_percakapan')->on('chat_percakapan')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('set null');
            $table->foreign('id_helm')->references('id_helm')->on('helm')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_pesan');
        Schema::dropIfExists('chat_percakapan');
    }
};
