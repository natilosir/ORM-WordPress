<?php
// move-to-plugins.php

$sourceDir = __DIR__ . '/../orm-wordpress';
$destDir = __DIR__ . '/../../../wp-content/plugins/ORM-WordPress';
$vendorDir = __DIR__ . '/../../../vendor';

function clearDestination($destination) {
    if (!file_exists($destination)) {
        return true;
    }

    $files = array_diff(scandir($destination), array('.', '..'));
    foreach ($files as $file) {
        $path = $destination . '/' . $file;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            if (!unlink($path)) {
                echo "Warning: Could not delete file '$path'\n";
                return false;
            }
        }
    }
    return true;
}

function moveFiles($source, $destination) {
    $success = true;

    // Check if source directory exists
    if (!file_exists($source)) {
        die("Error: Source directory '$source' does not exist.\n");
    }

    // Create destination directory if it doesn't exist
    if (!file_exists($destination)) {
        if (!mkdir($destination, 0755, true)) {
            die("Error: Failed to create destination directory '$destination'.\n");
        }
        echo "Created directory: $destination\n";
    } else {
        // Clear destination first
        echo "Clearing destination directory...\n";
        if (!clearDestination($destination)) {
            die("Error: Failed to clear destination directory\n");
        }
    }

    // Open source directory
    $dir = opendir($source);
    if (!$dir) {
        die("Error: Could not open source directory '$source'.\n");
    }

    // Read directory contents
    while (false !== ($file = readdir($dir))) {
        // Skip these items
        if ($file == '.' || $file == '..' || $file == '.git' || $file == 'move-to-plugins.php') {
            continue;
        }

        $srcFile = $source . '/' . $file;
        $destFile = $destination . '/' . $file;

        // If it's a directory, process recursively
        if (is_dir($srcFile)) {
            if (!moveFiles($srcFile, $destFile)) {
                $success = false;
            }
        } else {
            // Move the file
            if (!rename($srcFile, $destFile)) {
                echo "Error: Failed to move '$srcFile' to '$destFile'\n";
                $success = false;
            } else {
                echo "Moved: $srcFile to $destFile\n";
            }
        }
    }

    closedir($dir);
    return $success;
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            if (!unlink($path)) {
                echo "Warning: Could not delete file '$path'\n";
                return false;
            }
        }
    }

    return rmdir($dir);
}

// Main execution
echo "Starting file transfer operation...\n";

// Move files first
$transferSuccess = moveFiles($sourceDir, $destDir);

if ($transferSuccess) {
    echo "File transfer completed successfully.\n";

    // Only delete vendor if file transfer was successful
    echo "Attempting to delete vendor directory at $vendorDir...\n";
    if (file_exists($vendorDir)) {
        if (!deleteDirectory($vendorDir)) {
            echo "Warning: Failed to completely remove vendor directory\n";
        } else {
            echo "Successfully deleted vendor directory\n";
        }
    } else {
        echo "Note: Vendor directory not found\n";
    }
} else {
    echo "File transfer completed with errors. Vendor directory was not deleted.\n";
}

echo "Operation finished.\n";
?>