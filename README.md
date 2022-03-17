# Backup Plus

## Backup module for the CMS [WBCE - Way Better Content Editing](https://www.wbce.org)

Author: mastermind and others

Rework of the backup module with faster, more reliable file creation,
option to save the pages only and new option to restore backups on the fly.

Configuration can be modified by editing the backup_settings.php file via FTP / AFE.

### Some official links

* [WBCE CMS Add-On Repository (AOR)](https://addons.wbce.org)
* [Backup Plus in the AOR](https://addons.wbce.org/pages/addons.php?do=item&item=159)
* [Discussion forum for Backup Plus](https://forum.wbce.org/viewtopic.php?id=4374)


### and some unofficial

Links to *Backup Plus* in gchriz' Github repository:

* [main entry](https://github.com/gchriz/wbce-backup-plus)
* [latest release version (overview page with download links)](https://github.com/gchriz/wbce-backup-plus/releases/latest)
* [current development version (.zip file)](https://github.com/gchriz/wbce-backup-plus/archive/refs/heads/main.zip)

---

**Please note:** Currently the zip downloads from Github are unfortunately
not directly installable in *WBCE* because they contain an additional root directory:

e.g. the file *wbce-backup-plus-2.8.0.zip*
contains this directory structure

```text
wbce-backup-plus-2.8.0/
    css/
    js/
    languages/
    backup_settings.default.php
    backup.php
    ...
```

So you need to unzip it first and re-zip the inner contents (css/, js/ etc.)
into a fresh zip file so it looks like:

```text
css/
js/
languages/
backup_settings.default.php
backup.php
...
```

I'll try to find a better solution...
