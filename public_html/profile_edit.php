<?php
session_start();
if(isset($_GET['lang'])){$lang=$_GET['lang'];$_SESSION['lang']=$lang;}elseif(isset($_SESSION['lang'])){$lang=$_SESSION['lang'];}else{$lang='tr';}
header("Content-Type: text/html; charset=utf-8");
require_once('db_config.php');
if (!isset($_SESSION['user_id'])) {
	header("Location: login.php");
	exit();
}
$user_id = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];

try {
	$stmt = $db->prepare("SELECT id, username, profile_info, profile_image FROM users WHERE id = :id LIMIT 1");
	$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
	$stmt->execute();
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	if (!$user) {
		header("Location: login.php");
		exit();
	}
} catch (PDOException $e) {
	echo $lang === 'tr' ? "Bir hata oluştu." : "An error occurred.";
	exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$profileImage = !empty($user['profile_image']) ? $user['profile_image'] : '';
	$newProfileInfo = isset($_POST['profile_info']) ? trim($_POST['profile_info']) : '';

	if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
		if ($_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
			$uploadDir = __DIR__ . '/profile_images/';
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
			$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
			$maxFileSize = 5 * 1024 * 1024;

			$originalName = $_FILES['profile_image']['name'];
			$tmpName = $_FILES['profile_image']['tmp_name'];
			$fileSize = (int)$_FILES['profile_image']['size'];
			$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimeType = finfo_file($finfo, $tmpName);
			finfo_close($finfo);

			$imageInfo = @getimagesize($tmpName);

			if (
				in_array($extension, $allowedExtensions, true) &&
				in_array($mimeType, $allowedMimeTypes, true) &&
				$imageInfo !== false &&
				$fileSize > 0 &&
				$fileSize <= $maxFileSize
			) {
				$newFileName = 'profile_' . $user_id . '_' . bin2hex(random_bytes(16)) . '.' . $extension;
				$targetPath = $uploadDir . $newFileName;
				$dbPath = 'profile_images/' . $newFileName;

				if (move_uploaded_file($tmpName, $targetPath)) {
					if (!empty($user['profile_image'])) {
						$oldImagePath = __DIR__ . '/' . ltrim($user['profile_image'], '/');
						if (
							file_exists($oldImagePath) &&
							is_file($oldImagePath) &&
							strpos(realpath($oldImagePath), realpath(__DIR__ . '/profile_images')) === 0 &&
							$user['profile_image'] !== $dbPath
						) {
							unlink($oldImagePath);
						}
					}
					$profileImage = $dbPath;
				} else {
					echo $lang === 'tr' ? "Dosya yüklenemedi." : "File upload failed.";
					exit();
				}
			} else {
				echo $lang === 'tr' ? "Yalnızca JPG, JPEG, PNG veya WEBP formatında gerçek görseller yükleyebilirsiniz." : "You can only upload real JPG, JPEG, PNG or WEBP images.";
				exit();
			}
		} else {
			echo $lang === 'tr' ? "Dosya yükleme hatası oluştu." : "A file upload error occurred.";
			exit();
		}
	}

	try {
		$stmt = $db->prepare("UPDATE users SET profile_info = :profile_info, profile_image = :profile_image WHERE id = :id");
		$stmt->bindParam(':profile_info', $newProfileInfo, PDO::PARAM_STR);
		$stmt->bindParam(':profile_image', $profileImage, PDO::PARAM_STR);
		$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		header("Location: /@/" . rawurlencode($username));
		exit();
	} catch (PDOException $e) {
		echo $lang === 'tr' ? "Bir hata oluştu." : "An error occurred.";
		exit();
	}
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></title>
<?php if (!empty($user['profile_image'])): ?>
<link rel="shortcut icon" href="../<?php echo htmlspecialchars($user['profile_image'], ENT_QUOTES, 'UTF-8'); ?>">
<?php else: ?>
<link rel="shortcut icon" href="uzaylogo.ico">
<?php endif; ?>
<link rel="stylesheet" type="text/css" href="styles.css">
<link rel="stylesheet" href="https://www.uzay.info/template/backgroundonly.css">
</head>
<body>
<div class="container">
	<h2><?php echo $lang === 'tr' ? htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . " Profili" : htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . " Profile"; ?></h2>
	<form method="post" enctype="multipart/form-data">
		<label for="profile_image"><?php echo $lang === 'tr' ? "Profil Resmi:" : "Profile Image:"; ?></label><br>
		<input type="file" id="profile_image" name="profile_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><br>
		<p style="color:#555;"><?php echo $lang === 'tr' ? "Mevcut profil resmini korumak için yeni bir görsel yüklenmesine gerek yoktur." : "You don't need to upload a new image to keep the current one."; ?></p>
		<label for="profile_info"><?php echo $lang === 'tr' ? "Profil Bilgisi:" : "Profile Info:"; ?></label><br>
		<textarea id="profile_info" name="profile_info"><?php echo htmlspecialchars($user['profile_info'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea><br>
		<p></p>
		<button type="button" class="btn-geri" onclick="window.location.href='/@/<?php echo rawurlencode($user['username']); ?>'">
			<?php echo $lang === 'tr' ? "Geri Dön" : "Go Back"; ?>
		</button>
		<p></p>
		<input type="submit" value="<?php echo $lang === 'tr' ? "Profili Güncelle" : "Update Profile"; ?>">
	</form>
</div>
</body>
</html>
