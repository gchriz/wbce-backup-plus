<?php
/**
 *
 * WebsiteBaker Community Edition (WBCE)
 * Way Better Content Editing.
 * Visit http://wbce.org to learn more and to join the community.
 *
 * @copyright Ryan Djurovich (2004-2009)
 * @copyright WebsiteBaker Org. e.V. (2009-2015)
 * @copyright WBCE Project (2015-)
 * @license GNU GPL2 (or any later version)
*/

/**
 *	AJAX Function to get a list of all the backupfiles:
 *  Check if called by backup parameter
 *
 */
if (!isset($_GET['backup'])) {
	die(header('Location: ../../'));
}

if (!isset($_GET['type'])) {
	die(header('Location: ../../'));
}

/**
 *	Include all initial routines
 *
 */
require_once('init.php');
require_once('info.php');		// contains the $module_version

/**
 -------------------------------------------------------------------------------
 *
 * 	CREATE ZIP FILE
 *
 -------------------------------------------------------------------------------
**/

// Increase the php memory limit temporary to 256 megabytes
ini_set('memory_limit', '256M');

// Make directory for the backups if not yet existing
$dir_path = WB_PATH.BACKUP_DATA_DIR;
make_dir($dir_path);

// Add an index.php file to the directory if not yet existing
createIndexFile($dir_path.'index.php');

// Create the file name prefix
$pfx = new BKU_FilePrefix($dir_path, $_GET['type']);

// Start backup log
$log = new BKU_Log($pfx->get());
$log->write('Backup started - Version ' . $module_version);

// Set the begin path depending on backup type
switch ($_GET['type']) {
	case 'full':
		$source = $_SERVER["DOCUMENT_ROOT"];
		$log->write('This directories are NOT included in zipfile: '.implode(", ",$ignoreFullDirs));
		break;

	case 'wbce':
		$source = WB_PATH;

		// Log ignored dirs
		$log->write('This directories are NOT included in zipfile: '.implode(", ",$ignoreWbceDirs));
		break;

	case 'page':
		$source = WB_PATH;

		// Log included dirs
		$log->write('This directories are included in zipfile: '.implode(", ",$includeDirs));
		break;

	default:	die(json_encode(array('code' => 4032, 'error' => 'invalid Parameter "type"')));
}

if (count($ignoreExts) > 0) {
	$log->write('File with this extensions are ignored: '.implode(", ",$ignoreExts));
}

if (!extension_loaded('zip')) 		die(json_encode(array('code' => 4032, 'error' => "PHP Zip extension not found!")));
if (!file_exists($source)) 			die(json_encode(array('code' => 4033, 'error' => "Zip creation source invalid!")));
if (!is_dir($source)) 				die(json_encode(array('code' => 4034, 'error' => "Zip creation source invalid!")));

$entryFound = false;
$zipfile = $pfx->get().'.zip';

// Instantiate native php class ZipArchive
$zip = new ZipArchive();
$res = $zip->open($zipfile, ZIPARCHIVE::CREATE);
if ($res !== true) {
	$log->write( sprintf($MOD_BACKUP['BACKUP_ZIP_ERROR'],$res));
	die(json_encode(array('code' => 4033, 'error' => sprintf($MOD_BACKUP['BACKUP_ZIP_ERROR'],$res))));
}

$iterator = new RecursiveDirectoryIterator($source);
// skip dot files while iterating
$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
$files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

foreach ($files as $file) {

	$continue = false;
	if (is_file($file)) {

		$filepath = $file->getPathname();

		if (substr($source,-1) == '/') {
			$rootpath = str_replace($source, '', $filepath);
		} else {
			$rootpath = substr(str_replace($source, '', $filepath),1);
		}

		// Skip ignored directories if "wbce" backup
		if ($_GET['type'] == 'wbce') {
			foreach ($ignoreWbceDirs as $exclude) {
				$exclude = $exclude.DIRECTORY_SEPARATOR;
				if (strpos($rootpath, $exclude) !== false || $rootpath == $exclude) {
					$continue = true;
					break;
				}
			}
			if ($continue) continue;
		}

		// Skip ignored directories if "full" backup
		if ($_GET['type'] == 'full') {
			foreach ($ignoreFullDirs as $exclude) {
				$exclude = $exclude.DIRECTORY_SEPARATOR;
				if (strpos($rootpath, $exclude) !== false || $rootpath == $exclude) {
					$continue = true;
					break;
				}
			}
			if ($continue) continue;
		}

		// Include directory only if "page" backup
		if ($_GET['type'] == 'page') {

			foreach ($includeDirs as $include) {
				$include = $include;
				if (strpos($rootpath, $include) === 0) {
					// found - addFile
				} else {
					$continue = true;
					break;
				}
			}
			if ($continue) continue;
		}

		// Skip not allowed extensions
		if (count($ignoreExts) > 0) {
			$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
			if (in_array($ext, $ignoreExts)) {
				continue;
			}
		}

		// Skip large files
		if (is_int($max_file_size)) {
			$max_size_mb = $max_file_size * 1000 * 1000; // MB
			if (filesize($filepath) > $max_size_mb) {
				if ($log_excluded_large_files) {
					$log->write( sprintf('File "%s" skipped, max file size exceeded', $rootpath));
				}
				continue;
			}
		}

		$entryFound = true;
		$zip->addFile($filepath, $rootpath);
	}
}

