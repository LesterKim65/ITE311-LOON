<?php

$servername = "localhost";

$username = "root";

$password = "";

$dbname = "lms_loon";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {

  die("Connection failed: " . $conn->connect_error);

}

$sql = "INSERT INTO migrations (`version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES ('2025-10-09-181050', 'CreateEnrollmentsTable', 'default', 'App', UNIX_TIMESTAMP(), 2);";

$conn->query($sql);

$sql = "INSERT INTO migrations (`version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES ('2025-10-22-154738', 'CreateMaterialsTable', 'default', 'App', UNIX_TIMESTAMP(), 2);";

$conn->query($sql);

$sql = "INSERT INTO migrations (`version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES ('2025-10-22-172751', 'AddInstructorIdToMaterialsTable', 'default', 'App', UNIX_TIMESTAMP(), 2);";

$conn->query($sql);

$sql = "INSERT INTO migrations (`version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES ('2025-10-24-150930', 'AlterMaterialsTableFilePath', 'default', 'App', UNIX_TIMESTAMP(), 2);";

$conn->query($sql);

$conn->close();

echo "Done";

?>
