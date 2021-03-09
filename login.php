<?php
//koneksi db
require 'functions.php';
session_start();
//cek cookie
if (isset($_COOKIE['id']) && isset($_COOKIE['key'])) {
	$id = $_COOKIE['id'];
	$key = $_COOKIE['key'];
	//ambil username
	$result = mysqli_query($conn, "SELECT username FROM user WHERE id ='$id'");
	$row = mysqli_fetch_assoc($result);
	//cek cookie dan username
	if ($key === hash('sha256', $row['username'])) {
		$_SESSION['login'] = true;
	}
	// if ($_COOKIE['login'] == 'true') {
	// 	$_SESSION['login'] == true;
	//}
}
//cek session
if ( isset($_SESSION["login"])) {
	header("Location: index.php");
	exit;
}
//tombol cari d'klik
if (isset($_POST["login"])) {
	$username = $_POST["username"];
	$password = $_POST["password"];

	$result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");
	//cek user name
	if (mysqli_num_rows($result)===1) {
		//cek password
		$row = mysqli_fetch_assoc($result);
		if(password_verify($password, $row["password"])){
			//set session
			$_SESSION["login"]=true;
			//cek cookie
			if (isset($_POST['remeber'])) {
				//buat cookie
				setcookie('id', $row['id'],time()+60);
				setcookie('key', hash('sha256', $row['username']),time()+120);
			}
			header("Location: index.php");
			exit;
		}else{
			$error = true;
		}
	}else{
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
	<?php if (isset($error)) : ?>
		<p style="color: red; font-style: italic;">username / password salah</p>
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