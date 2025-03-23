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
        Schema::table('incidents', function (Blueprint $table) {
            $table->text('handling_actions')->nullable()->after('status');
            $table->date('handling_date')->nullable()->after('handling_actions');
            $table->text('handling_result')->nullable()->after('handling_date');
            $table->text('follow_up_plan')->nullable()->after('handling_result');
            $table->foreignId('handler_id')->nullable()->constrained('users')->after('follow_up_plan');
            $table->timestamp('completed_at')->nullable()->after('handler_id');
            $table->string('handling_document')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn([
                'handling_actions',
                'handling_date',
                'handling_result',
                'follow_up_plan',
                'handler_id',
                'completed_at',
                'handling_document'
            ]);
        });
    }
};
