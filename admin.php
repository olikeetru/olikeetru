<?php
session_start();
$admin_user = "admin";
$admin_pass = "Maveerar2026"; 

if (isset($_POST['login'])) {
    if ($_POST['username'] == $admin_user && $_POST['password'] == $admin_pass) {
        $_SESSION['loggedin'] = true;
    } else { $error = "தவறான விபரங்கள்!"; }
}
if (isset($_GET['logout'])) { session_destroy(); header("Location: admin.php"); }

// கோப்பு மற்றும் விபரங்களைப் பதிவேற்றல்
if (isset($_POST['upload']) && isset($_SESSION['loggedin'])) {
    $conn = new mysqli("localhost", "puligal_user", "Maveerar2026", "puligal_db");
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $category = $conn->real_escape_string($_POST['category']);
    
    $birth_date = isset($_POST['birth_date']) ? $conn->real_escape_string($_POST['birth_date']) : '';
    $death_date = isset($_POST['death_date']) ? $conn->real_escape_string($_POST['death_date']) : '';
    $death_place = isset($_POST['death_place']) ? $conn->real_escape_string($_POST['death_place']) : '';

    $media_type = 'none';
    $target_file = '';

    if($_FILES['media']['name']) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        $target_file = $target_dir . time() . "_" . basename($_FILES["media"]["name"]);
        move_uploaded_file($_FILES["media"]["tmp_name"], $target_file);
        
        $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) { $media_type = 'image'; }
        if(in_array($ext, ['mp4', 'mkv', 'avi', 'mov'])) { $media_type = 'video'; }
        if(in_array($ext, ['mp3', 'wav', 'ogg'])) { $media_type = 'audio'; }
    }

    $conn->query("INSERT INTO news (title, content, media_type, media_path, category, birth_date, death_date, death_place) VALUES ('$title', '$content', '$media_type', '$target_file', '$category', '$birth_date', '$death_date', '$death_place')");
    $msg = "பதிவேற்றம் வெற்றிகரமாக முடிந்தது!";
}

// நீக்குவதற்கான குறியீடு
if (isset($_GET['delete']) && isset($_SESSION['loggedin'])) {
    $conn = new mysqli("localhost", "puligal_user", "Maveerar2026", "puligal_db");
    $id_to_delete = intval($_GET['delete']);
    $res = $conn->query("SELECT media_path FROM news WHERE id = $id_to_delete");
    $file_row = $res->fetch_assoc();
    if($file_row && !empty($file_row['media_path']) && file_exists($file_row['media_path'])) { unlink($file_row['media_path']); }
    $conn->query("DELETE FROM news WHERE id = $id_to_delete");
    $msg = "பதிவு வெற்றிகரமாக நீக்கப்பட்டது!";
}
?>
<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - புலிகளின் குரல்</title>
    <style>
        body { background: #121212; color: #fff; font-family: Arial; padding: 30px; }
        .box { background: #1e1e1e; max-width: 600px; margin: auto; padding: 30px; border-radius: 8px; border: 1px solid #333; }
        input, textarea, select { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #444; background: #222; color: #fff; box-sizing: border-box; }
        button { background: #ffcc00; padding: 12px 25px; border: none; cursor: pointer; font-weight: bold; width: 100%; border-radius: 5px; color: #000; }
        .maveerar-fields { display: none; background: #292929; padding: 15px; border-radius: 6px; margin: 10px 0; }
    </style>
    <script>
        function toggleFields(val) {
            document.getElementById('maveerar_info').style.display = (val === 'maveerar') ? 'block' : 'none';
        }
    </script>
</head>
<body>

<?php if(!isset($_SESSION['loggedin'])): ?>
    <div class="box" style="max-width: 400px;">
        <h2>Admin Login</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
<?php else: ?>
    <div class="box">
        <h2>நிர்வாகி பக்கம் (Upload Dashboard)</h2>
        <p><a href="?logout=1" style="color:#ff6600;">Logout</a> | <a href="index.php" target="_blank" style="color:#ffcc00;">View Website</a></p>
        <?php if(isset($msg)) echo "<p style='color:#00ff00;'>$msg</p>"; ?>
        
        <form method="post" enctype="multipart/form-data">
            <label>பதிவின் வகை (Category):</label>
            <select name="category" onchange="toggleFields(this.value)" required>
                <option value="news">செய்திகள் / வீடியோக்கள் (Latest Updates)</option>
                <option value="song">தேசியப் பாடல்கள் (Songs)</option>
                <option value="maveerar">மாவீரர் வரலாறு (Maveerar History)</option>
            </select>

            <input type="text" name="title" placeholder="தலைப்பு / மாவீரர் பெயர்" required>
            <textarea name="content" rows="4" placeholder="விபரம் / வரலாற்றுக்குறிப்பு" required></textarea>

            <!-- மாவீரர் வரலாற்றுக்கான தனி விபரங்கள் -->
            <div id="maveerar_info" class="maveerar-fields">
                <h3>மாவீரர் தரவுகள்:</h3>
                <input type="text" name="birth_date" placeholder="பிறந்த தேதி (எ.கா: 10.05.1970)">
                <input type="text" name="death_date" placeholder="வீரச்சாவு தேதி (எ.கா: 27.11.1989)">
                <input type="text" name="death_place" placeholder="வீரச்சாவடைந்த இடம் (எ.கா: மணலாறு)">
            </div>

            <p>கோப்பினைத் தேர்ந்தெடுக்கவும் (Photo / Video / MP3):</p>
            <input type="file" name="media">
            
            <button type="submit" name="upload">இணையத்தில் பதிவேற்று</button>
        </form>

        <div style="margin-top: 30px; text-align: left; background: #1a1a1a; padding: 20px; border-radius: 8px;">
            <h3>பதிவுகளை நிர்வகித்தல் (Manage Posts)</h3>
            <?php
            $conn = new mysqli("localhost", "puligal_user", "Maveerar2026", "puligal_db");
            $all_news = $conn->query("SELECT id, title, category FROM news ORDER BY id DESC");
            while($post = $all_news->fetch_assoc()):
            ?>
                <div style="display: flex; justify-content: space-between; background: #252525; padding: 10px; margin: 10px 0; border-radius: 4px; border-left: 4px solid #ffcc00;">
                    <span><?php echo htmlspecialchars($post['title']); ?> (<?php echo strtoupper($post['category']); ?>)</span>
                    <a href="admin.php?delete=<?php echo $post['id']; ?>" onclick="return confirm('நிச்சயமாக நீக்க வேண்டுமா?')" style="color: #ff3333; text-decoration: none; font-weight: bold;">Delete</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>

</body>
</html>
