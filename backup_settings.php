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

// No direct access
if (!defined('WB_PATH')) die(header('Location: ../../index.php'));

/**
 *	Directory where data is stored
 *
 */

define('BACKUP_DATA_DIR', DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR);


/**
 -------------------------------------------------------------------------------
 *
 *  THE CONFIGURATION SECTION
 *
 -------------------------------------------------------------------------------
**/

// Prevent server or network timeout while zipping the backup
// by excluding directories and / or large files.

// Exclude large files, max file size in megabytes
$max_file_size = 60;

// Log excluded large files
$log_excluded_large_files = true;

// Log excluded files with ignored extension
$log_excluded_extensions = true;


// -------------------- Includes for backup type PAGE ---------------------------
// Specify directories you WANT in the backup as an array for backup type "page"
$includeDirs = array('pages');

// Specify database tables you WANT in the SQL export as an array for backup type "page"
$exportTables = array('mod_wysiwyg','mod_menu_link','mod_sitemap','pages','sections');


// -------------------- Excludes for backup type WBCE ---------------------------
// Specify directories you do NOT want in the backup as an array for backup type "wbce"
$ignoreWbceDirs = array('backups', 'owncloud','nextcloud','logs','log','cgi-bin');


// -------------------- Excludes for backup type FULL ----------------------------
// Specify directories you do NOT want in the backup as an array for backup type "full"
$ignoreFullDirs = array('backups', 'owncloud','nextcloud','logs','log','cgi-bin');


// Specify file EXTENSIONS you do NOT want in the backup as an array
$ignoreExts = array();
