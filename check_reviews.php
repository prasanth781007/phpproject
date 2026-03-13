<?php
require_once 'c:/xampp/htdocs/1magical/includes/config.php';

echo "Checking Reviews Table:\n";
$sql = "SELECT * FROM reviews";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    if ($row['photo']) {
        echo "Review ID " . $row['id'] . " Photo: " . $row['photo'] . "\n";
        if (file_exists('c:/xampp/htdocs/1magical/' . $row['photo'])) {
            echo "  - File EXISTS\n";
        } else {
            echo "  - File DOES NOT EXIST (Checking scripts/uploads/reviews/...)\n";
            $legacy_path = 'c:/xampp/htdocs/1magical/scripts/' . $row['photo'];
            if (file_exists($legacy_path)) {
                echo "  - File FOUND in scripts folder. Fixing...\n";
                $dest = 'c:/xampp/htdocs/1magical/' . $row['photo'];
                $dir = dirname($dest);
                if (!is_dir($dir))
                    mkdir($dir, 0777, true);
                if (rename($legacy_path, $dest)) {
                    echo "  - SUCCESSFULLY MOVED to root uploads.\n";
                }
            } else {
                echo "  - File NOT FOUND in legacy location either.\n";
            }
        }
    }
}
?>