<?php
// Path ke file atau direktori sumber
$source = '/path/to/source/file_or_directory';

// Path tujuan untuk symlink
$target = '/path/to/target/symlink';

// Membuat symlink
if (symlink($source, $target)) {
    echo "Symlink berhasil dibuat!";
} else {
    echo "Gagal membuat symlink.";
}
?>
