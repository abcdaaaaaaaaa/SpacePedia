<?php
session_start();
if (isset($_GET['lang'])) {
	$lang = $_GET['lang'];
	$_SESSION['lang'] = $lang;
} elseif (isset($_SESSION['lang'])) {
	$lang = $_SESSION['lang'];
} else {
	$lang = 'tr';
}
header("Content-Type: text/html; charset=utf-8");
require_once('db_config.php');
if (!isset($_SESSION['user_id'])) {
	header("Location: login.php");
	exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
try {
	$stmt = $db->prepare("SELECT id, username, profile_info, profile_image FROM users WHERE username = :username");
	$stmt->bindParam(':username', $username);
	$stmt->execute();
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	echo ($lang === 'tr' ? "Hata: " : "Error: ") . $e->getMessage();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$uploadDir = 'profile_images/';
	$uploadedImage = $uploadDir . basename($_FILES['profile_image']['name']);
	$imageFileType = strtolower(pathinfo($uploadedImage, PATHINFO_EXTENSION));
	if ($_FILES['profile_image']['tmp_name'] !== "") {
		if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadedImage)) {
			if (!empty($user['profile_image'])) {
				if (file_exists($user['profile_image']) && $user['profile_image'] !== $uploadedImage) {
					unlink($user['profile_image']);
				}
			}
			$profileImage = $uploadedImage;
		}
	} else {
		$profileImage = !empty($user['profile_image']) ? $user['profile_image'] : '';
	}
	$newProfileInfo = $_POST['profile_info'];
	try {
		$stmt = $db->prepare("UPDATE users SET profile_info = :profile_info, profile_image = :profile_image WHERE username = :username");
		$stmt->bindParam(':profile_info', $newProfileInfo);
		$stmt->bindParam(':profile_image', $profileImage);
		$stmt->bindParam(':username', $username);
		$stmt->execute();
		header("Location: /@/$username");
		exit();
	} catch (PDOException $e) {
		echo ($lang === 'tr' ? "Hata: " : "Error: ") . $e->getMessage();
	}
	$user['profile_info'] = $newProfileInfo;
	$user['profile_image'] = $profileImage;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $user['username']; ?></title>
<?php if (!empty($user['profile_image'])): ?>
<link rel="shortcut icon" href="../<?php echo $user['profile_image']; ?>">
<?php else: ?>
<link rel="shortcut icon" href="uzaylogo.ico">
<?php endif; ?>
<link rel="stylesheet" type="text/css" href="styles.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
</head>
<body>
<div class="container">
	<h2><?php echo $lang === 'tr' ? $user['username'] . " Profili" : $user['username'] . " Profile"; ?></h2>
	<form method="post" enctype="multipart/form-data">
		<label for="profile_image"><?php echo $lang === 'tr' ? "Profil Resmi:" : "Profile Image:"; ?></label><br>
		<input type="file" id="profile_image" name="profile_image"><br>
		<p style="color:#555;"><?php echo $lang === 'tr' ? "Mevcut profil resmini korumak için yeni bir görsel yüklenmesine gerek yoktur." : "You don't need to upload a new image to keep the current one."; ?></p>
		<label for="profile_info"><?php echo $lang === 'tr' ? "Profil Bilgisi:" : "Profile Info:"; ?></label><br>
		<textarea id="profile_info" name="profile_info"><?php echo $user['profile_info']; ?></textarea><br>
		<p></p>
		<button type="button" class="btn-geri" onclick="window.location.href='/@/<?php echo $user['username']; ?>'">
			<?php echo $lang === 'tr' ? "Geri Dön" : "Go Back"; ?>
		</button>
		<p></p>
		<input type="submit" value="<?php echo $lang === 'tr' ? "Profili Güncelle" : "Update Profile"; ?>">
	</form>
</div>
</body>
</html>
