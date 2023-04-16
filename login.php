<?php
// Mengimpor file functions.php yang berisi definisi fungsi dan koneksi database
require 'functions.php';
// Memulai sesi untuk penggunaan variabel global $_SESSION
session_start();

// Pengecekan apakah cookie sudah ter-set sebelumnya
if (isset($_COOKIE['id']) && isset($_COOKIE['key'])) {
	// Mengambil nilai cookie yang tersimpan
	$id = $_COOKIE['id'];
	$key = $_COOKIE['key'];
	// Query ke database untuk mengambil username dengan id yang didapat dari cookie
	$result = mysqli_query($conn, "SELECT username FROM user WHERE id ='$id'");
	$row = mysqli_fetch_assoc($result);
	// Mengecek apakah hash dari username sama dengan nilai key pada cookie
	if ($key === hash('sha256', $row['username'])) {
		// Jika sama, maka user dianggap sudah login dan session di-set
		$_SESSION['login'] = true;
	}
	// if ($_COOKIE['login'] == 'true') {
	// 	$_SESSION['login'] == true;
	//}
}

// Cek apakah user sudah login, jika sudah redirect ke halaman index
if (isset($_SESSION["login"])) {
	header("Location: index.php");
	exit;
}

// Jika tombol login ditekan, maka:
if (isset($_POST["login"])) {
	// Mengambil data username dan password dari form login
	$username = $_POST["username"];
	$password = $_POST["password"];
	// Menjalankan query pada tabel user untuk mengambil data user dengan username yang sesuai dengan inputan username
	$result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");
	// Jika query menghasilkan satu baris, maka:
	if (mysqli_num_rows($result) === 1) {
		// Mengambil data user dari hasil query
		$row = mysqli_fetch_assoc($result);
		// Melakukan verifikasi password
		if (password_verify($password, $row["password"])) {
			// Jika password benar, maka set session login menjadi true
			$_SESSION["login"] = true;
			// Jika checkbox remember me di-check, maka set cookie
			if (isset($_POST['remeber'])) {
				// Membuat cookie dengan nama 'id' dan nilai id user yang sedang login
				setcookie('id', $row['id'], time() + 60);
				// Membuat cookie dengan nama 'key' dan nilai hash sha256 dari username user yang sedang login
				setcookie('key', hash('sha256', $row['username']), time() + 120);
			}
			// Redirect ke halaman index.php
			header("Location: index.php");
			exit;
		} else {
			// Jika password salah, maka set variabel error menjadi true
			$error = true;
		}
	} else {
		// Jika username tidak ditemukan di database, maka set variabel 
		$error = true;
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Halaman Login</title>
</head>

<body>
	<h1>Halaman Login</h1>
	// Ini adalah bagian dari struktur kontrol PHP yang disebut sebagai "conditional statement" atau "percabangan".
	<?php if (isset($error)) : ?>
		// Jika variabel $error telah di-set oleh kode sebelumnya, maka akan menampilkan pesan kesalahan pada halaman web.
		<p style="color: red; font-style: italic;">username / password salah</p>
	// Syntax endif digunakan untuk menutup blok kondisi IF di baris sebelumnya.
	<?php endif; ?>
	<form action="" method="post">
		<ul>
			<li>
				<label for="username">Username :</label>
				<input type="text" name="username" id="username" autocomplete="off">
			</li>
			<li>
				<label for="password">Password :</label>
				<input type="password" name="password" id="password">
			</li>
			<li>
				<input type="checkbox" name="remember" id="remember">
				<label for="remember">Remember Me</label>
			</li>
			<li>
				<button type="submit" name="login">Login</button>
			</li>
		</ul>
	</form>
</body>

</html>