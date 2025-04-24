<?php
// move-to-plugins.php

$sourceDir = '../orm-wordpress';
$destDir = '../../../wp-content/plugins/ORM-WordPress';
$vendorDir = '../../../vendor'; // Vendor directory to delete

function moveFiles($source, $destination) {
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
            moveFiles($srcFile, $destFile);
        } else {
            // Move the file
            if (!rename($srcFile, $destFile)) {
                echo "Warning: Failed to move '$srcFile' to '$destFile'\n";
            } else {
                echo "Moved: $srcFile to $destFile\n";
            }
        }
    }

    closedir($dir);
}

function deleteVendorFolder($vendorPath) {
    if (file_exists($vendorPath)) {
        if (!deleteDirectory($vendorPath)) {
            echo "Warning: Failed to completely remove vendor directory\n";
        } else {
            echo "Successfully deleted vendor directory at $vendorPath\n";
        }
    } else {
        echo "Note: Vendor directory not found at $vendorPath\n";
    }
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
moveFiles($sourceDir, $destDir);
echo "File transfer completed.\n";

echo "Cleaning up vendor directory at $vendorDir...\n";
deleteVendorFolder($vendorDir);
echo "Cleanup completed.\n";

echo "Operation finished successfully!\n";
?>