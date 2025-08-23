<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transformation_results', function (Blueprint $table) {
            $table->timestamp('successfully_completed_at')->nullable()->after('latest_exception_trace');
        });
    }

    public function down()
    {
        Schema::table('transformation_results', function (Blueprint $table) {
            $table->dropColumn('successfully_completed_at');
        });
    }
};
