<?php
session_start();
//cek session
if ( !isset($_SESSION["login"])) {
	header("Location: login.php");
	exit;
}
//koneksi db
require 'functions.php';
$id = $_GET["id"];
if (hapus($id)>0) {
	echo "
		<script>
			alert('Data Berhasil Dihapus');
			document.location.href='index.php';
		</script>
	";
}else{
	echo "
		<script>
			alert('Data Gagal Dihapus');
			document.location.href='index.php';
		</script>
	";
}
?>