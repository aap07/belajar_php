<?php
session_start();
//cek session
if ( !isset($_SESSION["login"])) {
	header("Location: login.php");
	exit;
}
//koneksi db
require 'functions.php';
//cek apakah tombol submit sdh ditekan apa blm
if (isset($_POST["submit"])) {
	//cek berhasil atau tidak
	if (tambah($_POST) > 0) {
		echo "
			<script>
				alert('Data Berhasil Disimpan');
				document.location.href='index.php';
			</script>
		";
	}else{
		echo "
			<script>
				alert('Data Gagal Disimpan');
				document.location.href='index.php';
			</script>
		";
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Tambah Data Mahasiswa</title>
</head>
<body>
	<h1>Tambah Data Mahasiswa</h1>
	<form action="" method="post" enctype="multipart/form-data">
		<ul>
			<li>
				<label for="nim">NIM :</label>
				<input type="text" name="nim" id="nim" required autocomplete="off">
			</li>
			<li>
				<label for="nama">Nama :</label>
				<input type="text" name="nama" id="nama" required autocomplete="off">
			</li>
			<li>
				<label for="email">Email :</label>
				<input type="text" name="email" id="email" required autocomplete="off">
			</li>
			<li>
				<label for="jurusan">Jurusan :</label>
				<input type="text" name="jurusan" id="jurusan" required autocomplete="off">
			</li>
			<li>
				<label for="gambar">Gambar :</label>
				<input type="file" name="gambar" id="gambar">
			</li>
			<li>
				<button type="submit" name="submit">Tambah Data</button>
			</li>
		</ul>
	</form>
</body>
</html>