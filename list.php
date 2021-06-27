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
 *	AJAX Function to get a list of all the backupfiles:
 *  Check if called by dir parameter
 *
 */
if (!isset($_GET['dir'])) {
	die(header('Location: ../../index.php'));
}

/**
 *	Include all initial routines
 *
 */
require_once('init.php');


/**
 *	Get list of all backup files (.zip) on the server
 *	Add a download link, log file link and a delete button to each backup
 *
 */

$backup_list 	= '';
$backup_files	= [];
$dir_path 		= WB_PATH.BACKUP_DATA_DIR;
$dir_url 	  	= WB_URL.str_replace('\\', '/', BACKUP_DATA_DIR);

if (is_dir($dir_path)) {
	$backup_files = scan_current_dir($dir_path, '/.*\.log/');
}

if (!empty($backup_files['filename']) && count($backup_files['filename']) > 0) {

	$backup_list  = sprintf('<h2 style="text-align:center">%s</h2><table id="listBackups" class="table table-striped table-hover table-sm">', $MOD_BACKUP['BACKUP_LIST_TITLE']);
	$backup_list .= sprintf('<thead><tr>%s</tr></thead><tbody>', $MOD_BACKUP['BACKUP_LIST_HEADER']);

	$i = 1;
	foreach ($backup_files['filename'] as $logfile) {

		// Date time of backup
		preg_match('#(\d{10}).(.)#', $logfile, $matches);
		if (empty($matches[1])) {
			$datetime = "?";
		} else {
			$datetime = gmdate(DEFAULT_DATE_FORMAT.', '.DEFAULT_TIME_FORMAT.':s', $matches[1] + DEFAULT_TIMEZONE);
		}

		$validation = true;
		$trClass = "";
		$rlink = "&nbsp;";

		// get backup type
		if ($matches[2] == 'f') {
			if (!$wb->ami_group_member('1')) continue;
			$name = $MOD_BACKUP['BACKUP_LIST_FULL'];
		} elseif ($matches[2] == 'w') {
			if (!$wb->ami_group_member('1')) continue;
			$name = $MOD_BACKUP['BACKUP_LIST_WBCE'];
		} elseif ($matches[2] == 'p') {
			$name = $MOD_BACKUP['BACKUP_LIST_PAGE'];
		} elseif ($matches[2] == 'r') {
			$name = $MOD_BACKUP['BACKUP_LIST_RECO'];
			$zfilesize = ""; $rlink = ""; $slink = ""; $zlink = "";
			$validation = false;
		} else {
			$zfilesize = ""; $rlink = ""; $slink = ""; $zlink = "";
			$name = "?";
		}

		// test for incomplete backup
		$zipfile = str_replace('.log', '.zip', $logfile);
		$complete = incompleteBackup($dir_path.$logfile, $matches[2]);
		if ($complete) {
			if ($matches[2] !== 'r') {
				$rlink = sprintf('<a title="%s" class="restore" data-file="%s"><i class="fa fa-recycle"></i></a>',$TEXT['RESTORE'], $zipfile);
			}
		} else {
			$trClass = "error";
		}

		if ($validation) {

			// Zipfile
			if (file_exists($dir_path.$zipfile)) {
				$zlink = sprintf('<a title="%s" href="%s"><i class="fa fa-download"></i></a>',$MOD_BACKUP['BACKUP_ZIP_DOWNLOAD'], $dir_url.$zipfile);

				// Zip file size
				$bytes = filesize($dir_path.$zipfile);
				$zfilesize = human_filesize($bytes);

			} else {
				$zlink = "&nbsp;";
				$zfilesize = $TEXT['ERROR'];
				$complete = false;
			}

			// SQL file
			$sqlfile = str_replace('.log', '.sql', $logfile);
			if (file_exists($dir_path.$sqlfile)) {
				$slink = sprintf('<a title="%s" href="%s"><i class="fa fa-database"></i></a>',$MOD_BACKUP['BACKUP_SQL_DOWNLOAD'], $dir_url.$sqlfile);
			} else {
				$slink = "&nbsp;";
				$complete = false;
			}
		}

		// The list of all backups as a table
		$backup_list .= sprintf('<tr class="%s"><td>%s. %s</td><td>%s</td><td>%s</td><td class="r">', $trClass, $i++, $datetime, $zfilesize, $name );
		$backup_list .= $rlink.$slink.$zlink;
		$backup_list .= sprintf('<a title="%s" class="showlog" data-file="%s"><i class="fa fa-file-text-o"></i></a>',$MOD_BACKUP['BACKUP_LOGFILE'], $dir_url.$logfile);
		$backup_list .= sprintf('<a title="%s" class="delete" data-file="%s"><i class="fa fa-trash"></i></a>',$TEXT['DELETE'], $logfile);
		$backup_list .= '</td></tr>';
	}
	$backup_list .= '</tbody></table>'."\n";

	// return the list to ajax call now
	die(json_encode(array('code' => 200, 'error' => '', 'list' => $backup_list)));

} else {

	// list is empty - no files, no error
	die(json_encode(array('code' => 200, 'error' => '', 'list' => '')));
}

/**
 *	Simple function to get human readable filesize
 *
 */
function human_filesize($size, $decimals = 2) {

	if($size < 1024)
		return number_format($size, 0, ',', '.').' Bytes';
	else if($size >= 1024 && $size < 1024*1024)
		return number_format($size/1024.0, 2, ',', '.').' KB';
	else
		return number_format($size/(1024.0*1024), 2, ',', '.').' MB';
}

/**
 *	Check if backup has been completed
 *	Delete broken (incomplete) backups
 *
 */
function incompleteBackup($logfile, $type) {
	$content = trim(file_get_contents($logfile, FALSE, NULL, (filesize($logfile) - 30)));
	if (strpos($content,'finished successfully') === false) {

		if ($type !== 'r') {
			// Get the temporary, incomplete zip file
			// It has an additional suffix eg. filename.zip.x8m3x4
			$zipfile = str_replace('.log', '.zip', $logfile);
			$search = '#'.basename($zipfile).'.*#';
			$dir_path = dirname($logfile);
			$temp_zipfile = scan_current_dir($dir_path, $search);

			// Delete the sql and the eventually the temporary zip file
			if (!empty($temp_zipfile['filename'])) {
				unlink($dir_path.DIRECTORY_SEPARATOR.$temp_zipfile['filename'][0]);
				$sqlfile = str_replace('.log', '.sql', $logfile);
				unlink($sqlfile);
			}
		}
		return false;
	}
	return true;
}
