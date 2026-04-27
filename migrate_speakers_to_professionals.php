<?php

/**
 * Migration script to move speakers to professionals
 * Run this script from the root of your Statamic project
 */

$speakersDir = 'content/collections/speakers/en/';
$professionalsDir = 'content/collections/professionals/en/';

if (!is_dir($speakersDir)) {
    echo "Speakers directory not found: $speakersDir\n";
    exit(1);
}

if (!is_dir($professionalsDir)) {
    mkdir($professionalsDir, 0755, true);
    echo "Created professionals directory: $professionalsDir\n";
}

$speakerFiles = glob($speakersDir . '*.md');
$migratedCount = 0;

echo "Found " . count($speakerFiles) . " speaker files to migrate\n";

foreach ($speakerFiles as $speakerFile) {
    $filename = basename($speakerFile);
    $professionalsFile = $professionalsDir . $filename;
    
    // Read the original file
    $content = file_get_contents($speakerFile);
    
    if ($content === false) {
        echo "Error reading file: $speakerFile\n";
        continue;
    }
    
    // Update the blueprint from 'speaker' to 'professional'
    $updatedContent = preg_replace('/^blueprint:\s*speaker$/m', 'blueprint: professional', $content);
    
    // Write to professionals directory
    if (file_put_contents($professionalsFile, $updatedContent) !== false) {
        echo "Migrated: $filename\n";
        $migratedCount++;
    } else {
        echo "Error writing file: $professionalsFile\n";
    }
}

echo "\nMigration completed!\n";
echo "Migrated $migratedCount files to professionals collection.\n";
echo "\nNext steps:\n";
echo "1. Review the migrated files in: $professionalsDir\n";
echo "2. Update any template references from 'speakers' to 'professionals'\n";
echo "3. Consider removing the speakers collection if no longer needed\n";
echo "4. Update any navigation or menu references\n";

?>