// if any file found
if ($entryFound === false) {
	$log->write( $TEXT['NONE_FOUND']);
	die(json_encode(array('code' => 4034, 'error' => $TEXT['NONE_FOUND'])));
}

$res = $zip->close();
if ($res === false) {

	$log->write( sprintf($MOD_BACKUP['BACKUP_ZIP_ERROR'],"FALSE"));
	$log->write( "Look also in Admin Errorlog viewer");
	die(json_encode(array('code' => 4035, 'error' => sprintf($MOD_BACKUP['BACKUP_ZIP_ERROR'],"FALSE"))));

} else {

	// this must be the last entry in the logfile: this is checked by list.php
	$log->write(sprintf('Zipfile for backup type "%s" finished successfully'.PHP_EOL,$_GET['type']));
}


/**
 -------------------------------------------------------------------------------
 *
 * 	CREATE SQL EXPORT FILE
 *
 -------------------------------------------------------------------------------
**/

// create pre comment for documentation
 $output = ''.PHP_EOL.
	'# '.PHP_EOL.
	'# Database Backup'.PHP_EOL.
	'# '.$_SERVER['HTTP_HOST'].PHP_EOL.
	'# '.gmdate(DATE_FORMAT, time()+TIMEZONE).', '.gmdate(TIME_FORMAT, time()+TIMEZONE).PHP_EOL.
	'# Backup modul version: '.$module_version.PHP_EOL.
	'# '.PHP_EOL.PHP_EOL;

/**
 *	Get table names
 *	Use this one for aLL tables in DB
 *
 */
$query = "SHOW TABLES";

if ($_GET['type'] == 'wbce') {

	// get ONLY wbce tables
	$prefix = str_replace('_', '\_', TABLE_PREFIX);
	$query = "SHOW TABLES LIKE '".$prefix."%'";
}

if ($_GET['type'] == 'page') {

	foreach( $exportTables as &$tab) {
		$tab = TABLE_PREFIX.$tab;
	}
	$log->write('Only this tables are included in the SQL export: '.implode(", ",$exportTables));
}

$result = $database->query($query);
if ($database->is_error()) {
	die(json_encode(array('code' => 4036, 'error' => $database->get_error())));
}

/**
 *	Loop through tables
 *
 */
while ($row = $result->fetchRow()) {

	if ($_GET['type'] == 'page') {
		if (!in_array($row[0], $exportTables)) {
			continue;
		}
	}

	//	Add Drop existing tables
	$output .= PHP_EOL."# Drop table ".$row[0]." if exists".PHP_EOL."DROP TABLE IF EXISTS `".$row[0]."`;".PHP_EOL;

	//	show sql query
	$sql = 'SHOW CREATE TABLE `'.$row[0].'`';
	$query2 = $database->query($sql);
	if ($database->is_error()) {
		die(json_encode(array('code' => 4037, 'error' => $database->get_error())));
	}

	/**
	 *	Start creating sql-backup
	 *
	 */
	$sql_backup = PHP_EOL."# Create table ".$row[0].PHP_EOL;

	$out = $query2->fetchRow();

	$sql_backup .= $out['Create Table'].";".PHP_EOL.PHP_EOL;
	$sql_backup .= "# Dump data for ".$row[0].PHP_EOL;

	/**
	 *	Select everything
	 *
	 */
	$out = $database->query('SELECT * FROM `'.$row[0].'`');
	$sql_code = '';

	/**
	 *	Loop through all collumns
	 *
	 */
	while ($code = $out->fetchRow(MYSQLI_ASSOC)) {
		$sql_code .= "INSERT INTO `".$row[0]."` SET ";

		foreach ($code as $insert => $value) {
			$sql_code .= "`".$insert ."`='".addslashes($value)."',";
		}
		$sql_code = substr($sql_code, 0, -1);
		$sql_code.= ";".PHP_EOL;
	}
	$output .= $sql_backup.$sql_code.PHP_EOL.PHP_EOL;
}

/**
 *	Write sql file
 *
 */

$sqlfile = $pfx->get().'.sql';
if (file_put_contents($sqlfile, $output) === false) {
	die(json_encode(array('code' => 4038, 'error' => $MOD_BACKUP['BACKUP_CREATE_SQL_ERROR'])));
}

$log->write('SQL export for backup type "' . $_GET['type'] . '" finished successfully');
$log->close();

die(json_encode(array('code' => 200, 'error' => '', 'message' => $MOD_BACKUP['BACKUP_DONE'])));


/*****************************************************************************************************/

function createIndexFile( $file ) {
	if (!is_file($file)) {
		$content = ''."<?php header('Location: ../index.php',true,301);";
		$handle = fopen($file, 'w');
		fwrite($handle, $content);
		fclose($handle);
		change_mode($file, 'file');
	}
}

?>

