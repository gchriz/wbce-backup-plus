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
 *
 */

// Module Description
$module_description = 'This module allows you to backup your files and databases.';

// Text outputs
$MOD_BACKUP['BACKUP_HEADER'] 	  		= 'BACKUP';
$MOD_BACKUP['BACKUP_COMPLETE'] 	  		= 'Complete backup - all files and the complete database<br>Access rights for all databases necessary!';
$MOD_BACKUP['BACKUP_WBCE'] 	  			= 'CMS Backup - Only CMS files and the CMS database';
$MOD_BACKUP['BACKUP_PAGES'] 	  		= 'Backup all created pages';
$MOD_BACKUP['BACKUP_INFO']				= 'The backup of all directories and files may take a while. It depends on the number of modules and templates installed and the size of media files.'
                                        . '<br>Only files up to max. %s MB will be included in the backup.<br>Files starting with a dot are NOT saved!';
$MOD_BACKUP['BACKUP_START'] 	  		= 'Start selected backup';

$MOD_BACKUP['BACKUP_LIST_TITLE']		= 'History and functions';
$MOD_BACKUP['BACKUP_LIST_HEADER'] 		= '<th>Date</th><th class="l">Size</th><th class="l">Type</th><th class="r">Functions</th>';
$MOD_BACKUP['BACKUP_LIST_FULL'] 		= 'Full backup';
$MOD_BACKUP['BACKUP_LIST_WBCE'] 		= 'CMS backup';
$MOD_BACKUP['BACKUP_LIST_PAGE'] 		= 'Pages backup';
$MOD_BACKUP['BACKUP_LIST_RECO'] 		= 'Recovery';

$MOD_BACKUP['BACKUP_WAIT']				= '<span id="waiting">Please wait <i class="fa fa fa-circle-o-notch fa-spin"></span>';
$MOD_BACKUP['BACKUP_DONE']				= 'Backup successful!';
$MOD_BACKUP['BACKUP_LOGFILE']			= 'Backup log';
$MOD_BACKUP['BACKUP_ZIP_DOWNLOAD']		= 'Download zipfile';
$MOD_BACKUP['BACKUP_SQL_DOWNLOAD']		= 'Download SQL export file';
/*$MOD_BACKUP['BACKUP_ALL_PAGES_INFO']	= 'All pages created by users are saved.<br>The size of the backup can be max. %s MB.<br><br>No other files or database tables are included!';*/

$MOD_BACKUP['BACKUP_DELETE']			= 'Delete this entry';
$MOD_BACKUP['BACKUP_SURE']				= 'Are you sure?';
$MOD_BACKUP['BACKUP_RESTORED']			= 'Recovery completed successfully.';
$MOD_BACKUP['BACKUP_RESTORE_WARNING']	= '<br><br>When the data is restored, the existing files and database tables are deleted and the selected data is restored.';
$MOD_BACKUP['BACKUP_WARNING1']			= '<br><br>During this function <b>never</b> use the application, otherwise inconsistencies may arise!';
$MOD_BACKUP['BACKUP_WARNING2']			= '<br><br>When the function is finished, the main menu is started. You may need to login again.';

$MOD_BACKUP['BACKUP_ZIP_ERROR']			= 'Zip-Error, Status: %s';
$MOD_BACKUP['BACKUP_LOG_ERROR']			= 'Error creating logfile!';
$MOD_BACKUP['BACKUP_CREATE_DIR_ERROR']	= 'Error creating directory "%s"!';
$MOD_BACKUP['BACKUP_CREATE_SQL_ERROR']	= 'Error creating SQL exportfile!';
$MOD_BACKUP['BACKUP_DELETE_ZIP_ERROR']	= 'Error deleting zipfile!';
$MOD_BACKUP['BACKUP_DELETE_LOG_ERROR']	= 'Error deleting logfile!';
$MOD_BACKUP['BACKUP_DELETE_SQL_ERROR']	= 'Error deleting SQL exportfile!';
$MOD_BACKUP['BACKUP_DELETE_PAGE_ERROR']	= 'Error deleting the page directory "%s"!';
$MOD_BACKUP['BACKUP_READ_SQL_ERROR']	= 'Error recovering the database!<br>Could not read the SQL exportfile!';
$MOD_BACKUP['BACKUP_PARAMETER_ERROR']	= 'Parameter "%s" missing in file "backup_settings.php"!';
