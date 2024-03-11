# Backup Plus

## Backup module for the CMS [WBCE - Way Better Content Editing](https://www.wbce.org)

Author: mastermind and others

Rework of the backup module with faster, more reliable file creation,
option to save the pages only and new option to restore backups on the fly.

Configuration can be modified by editing the *backup_settings.php* file via FTP / AFE.

There are three types of backup available

  * Pages - All created pages
  * CMS - Files and the database tables of the current CMS
  * Complete - All files (DOCUMENT_ROOT) and the complete database
    (Access rights for all DB tables necessary!)

During a restore of the backup types "Pages" and "CMS" all occurences of WB_URL
in the data will be automatically changed to the current CMS instance.

The backup/restore of all directories and files may take a while.
It depends on the number of modules and templates installed and the size of media files.
So a it might exceed the server's max_execution_time and produce a broken/incomplete backup.

Only files up to max. 60 MB will be included in the backup.
Some directories won't be included, have a look at *backup_settings.php*.
Empty directories won't be included. Links to directories won't be followed.


### Some "official" links

* [WBCE CMS Add-On Repository (AOR)](https://addons.wbce.org)
* [Backup Plus in the AOR](https://addons.wbce.org/pages/addons.php?do=item&item=159)
* [Discussion forum for Backup Plus](https://forum.wbce.org/viewtopic.php?id=4374)

-----

### Some internal links in this (gchriz') Github repository right here

* [main entry page](https://github.com/gchriz/wbce-backup-plus)
* [mirror of latest release version](https://github.com/gchriz/wbce-backup-plus/releases/latest) (overview page with download links)
* [current main/head/development version (.zip file)](https://github.com/gchriz/wbce-backup-plus/archive/refs/heads/main.zip), most of the time identical to the release version
* [active branch(es) with ongoing work](https://github.com/gchriz/wbce-backup-plus/branches), to be included into development version soon
