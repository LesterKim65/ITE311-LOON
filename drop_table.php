<?php

require 'vendor/autoload.php';

$db = \Config\Database::connect();

try {
    $db->query("DROP TABLE IF EXISTS enrollments;");
    echo "Table dropped successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
