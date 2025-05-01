<?php
// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Pastikan ZipArchive tersedia
if (!class_exists('ZipArchive')) {
    die("Error: Ekstensi ZipArchive tidak tersedia. Silakan aktifkan di php.ini");
}

// Fungsi untuk membuat zip dari direktori
function createUnlimitedZip($sourcePath, $zipFilePath) {
    $zip = new ZipArchive();
    
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        throw new Exception("Gagal membuka/membuat file zip");
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourcePath, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
        try {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($sourcePath) + 1);

            if ($file->isDir()) {
                if (!$zip->addEmptyDir($relativePath)) {
                    throw new Exception("Gagal menambahkan direktori: " . $relativePath);
                }
            } else {
                if (!$zip->addFile($filePath, $relativePath)) {
                    throw new Exception("Gagal menambahkan file: " . $relativePath);
                }
            }
        } catch (Exception $e) {
            $zip->close();
            unlink($zipFilePath);
            throw $e;
        }
    }

    $zip->close();
    return true;
}

// Fungsi validasi path dengan pengecekan lebih ketat
function validatePath($path) {
    if (empty($path)) {
        throw new Exception("Path tidak boleh kosong");
    }

    $realPath = realpath($path);
    if ($realPath === false) {
        throw new Exception("Path tidak valid: " . htmlspecialchars($path));
    }

    $rootPath = realpath($_SERVER['DOCUMENT_ROOT']);
    if (strpos($realPath, $rootPath) !== 0) {
        throw new Exception("Path berada di luar root directory");
    }

    return $realPath;
}

// Fungsi untuk membersihkan nama file
function cleanFilename($filename) {
    $clean = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $filename);
    return substr($clean, 0, 100); // Batasi panjang nama file
}

// Fungsi format ukuran file
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' bytes';
}

// Proses utama
try {
    $message = '';
    $zipCreated = false;
    $zipFileUrl = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zip_path'])) {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $pathToZip = $_POST['zip_path'];
        $zipName = isset($_POST['zip_name']) ? cleanFilename($_POST['zip_name']) : 'backup_' . date('Y-m-d_His');
        $validatedPath = validatePath($pathToZip);

        $zipFileName = $zipName . '.zip';
        $zipFilePath = __DIR__ . DIRECTORY_SEPARATOR . $zipFileName;

        if (createUnlimitedZip($validatedPath, $zipFilePath)) {
            $message = "File ZIP berhasil dibuat! Ukuran: " . formatSizeUnits(filesize($zipFilePath));
            $zipCreated = true;
            $zipFileUrl = $zipFileName;
        }
    }

    // Handle delete request
    if (isset($_GET['delete'])) {
        $fileToDelete = __DIR__ . DIRECTORY_SEPARATOR . $_GET['delete'];
        if (validatePath($fileToDelete) && unlink($fileToDelete)) {
            $message = "File ZIP berhasil dihapus!";
        } else {
            throw new Exception("Gagal menghapus file");
        }
    }
} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
}

// Tampilkan HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unlimited ZIP Creator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background-color: #337ab7;
            color: white;
        }
        .btn-danger {
            background-color: #d9534f;
            color: white;
        }
        .zip-item {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Unlimited ZIP Creator</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo strpos($message, 'Error:') === 0 ? 'alert-danger' : 'alert-success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="zip_path">Directory Path:</label>
                <input type="text" id="zip_path" name="zip_path" 
                       value="<?php echo isset($_POST['zip_path']) ? htmlspecialchars($_POST['zip_path']) : htmlspecialchars(__DIR__); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="zip_name">ZIP Filename (without extension):</label>
                <input type="text" id="zip_name" name="zip_name" 
                       value="<?php echo isset($_POST['zip_name']) ? htmlspecialchars($_POST['zip_name']) : 'backup_'.date('Y-m-d_His'); ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Create ZIP</button>
        </form>

        <?php if ($zipCreated): ?>
            <div class="zip-item">
                <span><?php echo htmlspecialchars($zipFileUrl); ?></span>
                <div>
                    <a href="<?php echo htmlspecialchars($zipFileUrl); ?>" class="btn btn-primary" download>Download</a>
                    <a href="?delete=<?php echo urlencode($zipFileUrl); ?>" class="btn btn-danger" onclick="return confirm('Delete this file?')">Delete</a>
                </div>
            </div>
        <?php endif; ?>

        <h3>Existing ZIP Files:</h3>
        <?php
        $zipFiles = glob(__DIR__ . '/*.zip');
        if (empty($zipFiles)) {
            echo "<p>No ZIP files found.</p>";
        } else {
            foreach ($zipFiles as $zipFile) {
                $fileName = basename($zipFile);
                echo '<div class="zip-item">';
                echo '<span>' . htmlspecialchars($fileName) . ' (' . formatSizeUnits(filesize($zipFile)) . ')</span>';
                echo '<div>';
                echo '<a href="' . htmlspecialchars($fileName) . '" class="btn btn-primary" download>Download</a>';
                echo '<a href="?delete=' . urlencode($fileName) . '" class="btn btn-danger" onclick="return confirm(\'Delete this file?\')">Delete</a>';
                echo '</div>';
                echo '</div>';
            }
        }
        ?>
    </div>
</body>
</html>
