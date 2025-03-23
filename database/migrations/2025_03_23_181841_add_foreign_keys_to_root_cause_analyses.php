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
        Schema::table('root_cause_analyses', function (Blueprint $table) {
            // Tambahkan foreign key untuk analyzed_by jika kolom tersebut ada
            if (Schema::hasColumn('root_cause_analyses', 'analyzed_by')) {
                // Cek jika foreign key belum ada
                $foreignKeys = $this->listForeignKeys('root_cause_analyses');
                if (!in_array('root_cause_analyses_analyzed_by_foreign', $foreignKeys)) {
                    $table->foreign('analyzed_by')->references('id')->on('users');
                }
            }

            // Tambahkan foreign key untuk updated_by jika kolom tersebut ada
            if (Schema::hasColumn('root_cause_analyses', 'updated_by')) {
                // Cek jika foreign key belum ada
                $foreignKeys = $this->listForeignKeys('root_cause_analyses');
                if (!in_array('root_cause_analyses_updated_by_foreign', $foreignKeys)) {
                    $table->foreign('updated_by')->references('id')->on('users');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('root_cause_analyses', function (Blueprint $table) {
            // Cek jika foreign key ada
            $foreignKeys = $this->listForeignKeys('root_cause_analyses');

            // Drop foreign key untuk analyzed_by
            if (in_array('root_cause_analyses_analyzed_by_foreign', $foreignKeys)) {
                $table->dropForeign('root_cause_analyses_analyzed_by_foreign');
            }

            // Drop foreign key untuk updated_by
            if (in_array('root_cause_analyses_updated_by_foreign', $foreignKeys)) {
                $table->dropForeign('root_cause_analyses_updated_by_foreign');
            }
        });
    }

    /**
     * Mendapatkan daftar foreign keys pada tabel
     */
    private function listForeignKeys($tableName)
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();

        $foreignKeys = [];
        $constraints = $conn->select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND TABLE_SCHEMA = '$dbName'
            AND TABLE_NAME = '$tableName'
        ");

        foreach ($constraints as $constraint) {
            $foreignKeys[] = $constraint->CONSTRAINT_NAME;
        }

        return $foreignKeys;
    }
};
