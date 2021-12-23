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

/**
 -------------------------------------------------------------------------------
 *
 * 	RESTORE DATA FROM ZIP FILE
 *
 -------------------------------------------------------------------------------
**/

// Create the file name prefix
$pfx = new BKU_FilePrefix(WB_PATH.BACKUP_DATA_DIR, "reco");

// Start backup log
$log = new BKU_Log($pfx->get());
$log->write('Restore started');

// get backup type
preg_match('#(\d{10}).(.)#', $_GET['file'], $matches);

// if restore of a pageBackup: delete all files in pages directories first or create directory
if ($matches[2] == 'p') {

	foreach($includeDirs as $dir) {
		if (!is_Dir($dir)) {
			if (mkdir($dir, 0705, true) === false) {
				abort(array('code' => 4031, 'error' => sprintf($MOD_BACKUP['BACKUP_CREATE_DIR_ERROR'],$dir)));
			} else {
				$log->write( sprintf('Restore directory "%s" created',$dir));
			}
		} else {
			if (cleanDir($dir) == false) {
				abort(array('code' => 4032, 'error' => sprintf($MOD_BACKUP['BACKUP_DELETE_PAGE_ERROR'],$dir)));
			}
			$log->write( sprintf('Restore directory "%s" cleaned',$dir));
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

// extract it to the path we determined above
if ($matches[2] == 'f') {
	$zip->extractTo($_SERVER["DOCUMENT_ROOT"]);
}
 else {
	$zip->extractTo(WB_PATH);
}

$res = $zip->close();
if ($res === false) {
	$log->write( sprintf($MOD_BACKUP['BACKUP_ZIP_ERROR'],$res));
	abort(array('code' => 4034, 'error' => sprintf($MOD_BACKUP['BACKUP_ZIP_ERROR'],$res)));
} else {
	$log->write( sprintf('Restore zipfile "%s" sucessfull', $zipfile));
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

// execute multi query
$db = $database->__get('db_handle');
if (!($db->multi_query($sql))) {
	abort(array('code' => 4036, 'error' => print_r($db->error,true)));
}

$log->write( sprintf('Restore SQL dump "%s" sucessfull', $sqlfile));
$log->write('Restore finished successfully');
$log->close();

abort(array('code' => 200, 'error' => '', 'message' => sprintf($MOD_BACKUP['BACKUP_RESTORED'])));


/*****************************************************************************************************/

function cleanDir($dir) {

	$dir = WB_PATH.DIRECTORY_SEPARATOR.$dir;

	$iterator = new RecursiveDirectoryIterator($dir);
	// skip dot files while iterating
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

	if (empty($status["error"])) {
		$s = 'result=' . urlencode($status["message"]);
	} else {
		$s = 'result=false&error=' . urlencode($status["error"].'<br>Code: '.$status["code"]);
	}
	if ($matches[2] == 'p') {
		header("Location: " . ADMIN_URL . '/admintools/tool.php?tool=backup_plus&'.$s);
	} else {
		header("Location: " . ADMIN_URL . '/login/');
	}
	exit;
}
