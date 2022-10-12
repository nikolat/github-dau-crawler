<?php
$dl = false;
$search_filename = 'repos0.txt';
if (file_exists('./repos/'. $search_filename)) {
    $t_file = filemtime('repos/'. $search_filename);
    $date_now = new DateTime('now');
    $date_now->modify('-60 minute');
    $t_now = $date_now->getTimestamp();
    if ($t_file < $t_now) {
        $dl = true;
    }
}
else {
    $dl = true;
}
if (isset($_GET['force'])) {
    if ($_GET['force'] == 'true') {
        $dl = true;
    }
}
header('Content-type: text/javascript; charset=UTF-8');
if ($dl) {
    // https://pisuke-code.com/php-run-async-process-by-exec/
    $command = "php generate.php";
    if ((substr(PHP_OS, 0, 3) !== 'WIN')) {
        exec($command . ' >/dev/null 2>&1 &');
    } else {
        $fp = popen('start "" '. $command, 'r');
        pclose($fp);
    }
    echo 'console.log("index.html: update");';
}
else {
    echo 'console.log("index.html: keep");';
}
