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
 *	Direct access prevention
 *
 */
defined('WB_PATH') OR die(header('Location: ../../index.php'));

/**
 *
 *  Copy backup_settings.default.php to backup_settings.php if not exists
 *
 */
$config_file = WB_PATH.'/modules/backup_plus/backup_settings.php';
$config_default_file = WB_PATH.'/modules/backup_plus/backup_settings.default.php';
if (!file_exists($config_file)) {
	copy($config_default_file, $config_file);
}


