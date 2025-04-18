<?php
session_start();
require 'vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;

include "db.php";

if (!isset($_SESSION['user_id'])) {
    die("You must login first.");
}

if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmpPath = $_FILES['image']['tmp_name'];
    $imageName = basename($_FILES['image']['name']);
    $userId = $_SESSION['user_id'];

    // GCS settings
    $storage = new StorageClient([
        'keyFilePath' => 'gcs-key.json',
        'projectId' => '2020FC'
    ]);

    $bucket = $storage->bucket('fc-user-images');
    $object = $bucket->upload(
        fopen($tmpPath, 'r'),
        ['name' => time() . '-' . $imageName]
    );

    $publicUrl = 'https://storage.googleapis.com/' . $bucket->name() . '/' . $object->name();

    $stmt = $conn->prepare("UPDATE users SET image_url = ? WHERE id = ?");
    $stmt->bind_param("si", $publicUrl, $userId);
    $stmt->execute();

    echo "Upload successful. <br><img src='$publicUrl' width='150'>";
} else {
    echo "Error uploading image.";
}
?>
