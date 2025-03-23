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
        // Cek apakah foreign key sudah ada
        $foreignKeys = $this->getForeignKeys('snars_chapters');
        $hasForeignKey = in_array('snars_chapters_snars_group_id_foreign', $foreignKeys);
        
        // Cek apakah indeks sudah ada
        $indexes = $this->getIndexes('snars_chapters');
        $hasIndex = in_array('snars_chapters_snars_group_id_index', $indexes);
        $hasUniqueIndex = in_array('snars_chapters_snars_group_id_code_unique', $indexes);
        
        Schema::table('snars_chapters', function (Blueprint $table) use ($hasForeignKey, $hasIndex, $hasUniqueIndex) {
            // Tambahkan foreign key constraint jika belum ada
            if (!$hasForeignKey) {
                $table->foreign('snars_group_id')
                      ->references('id')
                      ->on('snars_groups')
                      ->onDelete('cascade');
            }
            
            // Tambahkan indeks jika belum ada
            if (!$hasIndex) {
                $table->index('snars_group_id');
            }
            
            // Tambahkan unique constraint jika belum ada
            if (!$hasUniqueIndex) {
                $table->unique(['snars_group_id', 'code']);
            }
        });
    }
    
    /**
     * Get all foreign keys for a table
     */
    private function getForeignKeys(string $table): array
    {
        $conn = Schema::getConnection();
        $foreignKeys = [];
        
        $results = $conn->select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' 
            AND TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ?",
            [$table]
        );
        
        foreach ($results as $result) {
            $foreignKeys[] = $result->CONSTRAINT_NAME;
        }
        
        return $foreignKeys;
    }
    
    /**
     * Get all indexes for a table
     */
    private function getIndexes(string $table): array
    {
        $conn = Schema::getConnection();
        $indexes = [];
        
        $results = $conn->select(
            "SHOW INDEXES FROM {$table}"
        );
        
        foreach ($results as $result) {
            $indexes[] = $result->Key_name;
        }
        
        return $indexes;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cek apakah foreign key sudah ada
        $foreignKeys = $this->getForeignKeys('snars_chapters');
        $hasForeignKey = in_array('snars_chapters_snars_group_id_foreign', $foreignKeys);
        
        // Cek apakah indeks sudah ada
        $indexes = $this->getIndexes('snars_chapters');
        $hasIndex = in_array('snars_chapters_snars_group_id_index', $indexes);
        $hasUniqueIndex = in_array('snars_chapters_snars_group_id_code_unique', $indexes);
        
        Schema::table('snars_chapters', function (Blueprint $table) use ($hasForeignKey, $hasIndex, $hasUniqueIndex) {
            // Hapus foreign key constraint jika ada
            if ($hasForeignKey) {
                $table->dropForeign(['snars_group_id']);
            }
            
            // Hapus indeks jika ada
            if ($hasIndex) {
                $table->dropIndex(['snars_group_id']);
            }
            
            // Hapus unique constraint jika ada
            if ($hasUniqueIndex) {
                $table->dropUnique(['snars_group_id', 'code']);
            }
        });
    }
};
