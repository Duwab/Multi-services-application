# Smart general README
Pour de vrai

alias sfcache='sudo chmod -R 774 var/cache/ var/logs/ var/sessions/ && sudo chown -R ubuntu:www-data var/cache/ var/logs/ var/sessions/'


php bin/console debug:route
cache, logs, sessions sont en 774 ubuntu:www-data
pour réajuster ces droits: sfcache


composer install
npm install
app/config/parameters.yml
apache configuration
php bin/console doctrine:schema:update --force


jwt private duwab


user core pour que ça marche
FosuserBundle (user provider + security main)
LexikJWT (security /jwt)
FosRestBundle (manage REST, parse JSON body, etc)
NelmioCors (cors pour /jwt uniquement)