<?php
// Mengimpor file functions.php yang berisi definisi fungsi dan koneksi database
require 'functions.php';
// Memulai sesi untuk penggunaan variabel global $_SESSION
session_start();
// Cek apakah variabel session "login" sudah di-set, jika belum maka user akan di-redirect ke halaman login
if (!isset($_SESSION["login"])) {
	header("Location: login.php");
	exit;
}
// Deklarasi variabel untuk menentukan jumlah data yang akan ditampilkan pada setiap halaman
$jumlahDataPerHalaman = 2;
// Menghitung jumlah data yang ada di database
$jumlahData = count(query("SELECT * FROM tbl_user"));
// Menghitung jumlah halaman yang diperlukan
$jumlahHalaman = ceil($jumlahData / $jumlahDataPerHalaman);
// Menentukan halaman aktif saat ini, jika tidak ada parameter halaman di URL maka halaman aktif = 1
$halamanAktif = (isset($_GET["halaman"])) ? $_GET["halaman"] : 1;
// Menentukan awal data yang akan ditampilkan pada halaman aktif
$awalData = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;
// Mengambil data user dari database sesuai dengan halaman aktif dan jumlah data per halaman
$user = query("SELECT * FROM tbl_user LIMIT $awalData, $jumlahDataPerHalaman");
?>

<!DOCTYPE html>
<head>
	<title>Home</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<style>
	</style>
</head>

<body>
	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link active" aria-current="page" href="index.php">User</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#">Produk</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#">Transaksi</a>
		</li>
	</ul>
	<br>
	<a class="btn btn-outline-primary" href="tambah.php">Tambah Data</a>
	<a class="btn btn-outline-danger" href="logout.php">Logout</a>
	<br>
	<br>
	<!-- Jika halaman aktif lebih besar dari 1 maka akan menampilkan tombol untuk ke halaman sebelumnya -->
	<?php if ($halamanAktif > 1) : ?>
		<a href="?halaman=<?= $halamanAktif - 1 ?>">&laquo;</a>
	<?php endif; ?>
	<!-- Perulangan for digunakan untuk menampilkan link ke semua halaman yang ada -->
	<?php for ($i = 1; $i <= $jumlahHalaman; $i++) : ?>
		<?php if ($i == $halamanAktif) : ?>
			<!-- // Jika nomor halaman sama dengan halaman aktif maka akan menampilkan nomor halaman dengan warna merah dan bold -->
			<a href="?halaman=<?= $i; ?>" style="font-weight: bold; color: red;"><?= $i; ?></a>
		<?php else : ?>
			<!-- // Jika nomor halaman tidak sama dengan halaman aktif maka akan menampilkan nomor halaman biasa tanpa style -->
			<a href="?halaman=<?= $i; ?>"><?= $i; ?></a>
		<?php endif; ?>
	<?php endfor; ?>
	<!-- Jika halaman aktif kurang dari jumlah halaman maka akan menampilkan tombol untuk ke halaman selanjutnya -->
	<?php if ($halamanAktif < $jumlahHalaman) : ?>
		<a href="?halaman=<?= $halamanAktif + 1 ?>">&raquo;</a>
	<?php endif; ?>
	<br>
	<table class="table">
		<thead class="table-dark">
			<tr>
				<th>No. </th>
				<th>Nama</th>
				<th>Username</th>
			</tr>
		</thead>
		<tbody>
			<!-- Inisialisasi variabel $i dengan nilai 1, kemudian melakukan looping dengan foreach untuk setiap data user pada array $user. -->
			<?php $i = 1; ?>
			<!-- Untuk setiap data user, dibuat sebuah baris tabel (<tr>) yang berisi nomor urut ($i ditambah $awalData), nama user, dan username. -->
			<?php foreach ($user as $row) : ?>
				<tr>
					<!-- Kemudian variabel $i ditambah 1 untuk menambah nomor urut pada baris selanjutnya. -->
					<td><?= $i + $awalData ?></td>
					<td><?= $row["nm_user"]; ?></td>
					<td><?= $row["username"]; ?></td>
				</tr>
				<?php $i++; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
</body>

</html>