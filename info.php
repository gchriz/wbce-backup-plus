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
*/

$module_directory = 'backup_plus';
$module_name = 'Backup Plus';
$module_function = 'tool';
$module_version = '2.8.2';
$module_platform = '1.5.x';
$module_author = 'misc., Ruud, webbird, freesbee, mastermind, chriz';
$module_license = 'GNU General Public License';
$module_description = 'This module allows you to backup your database and your files.';
$module_icon = 'fa fa-download';

/**
 * 
 * Update history
 * 
 * 2.6.0    bug on linux: test if source has backslash on end
 * 
 * 2.7.0    create "pages" dir if not exist
 *          replace windows dir separator by "/" for zip, so backup from windows can be restored on linux
 *          include DB Version and OS in sql export
 *          bug: do not include backup_settings.php into zip file for release
 *
 * 2.7.2    make sure to exclude full directory name only and not parts of it as well and fix some typos 
 * 
 * 2.8.0    bug corrected: make sure to exclude full directory name only and not parts of it as well!
 *          bug corrected: missing wb_path for creating and cleaning pages directory
 *          bug corrected: skip file if not readable
 *          bug corrected: on incomplete backup > test if sql file exists before trying to delete
 *          write number of files saved in logfile
 *          write excluded directories in logfile
 *          skip files with not allowed extension (i.e. links)
 *          some text in english version corrected
 *          info for full backup: must have access rights
 *          better button styling
 *          delete text improved
 * 
 * 2.8.1    (by florian) PHP 8.1 deprecated fix 
 *			small fixes to DE.php
 *
 * 2.8.2    (by chriz) bugfix: restore now detects and shows possible SQL and unzip errors
 *          add some technical details as background information to DE.php, EN.php and to restore logs
 *          turn off foreign key check in SQL file during restore
 *          create backups directory on install and every call of Backup Plus to have it existing for sure
 *
 */

