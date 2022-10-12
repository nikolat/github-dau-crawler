<?php
ob_start();
include 'render.php';
$html = ob_get_contents();
include 'createrss2.php';
ob_end_clean();
if (!file_exists('./docs/')) {
    mkdir('./docs');
}
array_map('unlink', glob('repos/*.txt'));
file_put_contents(__DIR__. '/docs/index.html', $html);
