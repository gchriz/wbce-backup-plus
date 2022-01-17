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
 *	Include global config and modul config
 *
 */
require_once('../../config.php');
require_once('backup_settings.php');

/**
 *	Create new admin object
 *
 */
require_once('../../framework/class.admin.php');
$admin = new admin('Admintools', 'admintools', false, false);

if ($admin->get_permission('admintools') == false) {
	die(header('Location: ../../index.php'));
}

/**
 *	Check caller
 *
 */
$caller = $_SERVER['HTTP_REFERER'];
$test = ADMIN_URL.'/admintools/tool.php';
if ($caller != $test) {
	die(header('Location: ../../index.php'));
}

/**
 *	Load module language file
 *
 */
$lang = __DIR__.'/languages/'.LANGUAGE.'.php';
require __DIR__.'/languages/EN.php'; // default
if (file_exists($lang)) {			 // override with user language
	require $lang;
}


/**
 * Class to create prefix for the files
 */
class BKU_FilePrefix
{
	private $pfx;

	public function __construct($dir_path,$type)
	{
		// Backup file name starts with domain name ...
		$host = str_replace(':','_',$_SERVER['HTTP_HOST']).'_';

		// ... then we add date string and the timestamp
		$timestr = gmdate('Y-m-d', time() + TIMEZONE).'-'.time();

		// ... then add the first character of backup type
		$timestr .= '-'.substr($type,0,1);

		// ... and finally we add a text token to minimize the propability of someone guessing the file name
		$tt = new RandomGen;
		$token = '-'.$tt->TextToken(6);

		$this->pfx = $dir_path.$host.$timestr.$token;
	}

	/**
	 * Write a string into logfile and ignore errors
	 * @return string prefix
	 */
	public function get()
	{
		return $this->pfx;
	}

}

/**
 * Class to write the logfile
 */
class BKU_Log
{
	private $fh;

	public function __construct($filesPrefix)
	{
		$this->fh = @fopen($filesPrefix.'.log', 'w');
		if ($this->fh == false) 	die(json_encode(array('code' => 4031, 'error' => $MOD_BACKUP['BACKUP_LOG_ERROR'])));
	}

	/**
	 * Write a string into logfile and ignore errors
	 * @param string $strg
	 */
	public function write($strg)
	{
		$time = gmdate(DEFAULT_DATE_FORMAT.'-'.DEFAULT_TIME_FORMAT.':s', time() + DEFAULT_TIMEZONE);
		fwrite($this->fh, $time. ' ' . $strg . PHP_EOL);
	}

	/**
	 * Close logfile
	 */
	public function close()
	{
		fclose($this->fh);
	}
}
