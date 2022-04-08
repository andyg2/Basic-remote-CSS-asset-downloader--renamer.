FROM php:7.4-cli
WORKDIR /usr/src/css_asset_dl
COPY parse.php /usr/src/css_asset_dl
CMD [ "php", "/usr/src/css_asset_dl/parse.php" ]