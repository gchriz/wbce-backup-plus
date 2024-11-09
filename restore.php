<?php
/**
 * WebsiteBaker Community Edition (WBCE)
 * Way Better Content Editing.
 * Visit http://wbce.org to learn more and to join the community.
 *
 * @copyright Ryan Djurovich (2004-2009)
 * @copyright WebsiteBaker Org. e.V. (2009-2015)
 * @copyright WBCE Project (2015-)
 * @license GNU GPL2 (or any later version)
 *
 */

/**
 *	AJAX Function to restore a backup
 *  Check if called by file parameter
 *
 */
if (!isset($_GET['file'])) {
	die(header('Location: ../../index.php'));
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
 * 	RESTORE DATA FROM ZIP FILE
 *
 -------------------------------------------------------------------------------
**/

// Create the file name prefix
$pfx = new BKU_FilePrefix(WB_PATH.BACKUP_DATA_DIR, "reco");

// Start restore log
$log = new BKU_Log($pfx->get());
$log->write('Restore started - Version ' . $module_version);

// get backup type
preg_match('#(\d{10}).(.)#', $_GET['file'], $matches);

// if restore of a pageBackup: delete all files in pages directories first or create directory
if ($matches[2] == 'p') {

	foreach($includeDirs as $dir) {
		$fulldir = WB_PATH.DIRECTORY_SEPARATOR.$dir;
		if (!is_Dir($fulldir)) {
			if (mkdir($fulldir, 0705, true) === false) {
				abort(array('code' => 4031, 'error' => sprintf($MOD_BACKUP['BACKUP_CREATE_DIR_ERROR'],$fulldir)));
			} else {
				$log->write( sprintf('Restore directory "%s" created',$fulldir));
			}
		} else {
			if (cleanDir($fulldir) == false) {
				abort(array('code' => 4032, 'error' => sprintf($MOD_BACKUP['BACKUP_DELETE_PAGE_ERROR'],$fulldir)));
			}
			$log->write( sprintf('Restore directory "%s" cleaned',$fulldir));
		}
	}
}

// restore the data now (zip-file)
$error = array();
$zipfile = WB_PATH.BACKUP_DATA_DIR.$_GET['file'];

$zip = new ZipArchive;
$res = $zip->open($zipfile);
if ($res !== true) {
	$log->write( sprintf($MOD_BACKUP['BACKUP_ZIP_ERROR'],$res));
	abort(array('code' => 4033, 'error' => sprintf($MOD_BACKUP['BACKUP_ZIP_ERROR'],$res)));
}

// determine the path to extract to
if ($matches[2] == 'f') {
	$zip_dest = $_SERVER["DOCUMENT_ROOT"];
} else {
	$zip_dest = WB_PATH;
}

$log->write( sprintf('Extracting zipfile "%s" to "%s"...', $zipfile, $zip_dest));

// Extract file by file to detect intermediate errors!
$ERROR_UNZIP = false;

for ($i = 0; $i < $zip->numFiles; $i++) {
	$RETVAL = false;
	$filename = $zip->getNameIndex($i);
	$RETVAL = $zip->extractTo($zip_dest,$filename);

	if (! $RETVAL) {
		$log->write( sprintf('ERROR on writing target file: %s', $filename) );
		// TODO: but how to know details of failure?
		$ERROR_UNZIP = true;
	}
}

$res = $zip->close();

if ($res === false || $ERROR_UNZIP) {
	$log->write(sprintf('ERROR unzipping zipfile %s', $zipfile));
	$log->write( sprintf($MOD_BACKUP['BACKUP_ZIP_ERROR'], $res));
	abort(array('code' => 4034, 'error' => sprintf($MOD_BACKUP['BACKUP_ZIP_ERROR'], $res)));
} else {
	$log->write( sprintf('Restore zipfile "%s" successful', $zipfile));
}


/**
 -------------------------------------------------------------------------------
 *
 * 	IMPORT THE SQL EXPORT FILE
 *
 -------------------------------------------------------------------------------
**/

$sqlfile = str_replace('.zip', '.sql', $zipfile);
$sql = file_get_contents($sqlfile);
if ($sql === false) {
	abort(array('code' => 4035, 'error' => $MOD_BACKUP['BACKUP_READ_SQL_ERROR']));
}

$log->write( sprintf('Restoring SQL dump "%s" ...', $sqlfile));

// get the source WB_URL from the SQL file's header comment
$sql_head = substr($sql, 0, 300);
preg_match('/^# WB_URL:\s(.*)$/m', $sql_head, $umatches);
$wb_url_src = $umatches[1];

// if not "full" backup (TODO: does it make sense there, and would it work?)
if ($matches[2] !== 'f') {
	// if source and target WB_URL are different: substitute to target for the import
	if ( $wb_url_src and ($wb_url_src !== WB_URL) ) {
		$log->write( sprintf('From source WB_URL "%s" ...', $wb_url_src));
		$log->write( sprintf('To  target  WB_URL "%s" ...', WB_URL));

		$pattern = '!'.$wb_url_src.'/(\\S)!m';
		$replacement = WB_URL . '/\\1';

		$sql_new = preg_replace($pattern, $replacement , $sql);
		// write changed SQL file for testing/checking purpose
		//file_put_contents($sqlfile.'.new', $sql_new);
		$sql = $sql_new;
	}
}

// execute multi query
$db = $database->__get('db_handle');

if (!($db->multi_query($sql))) {
	$log->write($db->errno . " - " . print_r($db->error,true));
	abort(array('code' => 4036, 'error' => $db->errno . " - " . print_r($db->error,true)));
}

// and walk through all single queries of the multi_query synchronously to detect possible errors!
do {
	$db->next_result();
    // This shows the progress/internal steps within multi_query().
    // Unfortunately without detailed data available...
    //$log->write('.');
} while ($db->more_results());

if ($db->error) {
	$log->write($db->errno . " - " . print_r($db->error,true));
	abort(array('code' => 4036, 'error' => $db->errno . " - " . print_r($db->error,true)));
}

$log->write('Restore finished successfully');
$log->close();

abort(array('code' => 200, 'error' => '', 'message' => sprintf($MOD_BACKUP['BACKUP_RESTORED'])));


/*****************************************************************************************************/

function cleanDir($dir) {
	$iterator = new RecursiveDirectoryIterator($dir);
	// skip dot files (. and ..) while iterating
	$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

	foreach ($files as $file) {

		if (is_file($file)) {
			$filename = $file->getFilename();
			if (!unlink($file)) {
				return false;
			}
		}
	}
	return true;
}

function abort($status) {
	global $matches;
	global $db;

	// Be really sure to have foreign key check enabled again
	// after having it possibly disabled in the SQL file.
	$sql = "SET FOREIGN_KEY_CHECKS=1;";
	if (!($db->query($sql))) {
		// TODO Perhaps another code number, but I don't know the rules for them
		$status = array('code' => 4036, 'error' => $db->errno . " - " . print_r($db->error,true));
	}

	if (empty($status["error"])) {
		$s = 'result=' . urlencode($status["message"]);
	} else {
		$s = 'result=false&error=' . urlencode($status["error"].'<br>Code: '.$status["code"]);
	}
	if (($matches[2] == 'p') || (! empty($status["error"]))) {
		header("Location: " . ADMIN_URL . '/admintools/tool.php?tool=backup_plus&'.$s);
	} else {
		header("Location: " . ADMIN_URL . '/login/');
	}
	exit;
}
