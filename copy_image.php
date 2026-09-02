<?php
$source = __DIR__ . '/Flights/images/aeroplane.jpg';

$destinations = [
    __DIR__ . '/aeroplane.png',
    __DIR__ . '/aeroplane.jpg',
    __DIR__ . '/images/aeroplane.png',
    __DIR__ . '/images/aeroplane.jpg',
    __DIR__ . '/Flights/aeroplane.png',
    __DIR__ . '/Flights/aeroplane.jpg',
    __DIR__ . '/Flights/images/aeroplane.png',
];

if (!file_exists(__DIR__ . '/images')) {
    mkdir(__DIR__ . '/images', 0777, true);
}

if (file_exists($source)) {
    foreach ($destinations as $dest) {
        $dir = dirname($dest);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        copy($source, $dest);
    }
    echo "Copied successfully!";
} else {
    echo "Source file not found: " . $source;
}
