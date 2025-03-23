<?php

$migrationDir = __DIR__ . '/database/migrations/';
$files = glob($migrationDir . '2025_03_19_*.php');

foreach ($files as $file) {
    echo "Processing: " . basename($file) . PHP_EOL;
    $content = file_get_contents($file);
    
    // Replace foreignUuid references to users with unsignedBigInteger
    $content = preg_replace(
        '/\$table->foreignUuid\(\'(created_by|updated_by|lead_assessor|assessed_by|verified_by|responsible_person|head_of_department)\'\)->nullable\(\)->references\(\'id\'\)->on\(\'users\'\);/',
        '$table->unsignedBigInteger(\'$1\')->nullable();' . PHP_EOL . 
        '            $table->foreign(\'$1\')->references(\'id\')->on(\'users\');',
        $content
    );
    
    // Replace other foreignUuid references with separate uuid and foreign key statements
    $content = preg_replace(
        '/\$table->foreignUuid\(\'([^\']+)\'\)->references\(\'id\'\)->on\(\'([^\']+)\'\);/',
        '$table->uuid(\'$1\');' . PHP_EOL . 
        '            $table->foreign(\'$1\')->references(\'id\')->on(\'$2\');',
        $content
    );
    
    file_put_contents($file, $content);
}

echo "All migrations fixed!";
