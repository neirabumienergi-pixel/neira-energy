<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('video')->nullable()->after('image_mobile');
            $table->string('video_url')->nullable()->after('video');
        });
        Schema::table('segments', function (Blueprint $table) {
            $table->string('hero_video')->nullable()->after('hero_image');
        });
    }

    public function down(): void
    {
        Schema::table('banners', fn (Blueprint $t) => $t->dropColumn(['video', 'video_url']));
        Schema::table('segments', fn (Blueprint $t) => $t->dropColumn('hero_video'));
    }
};
