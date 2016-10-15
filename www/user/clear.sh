php bin/console cache:clear && php bin/console cache:clear --env=prod&& php bin/console assets:install web && php bin/console assetic:dump && chown -R www-data:www-data var/logs/ var/cache/
