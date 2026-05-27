<?php
$conn = @new mysqli("localhost", "puligal_user", "Maveerar2026", "puligal_db");
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$result = $conn->query("SELECT * FROM news WHERE id = $id");
$row = $result->fetch_assoc();

if (!$row) {
    die("பதிவு எதுவும் கண்டுபிடிக்கப்படவில்லை!");
}
?>
<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($row['title']); ?> - புலிகளின் குரல்</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">புலிகளின் குரல்</div>
        <nav><a href="index.php">முகப்பு (Home)</a></nav>
    </header>

    <main class="main-container" style="max-width: 800px; margin: 40px auto;">
        <article class="news-card" style="width: 100%; background: #161616; padding: 30px;">
            <h1 style="color: #ffcc00; text-align: left; margin-bottom: 20px; font-size: 2.2rem;"><?php echo htmlspecialchars($row['title']); ?></h1>
            
            <?php if($row['media_type'] == 'image' && !empty($row['media_path'])): ?>
                <img src="<?php echo htmlspecialchars($row['media_path']); ?>" style="width:100%; max-height:500px; object-fit:contain; border-radius:8px; margin-bottom:20px;">
            <?php elseif($row['media_type'] == 'video' && !empty($row['media_path'])): ?>
                <video controls style="width:100%; margin-bottom:20px;"><source src="<?php echo htmlspecialchars($row['media_path']); ?>"></video>
            <?php endif; ?>

            <p style="color: #dddddd; font-size: 1.1rem; text-align: left; white-space: pre-wrap;"><?php echo htmlspecialchars($row['content']); ?></p>
            <br>
            <a href="index.php" style="color: #ff6600; text-decoration: none;">&larr; முகப்பிற்குத் திரும்புக</a>
        </article>
    </main>
</body>
</html>
