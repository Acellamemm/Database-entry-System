<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $description = trim($_POST['description']);
    $soundcloud_link = trim($_POST['soundcloud_link']);

    $errors = [];

    if (empty($title)) $errors[] = "Title is required.";
    if (empty($genre)) $errors[] = "Genre is required.";
    if (empty($soundcloud_link)) $errors[] = "SoundCloud link is required.";

    // Basic SoundCloud link validation
    if (!empty($soundcloud_link) && !str_contains($soundcloud_link, 'soundcloud.com')) {
        $errors[] = "Please enter a valid SoundCloud link.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO mixes (user_id, title, genre, description, soundcloud_link) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $_SESSION['user_id'], $title, $genre, $description, $soundcloud_link);

        if ($stmt->execute()) {
            header("Location: dashboard.php?uploaded=1");
            exit();
        } else {
            $errors[] = "Upload failed. Try again.";
        }
        $stmt->close();
    }
}
mysqli_close($conn);
?>

<!-- If errors, show simple error page (you can style later) -->
<!DOCTYPE html>
<html>
<head><title>Upload Error</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="content-wrapper" style="text-align:center;margin-top:100px;">
        <h2>Upload Failed</h2>
        <ul style="color:#ff6b6b;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="dashboard.php" style="color:var(--accent1);">← Back to Dashboard</a>
    </div>
</body>
</html>