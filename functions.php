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
	// mengakses variabel $conn yang didefinisikan di luar fungsi
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

//fungsi untuk meregistrasi user baru
function tambah_user($data)
{
	// Mengakses koneksi database yang telah dibuat sebelumnya
	// mengakses variabel $conn yang didefinisikan di luar fungsi
	global $conn;
	//mengambil username dan menghapus karakter slash
	$username = strtolower(stripslashes($data["username"]));
	//mengambil nama dan menghapus karakter slash
	$name = strtolower(stripslashes($data["name"]));
	//mengambil password dan mencegah karakter yang tidak diinginkan seperti tanda petik tunggal, dll
	$password = mysqli_real_escape_string($conn, $data["password"]);
	//Memanggil fungsi upload dan menyimpan hasilnya pada variabel $gambar
	$gambar = upload();
	if (!$gambar) {
		//jika fungsi upload mengembalikan nilai false, maka fungsi yang memanggilnya juga mengembalikan false
		return false;
	}
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
	mysqli_query($conn, "INSERT INTO tbl_user VALUES('','$username','$name','$gambar','$password')");
	//mengembalikan jumlah baris yang terpengaruh oleh query sebelumnya
	return mysqli_affected_rows($conn);
}

//fungsi untuk mengupload gambar
function upload()
{
	// Mengakses koneksi database yang telah dibuat sebelumnya
	// mengakses variabel $conn yang didefinisikan di luar fungsi
	global $conn;
	//mendapatkan informasi file gambar yang diupload
	//mendapatkan nama file gambar
	$namaFile = $_FILES['gambar']['name'];
	//mendapatkan ukuran file gambar
	$ukuranFile = $_FILES['gambar']['size'];
	//mendapatkan kode error jika ada
	$error = $_FILES['gambar']['error'];
	//mendapatkan path sementara file gambar
	$tmpName = $_FILES['gambar']['tmp_name'];
	//cek apakah gambar sudah dipilih atau belum
	// Terdapat beberapa nilai error yang umumnya digunakan dalam PHP, yaitu:
	// 0: Tidak terdapat kesalahan, file berhasil diupload.
	// 1: Ukuran file melebihi batas yang diizinkan oleh server.
	// 2: Ukuran file melebihi batas yang diizinkan oleh form.
	// 3: File hanya berhasil diupload sebagian.
	// 4: Tidak ada file yang diupload.
	// 6: Tidak ditemukan folder temporary.
	// 7: Gagal menyimpan file ke server.
	// 8: File upload dihentikan oleh ekstensi PHP.
	if ($error === 4) {
		echo "
			<script>
				alert('Pilih Gambar Terlebih Dahulu');
			</script>
		";
		return false;
	}
	//mendefinisikan jenis ekstensi file gambar yang diijinkan
	$ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
	//Membagi string nama file gambar dengan karakter "." dan menyimpannya dalam array $ekstensiGambar.
	$ekstensiGambar = explode('.', $namaFile);
	//lalu, end() digunakan untuk mengambil elemen terakhir dari array, yaitu ekstensi file dan strtolower() digunakan untuk mengubah semua huruf dalam ekstensi file menjadi huruf kecil.
	$ekstensiGambar = strtolower(end($ekstensiGambar));
	//cek apakah ekstensi gambar yang diupload sesuai dengan yang diijinkan
	if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
		echo "
			<script>
				alert('Upload File Dengan ekstensi (.jpg - .jpeg - .png)');
			</script>
		";
		return false;
	}
	//cek ukuran file gambar
	if ($ukuranFile > 2500000) {
		echo "
			<script>
				alert('Ukuran Gambar Terlalu Besar (maks:2,5Mb');
			</script>
		";
		return false;
	}
	//membuat nama file gambar baru dengan memakai uniqid() dan menggabungkan dengan ekstensi gambar
	$namaFilebaru = uniqid();
	$namaFilebaru .= ".";
	$namaFilebaru .= $ekstensiGambar;
	//memindahkan file gambar ke direktori yang ditentukan
	move_uploaded_file($tmpName, 'img/' . $namaFilebaru);
	//mengembalikan nama file gambar baru
	return $namaFilebaru;
}

//fungsi untuk ubah data user
function ubah_user($data)
{
	// Mengakses koneksi database yang telah dibuat sebelumnya
	// mengakses variabel $conn yang didefinisikan di luar fungsi
	global $conn;
	// mengambil nilai id dari data yang diberikan dari file ubah_user
	$id = $data["id"];
	// mengambil nilai username dari data dalam file ubah_user dan melakukan sanitasi karakter khusus
	$username = htmlspecialchars($data["username"]);
	// mengambil nilai name dari data dalam file ubah_user dan melakukan sanitasi karakter khusus
	$name = htmlspecialchars($data["name"]);
	// mengambil nilai gambarLama dari data dalam file ubah_user dan melakukan sanitasi karakter khusus
	$gambarLama = htmlspecialchars($data["gambarLama"]);
	// memeriksa apakah terdapat error pada file yang diupload
	if ($_FILES['gambar']['error'] === 4) {
		// jika ada error, gunakan gambar lama
		$gambar = $gambarLama;
	} else {
		// jika tidak ada error, upload gambar baru
		$gambar = upload();
	}
	// membuat query SQL untuk mengubah data user dengan nilai baru dengan kolom id pada tbl_user sebagai acuan
	$query = "UPDATE tbl_user SET username='$username', nm_user='$name',foto='$gambar' WHERE id_user = '$id'";
	// menjalankan query SQL
	mysqli_query($conn, $query);
	// mengembalikan jumlah baris yang terpengaruh oleh query SQL
	return mysqli_affected_rows($conn);
}

//fungsi untuk hapus user
function hapus_user($id)
{
	// Mengakses koneksi database yang telah dibuat sebelumnya
	// mengakses variabel $conn yang didefinisikan di luar fungsi
	global $conn;
	// menjalankan query untuk menghapus data tbl_user dengan id tertentu
	mysqli_query($conn, "DELETE FROM tbl_user WHERE id_user = $id");
	// mengembalikan jumlah baris yang terpengaruh dari operasi hapus
	return mysqli_affected_rows($conn);
}
