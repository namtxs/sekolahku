<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('allow_comments')->default(false)->after('views');
        });

        // Berita yang sudah ada tetap bisa dikomentari; halaman statis tidak.
        DB::table('posts')->where('type', 'post')->update(['allow_comments' => true]);
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('allow_comments');
        });
    }
};
