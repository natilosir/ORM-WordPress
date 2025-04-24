<?php

// مسیر پوشه ORM-WordPress
$sourceDir = __DIR__ . '/ORM-WordPress';

// مسیر پوشه پلاگین وردپرس
$pluginDir = getenv('WP_PLUGIN_DIR') ? : __DIR__ . '/wp-content/plugins';

// مسیر مقصد پوشه ORM-WordPress در پوشه پلاگین
$destinationDir = $pluginDir . '/ORM-WordPress';

// اطمینان از وجود پوشه مقصد
if ( !is_dir($pluginDir) ) {
    mkdir($pluginDir, 0755, true);
}

// تابعی برای انتقال پوشه
function recursiveCopy( $source, $destination ) {
    if ( !is_dir($destination) ) {
        mkdir($destination, 0755, true);
    }
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ( $iterator as $item ) {
        $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
        if ( $item->isDir() ) {
            mkdir($target, 0755, true);
        } else {
            copy($item, $target);
        }
    }
}

// انتقال پوشه ORM-WordPress به پوشه پلاگین
if ( is_dir($sourceDir) ) {
    echo "Moving ORM-WordPress to plugins directory...\n";
    recursiveCopy($sourceDir, $destinationDir);
    echo "Move completed.\n";
} else {
    echo "Source directory not found: $sourceDir\n";
}