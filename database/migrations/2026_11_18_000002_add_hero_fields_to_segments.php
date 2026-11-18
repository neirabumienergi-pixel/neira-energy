<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('segments', function (Blueprint $table) {
            $table->string('hero_title')->nullable()->after('content');
            $table->text('hero_text')->nullable()->after('hero_title');
            $table->string('hero_image')->nullable()->after('hero_text');
            $table->json('hero_points')->nullable()->after('hero_image');
            $table->string('hero_cta_label')->nullable()->after('hero_points');
            $table->string('hero_cta_url')->nullable()->after('hero_cta_label');
            $table->string('calc_profile')->default('business')->after('hero_cta_url'); // residential|business
            $table->boolean('show_in_hero')->default(true)->after('calc_profile');
        });
    }

    public function down(): void
    {
        Schema::table('segments', fn (Blueprint $t) => $t->dropColumn(['hero_title', 'hero_text', 'hero_image', 'hero_points', 'hero_cta_label', 'hero_cta_url', 'calc_profile', 'show_in_hero']));
    }
};
