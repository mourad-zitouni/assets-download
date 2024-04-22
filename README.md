# assets-download
# Install the project:
1. clone the repository.
2. cd assets-download
3. ddev start
4. ddev composer install
5. Import database:
    ddev import-db < db-backup/dump.sql
6. Enable and set config directory on web/sites/default/settings.php
    $settings['config_sync_directory'] = '../config/sync';
8. Clear drupal cache:
    ddev drush cr
9. Go to https://assets-download.ddev.site
10. User login: admin/admin
