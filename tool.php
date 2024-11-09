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
 *	Load module language file
 *
 */
$lang = __DIR__.'/languages/'.LANGUAGE.'.php';
require __DIR__.'/languages/EN.php'; // default
if (file_exists($lang)) {			 // override with user language
	require $lang;
}

/**
 *	Include module backup_settings.php
 *
 */
require_once('backup_settings.php');

/**
 *	Check if all necessary parameter exists
 *
 */

if (!defined('BACKUP_DATA_DIR'))	stop( 'BACKUP_DATA_DIR' );
if (empty($max_file_size))			stop( 'max_file_size' );
if (empty($includeDirs))			stop( 'includeDirs' );
if (empty($exportTables))			stop( 'exportTables' );
if (empty($ignoreWbceDirs))			stop( 'ignoreWbceDirs' );
if (empty($ignoreFullDirs))			stop( 'ignoreFullDirs' );

if (empty($log_excluded_large_files)) {
	$log_excluded_large_files = true;
}

if (empty($log_excluded_extensions)) {
	$log_excluded_extensions = true;
}

if (empty($ignoreExts))	{
	$ignoreExts = array();
}

function stop($msg) {
	global $MOD_BACKUP;
	die(sprintf( $MOD_BACKUP['BACKUP_PARAMETER_ERROR'], $msg ));
}

// Make directory for the backups if not yet existing
$dir_path = WB_PATH.BACKUP_DATA_DIR;
make_dir($dir_path);

/**
 *	Show form
 *
 */
?>

<link href="<?php echo WB_URL . '/modules/backup_plus/css/tool.css' ?>" rel="stylesheet" type="text/css" media="screen" />

<div id="backup_plus_main">
	<div id="left">
		<h2><b><?php echo $MOD_BACKUP['BACKUP_HEADER'] ?></b></h2>
		<form name="prompt" method="post">
			<div>
				<input type="radio" name="backup" value="page" checked="checked"> <?php echo $MOD_BACKUP['BACKUP_PAGES'] ?><br>
<?php if ($wb->ami_group_member('1')) { ?>
				<input type="radio" name="backup" value="wbce"> <?php echo $MOD_BACKUP['BACKUP_WBCE'] ?><br>
				<input type="radio" name="backup" value="full"> <?php echo $MOD_BACKUP['BACKUP_COMPLETE'] ?><br>
<?php } ?>
				<br>
			</div>
			<div><?php printf($MOD_BACKUP['BACKUP_INFO'], $max_file_size); ?></div><br>
			<input type="button" id="backup" value="<?php echo $MOD_BACKUP['BACKUP_START'] ?>">
			<br>
		</form>
		<br style="clear:both">
<?php
	// called from restore.php with params?
	if (isset($_GET['result'])) {
		if (empty($_GET['error'])) {
?>
		<div class="alert alert-success" style="display:inherit"><?php echo $_GET['result'] ?></div>
		<div class="alert alert-fail">&nbsp;</div>
<?php 	} else { ?>
		<div class="alert alert-success">&nbsp;</div>
		<div class="alert alert-fail" style="display:inherit"><?php echo $_GET['error'] ?></div>
<?php   }
	} else {
?>
		<div class="alert alert-success">&nbsp;</div>
		<div class="alert alert-fail">&nbsp;</div>
<?php
	}
?>
		<br><br>
	</div>

	<div id="right">
		<div id="backupList"></div>
	</div>
</div>

<script src="<?php echo WB_URL . '/modules/backup_plus/js/alerty.min.js' ?>" type="text/javascript"></script>

