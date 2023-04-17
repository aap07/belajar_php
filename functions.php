<?php
//koneksi database
$conn = mysqli_connect("localhost", "root", "", "e_commerce");
// if (!$conn) {
// 	die("Koneksi gagal: " . mysqli_connect_error());
// }
// echo "Koneksi berhasil";

// Fungsi untuk menjalankan query pada database dan mengembalikan hasil
function query($query)
{
	// Mengakses koneksi database yang telah dibuat sebelumnya
	global $conn;
	// Menjalankan query pada database
	$result = mysqli_query($conn, $query);
	// Membuat array kosong untuk menampung hasil
	$rows = [];
	// Mengambil setiap baris hasil query dan memasukkannya ke dalam array
	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	// Mengembalikan hasil dalam bentuk array asosiatif
	return $rows;
}
// Melakukan query pada tabel user dan menyimpan hasilnya dalam variabel $user
// $user = query("SELECT * FROM tbl_user");
// Melakukan perulangan untuk setiap baris dari hasil query dan menampilkan nilai kolom 'nama'
// foreach ($user as $data) {
// 	echo $data['nm_user'] . '<br>';
// }

function tambah($data)
{
	global $conn;
	$nim = htmlspecialchars($data["nim"]);
	$nama = htmlspecialchars($data["nama"]);
	$email = htmlspecialchars($data["email"]);
	$jurusan = htmlspecialchars($data["jurusan"]);

	// upload gambar
	$gambar = upload();
	if (!$gambar) {
		return false;
	}

	$query = "INSERT INTO mahasiswa VALUES
			('','$nim','$nama','$email','$jurusan','$gambar')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}
function upload()
{
	global $conn;
	$namaFile = $_FILES['gambar']['name'];
	$ukuranFile = $_FILES['gambar']['size'];
	$error = $_FILES['gambar']['error'];
	$tmpName = $_FILES['gambar']['tmp_name'];

	//cek apakah ada gambar yg d'upload
	if ($error === 4) {
		echo "
			<script>
				alert('Pilih Gambar Terlebih Dahulu');
			</script>
		";
		return false;
	}
	//cek apakah yg d'upload gambar apa bukan
	$ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
	$ekstensiGambar = explode('.', $namaFile);
	$ekstensiGambar = strtolower(end($ekstensiGambar));
	if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
		echo "
			<script>
				alert('Upload File Dengan ekstensi (.jpg - .jpeg - .png)');
			</script>
		";
		return false;
	}
	//cek ukuran gambar
	if ($ukuranFile > 2500000) {
		echo "
			<script>
				alert('Ukuran Gambar Terlalu Besar (maks:2,5Mb');
			</script>
		";
		return false;
	}
	//upload gambar
	//ganti nama gambar
	$namaFilebaru = uniqid();
	$namaFilebaru .= ".";
	$namaFilebaru .= $ekstensiGambar;
	move_uploaded_file($tmpName, 'img/' . $namaFilebaru);
	return $namaFilebaru;
}
function hapus($id)
{
	global $conn;
	mysqli_query($conn, "DELETE FROM mahasiswa WHERE id = $id");
	return mysqli_affected_rows($conn);
}
function ubah($data)
{
	global $conn;
	$id = $data["id"];
	$nim = htmlspecialchars($data["nim"]);
	$nama = htmlspecialchars($data["nama"]);
	$email = htmlspecialchars($data["email"]);
	$jurusan = htmlspecialchars($data["jurusan"]);
	$gambarLama = htmlspecialchars($data["gambarLama"]);

	//cek apa ganti gamabar
	if ($_FILES['gambar']['error'] === 4) {
		$gambar = $gambarLama;
	} else {
		$gambar = upload();
	}

	$query = "UPDATE mahasiswa SET nim='$nim', nama='$nama', email='$email', jurusan='$jurusan', gambar='$gambar' WHERE id = '$id'";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}
function cari($keyword)
{
	global $conn;
	$query = "SELECT * FROM mahasiswa WHERE nama LIKE '%$keyword%' OR nim LIKE '%$keyword%' OR jurusan LIKE '%$keyword%'";
	return query($query);
}
//fungsi untuk meregistrasi user baru
function registrasi($data)
{
	global $conn;
	//mengambil username dan menghapus karakter slash
	$username = strtolower(stripslashes($data["username"]));
	//mengambil nama dan menghapus karakter slash
	$name = strtolower(stripslashes($data["name"]));
	//mengambil password dan mencegah karakter yang tidak diinginkan seperti tanda petik tunggal, dll
	$password = mysqli_real_escape_string($conn, $data["password"]);

	//cek apakah username sudah digunakan sebelumnya
	$result = mysqli_query($conn, "SELECT username FROM tbl_user WHERE username = '$username'");
	if (mysqli_fetch_assoc($result)) {
		echo "
			<script>
				alert('Username Sudah Ada');
			</script>
		";
		return false;
	}
	//enkripsi password sebelum disimpan di database
	$password = password_hash($password, PASSWORD_DEFAULT);
	//tambahkan user baru ke dalam database
	mysqli_query($conn, "INSERT INTO tbl_user VALUES('','$username','$name','$password')");
	//mengembalikan jumlah baris yang terpengaruh oleh query sebelumnya
	return mysqli_affected_rows($conn);
}
