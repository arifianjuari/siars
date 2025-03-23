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
            $table->dateTime('handling_date')->nullable()->after('handling_actions');
            $table->text('handling_result')->nullable()->after('handling_date');
            $table->string('handling_document')->nullable()->after('handling_result');
            $table->text('follow_up_plan')->nullable()->after('handling_document');
            $table->unsignedBigInteger('handler_id')->nullable()->after('follow_up_plan');
            $table->dateTime('completed_at')->nullable()->after('handler_id');

            // Tambahkan foreign key untuk handler_id jika diperlukan
            $table->foreign('handler_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['handler_id']);
            $table->dropColumn([
                'handling_actions',
                'handling_date',
                'handling_result',
                'handling_document',
                'follow_up_plan',
                'handler_id',
                'completed_at'
            ]);
        });
    }
};