<script type="text/javascript">
//<![CDATA[
$(function() {
	'use strict';

	// show of the backup file list first
	getBackupfiles();

	// all backup functions
	$( document ).on( "click", "#backup", function(e) {
		e.preventDefault();

		var type = $('input[name=backup]:checked').val();
		var txtbku = '<?php echo ($MESSAGE['GENERIC_PLEASE_BE_PATIENT'] . $MOD_BACKUP['BACKUP_WARNING1']) ?>';
		confirm( '<?php echo $MOD_BACKUP['BACKUP_START'] ?>', txtbku, backupFunction, type );
	});

	function backupFunction( type ){
		var myURL = '<?php echo WB_URL ?>' + '/modules/backup_plus/backup.php';
		var _request = $.ajax({
			type:		'GET',
			url:		myURL,
			dataType:	'json',
			data: {
				backup: "yes",
				type:	type
			}
		});
		_request.done(function(msg) {
			checkDone(msg, myURL);
		});
		_request.fail(function(xhr, ajaxOptions, thrownError) {
			showError(xhr.status, thrownError, myURL);
		});
	};

	// delete function
	$( document ).on( "click", ".delete", function(e) {
		e.preventDefault();
		confirm( '<?php echo $MOD_BACKUP['BACKUP_DELETE'] ?>', '<?php echo $MOD_BACKUP['BACKUP_SURE'] ?>', deleteFunction, $(this).attr('data-file') );
	});

	function deleteFunction( file ) {
		var myURL   = '<?php echo WB_URL ?>' + '/modules/backup_plus/delete.php';
		var _request = $.ajax({
			type: 		'GET',
			url: 		myURL,
			dataType:	'json',
			data: {
				file: 	file
			}
		});
		_request.done(function(msg) {
			checkDone(msg, myURL);
		});
		_request.fail(function(xhr, ajaxOptions, thrownError) {
			showError(xhr.status, thrownError, myURL);
		});
	};

	// restore backup to original position from zipfile
	$( document ).on( "click", ".restore", function(e) {
		e.preventDefault();
		var txtres = '<?php echo ($MOD_BACKUP['BACKUP_SURE'] . $MOD_BACKUP['BACKUP_RESTORE_WARNING'] . $MOD_BACKUP['BACKUP_WARNING1'] . $MOD_BACKUP['BACKUP_WARNING2']) ?>';
		confirm( '<?php echo $MOD_BACKUP['BACKUP_LIST_RECO'] ?>', txtres, restoreFunction, $(this).attr('data-file') );
	});

	function restoreFunction( file ) {
		window.location.href = '<?php echo WB_URL ?>' + '/modules/backup_plus/restore.php?file=' + file;
		return true;
	};

	// show logfile
	$( document ).on( "click", ".showlog", function(e) {
		e.preventDefault();
		popup($(this).attr('data-file'));
	});


	// ajax show the list of the backup files
	function getBackupfiles() {

		var myURL   = '<?php echo WB_URL ?>' + '/modules/backup_plus/list.php';

		var _request = $.ajax({
			type: 		'GET',
			url: 		myURL,
			dataType:	'json',
			data: {
				dir: 	'backups'
			}
		});
		_request.done(function(msg) {
			if (typeof msg === 'object' && msg !== null) {
				if (msg.code === 200) {
					$('#backupList').html(msg.list);
				} else {
					showError(msg.code, msg.error, myURL);
				}
			} else {
				showError(403, 'PHP error?', myURL);
			}
		});
		_request.fail(function(xhr, ajaxOptions, thrownError) {
			showError(xhr.status, thrownError, myURL);
		});
	}

	function checkDone(msg,url) {
		if (typeof msg === 'object' && msg !== null) {
			if (msg.code === 200) {
				showSuccess(msg.message);
			} else {
				showError(msg.code, msg.error, url);
			}
		} else {
			showError(403, 'PHP error?', url);
		}
	}

	function showWaiting() {
		$('#backup').hide();
		$('div.alert.alert-fail').text('').hide();
		$('div.alert.alert-success').html('<?php echo $MOD_BACKUP['BACKUP_WAIT'] ?>').show();
	}

	function showSuccess( message ) {
		$('#backup').show();
		$('div.alert.alert-fail').text('').hide();
		$('div.alert.alert-success').html(message);
		getBackupfiles();

	}

	function showError( status, error, url ) {
		$('#backup').show();
		$('div.alert.alert-success').text('').hide();
		$('div.alert.alert-fail').html('Status: ' + status + '<br>' + error + '<br>URL: ' + url ).show();
		getBackupfiles();
	}

	function confirm( tit, body, func, par ) {
		alerty.confirm( body, {title: tit, cancelLabel: '<?php echo $TEXT['CANCEL'] ?>', okLabel: 'OK'},
			function(){
				$('#backupList').html('');
				showWaiting();
				func(par);
			},
			function(){return false}
		)
	}

	// show a popup window
	function popup(url,w,h) {

		if(url==null||url=="") return;

		var w = (w) ? w : $(window).width() - 50;
		var h = (h) ? h : $(window).height() - 100;
		var l = $(window.parent).width() - w;

		var pop = window.open( url, 'popup', 'width='+w+',height='+h+',left='+l+',top=80,resizable=yes,scrollbars=yes,status=no,location=no');
		if (pop) pop.focus();
	}
});

//]]>
</script>
