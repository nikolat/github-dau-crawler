<?php
ob_start();
include 'render.php';
$html = ob_get_contents();
include 'createrss2.php';
ob_end_clean();
file_put_contents(__DIR__. '/index.html', $html);
