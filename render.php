<?php
date_default_timezone_set('Asia/Tokyo');
if (!file_exists('./repos/')) {
    mkdir('./repos');
}
$dl = true;
$max = 30;
$search_key = 'sirefaso';
$search_url = 'https://api.github.com/search/repositories?q=topic:'. $search_key. '&sort=updated';
$search_filename = 'repos.json';
if ($dl) {
    download_file($search_url, 'repos/'. $search_filename);
}
$json = file_get_contents(__DIR__. '/repos/'. $search_filename);
if ($json === false) {
    throw new \RuntimeException('file not found.');
}
$data_repos = json_decode($json, true);
$n = $data_repos['total_count'];
if ($n > $max) {
    $n = $max;
}
$repos = [];
$c = 0;
$date_now = strtotime('now');
for ($i = 0; $i < $n; $i++) {
    $item = $data_repos['items'][$i];
    if (!isset($item['topics'])) {
        continue;
    }
    $topics = $item['topics'];
    $type = '';
    if (in_array('ukagaka-ghost', $topics)) {
        $type = 'ghost';
    }
    elseif (in_array('ukagaka-shell', $topics)) {
        $type = 'shell';
    }
    elseif (in_array('ukagaka-balloon', $topics)) {
        $type = 'balloon';
    }
    elseif (in_array('ukagaka-plugin', $topics)) {
        $type = 'plugin';
    }
    elseif (in_array('ukagaka-shiori', $topics)) {
        $type = 'shiori';
    }
    elseif (in_array('ukagaka-saori', $topics)) {
        $type = 'saori';
    }
    elseif (in_array('ukagaka-spec', $topics)) {
        $type = 'spec';
    }
    else {
        continue;
    }
    $date_update = strtotime($item['pushed_at']);
    $diff = $date_now - $date_update;
    $classname = '';
    if ($diff < 1 * 24 * 60 * 60) {
        $classname = 'days-over-0';
    }
    else if ($diff < 7 * 24 * 60 * 60) {
        $classname = 'days-over-1';
    }
    else if ($diff < 30 * 24 * 60 * 60) {
        $classname = 'days-over-7';
    }
    else if ($diff < 365 * 24 * 60 * 60) {
        $classname = 'days-over-30';
    }
    else {
        $classname = 'days-over-365';
    }
    $repo = [
        'id' => str_replace('/', '_', $item['full_name']),
        'title' => $item['name'],
        'category' => $type,
        'classname' => $classname,
        'author' => $item['owner']['login'],
        'html_url' => $item['html_url'],
        'created_at_time' => $item['created_at'],
        'created_at_str' => date("Y-m-d H:i:s", strtotime($item['created_at'])),
        'updated_at_time' => $item['pushed_at'],
        'updated_at_str' => date("Y-m-d H:i:s", strtotime($item['pushed_at']))
    ];
    $repos[$c] = $repo;
    $c++;
}
function download_file($url, $filename)
{
    $ch = curl_init($url);
    $fp = fopen($filename, 'w');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/1.0 (Win3.1)');
    curl_exec($ch);
    if(curl_error($ch)) {
        fwrite($fp, curl_error($ch));
    }
    curl_close($ch);
    fclose($fp);
}
function s($s)
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401);
}
?><!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width" />
<title>偽SiReFaSo</title>
<link rel="stylesheet" href="./media/css/common.css" media="screen and (min-width: 481px)" />
<link rel="stylesheet" href="./media/css/smartphone.css" media="only screen and (max-width: 480px)" />
<link rel="help" href="./about.html" />
<script src="./update.php" defer="defer"></script>
</head>
<body>
	<header id="header">
		<div id="title-area">
			<h1 id="title"><a href="./">偽SiReFaSo</a></h1>
			<h2 id="subtitle">UKAGAKA Ghost Update Feed Generator</h2>
		</div>
		<section id="menu-area">
			<h2>navigation</h2>
			<nav class="category">
				<h3>categories</h3>
				<ul>
					<li class="about"><a href="./about.html">about</a></li>
				</ul>
			</nav>
		</section>
	</header>
	<section id="content" class="hfeed">
		<h2 class="entries-caption">entries</h2>
<?php for ($i = 0; $i < count($repos); $i++) { ?>
		<article id="<?php echo s($repos[$i]['id']); ?>" class="hentry autopagerize_page_element <?php echo s($repos[$i]['category']); ?> <?php echo s($repos[$i]['classname']); ?>">
			<h3 class="entry-title"><a href="<?php echo s($repos[$i]['html_url']); ?>" rel="bookmark"><?php echo s($repos[$i]['title']); ?></a></h3>
			<dl>
				<dt class="created">登録日時</dt>
				<dd class="created"><time datetime="<?php echo s($repos[$i]['created_at_time']); ?>"><?php echo s($repos[$i]['created_at_str']); ?></time></dd>
				<dt class="updated">最終更新</dt>
				<dd class="updated"><time datetime="<?php echo s($repos[$i]['updated_at_time']); ?>"><?php echo s($repos[$i]['updated_at_str']); ?></time></dd>
				<dt class="author">作者</dt>
				<dd class="author"><?php echo s($repos[$i]['author']); ?></dd>
			</dl>
		</article>
<?php } ?>
	</section>
	<footer id="footer">
		<p id="copyright"><small>Copyright &#169; 2022 <a href="./">偽SiReFaSo</a></small></p>
	</footer>
</body>
</html>
