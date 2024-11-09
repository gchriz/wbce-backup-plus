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
 *	AJAX Function to delete the backup, logfile and ev. sql file
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
 *	Delete the backup and the log file
 *
 */
$error = array();
$logfile = $_GET['file'];
$logfilepath = WB_PATH.BACKUP_DATA_DIR.$logfile;
if (!unlink($logfilepath)) {
	$error[] = $MOD_BACKUP['BACKUP_DELETE_LOG_ERROR'];
}

$zipfile = str_replace('.log', '.zip', $logfile);
$zipfilepath = WB_PATH.BACKUP_DATA_DIR.$zipfile;
if (file_exists($zipfilepath)) {
	if (!unlink($zipfilepath)) {
		$error[] = $MOD_BACKUP['BACKUP_DELETE_ZIP_ERROR'];
	}
}

$sqlfile = str_replace('.log', '.sql', $logfile);
$sqlfilepath = WB_PATH.BACKUP_DATA_DIR.$sqlfile;
if (file_exists($sqlfilepath)) {
	if (!unlink($sqlfilepath)) {
		$error[] = $MOD_BACKUP['BACKUP_DELETE_SQL_ERROR'];
	}
}

// Check if there was an error, otherwise say successful
if (count($error) > 0) {
	$error_msg = implode('<br>', $error);
	die(json_encode(array('code' => 403, 'error' => $error_msg)));
}

// Date time of backup
preg_match('#(\d{10}).(.)#', $logfile, $matches);
if (empty($matches[1])) {
	$datetime = "?";
} else {
	$datetime = gmdate(DEFAULT_DATE_FORMAT.', '.DEFAULT_TIME_FORMAT.':s', $matches[1] + DEFAULT_TIMEZONE);
}

// get backup type
if ($matches[2] == 'f') {
	$name = $MOD_BACKUP['BACKUP_LIST_FULL'];
} elseif ($matches[2] == 'w') {
	$name = $MOD_BACKUP['BACKUP_LIST_WBCE'];
} elseif ($matches[2] == 'p') {
	$name = $MOD_BACKUP['BACKUP_LIST_PAGE'];
} elseif ($matches[2] == 'r') {
	$name = $MOD_BACKUP['BACKUP_LIST_RECO'];
} else {
	$name = "?";
}

die(json_encode(array('code' => 200, 'error' => '', 'message' => "$name $datetime - ".$TEXT['DELETED'])));
