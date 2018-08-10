#
# This automatically creates a fresh build for distribution with other modules or for getting started with Mollie
# without composer.
#
mollie-api-php.zip: php-scoper.phar
	rm -rf build/*

	#
	# First, install all dependencies. Then prefix everything with humbug/php-scoper. Finally, we should dump the
	# autoloader again to update the autoloader with the new classnames.
	#
	composer install --no-dev --no-scripts --no-suggest
	php php-scoper.phar add-prefix --force
	composer dump-autoload --working-dir build --classmap-authoritative

	#
	# Now move the autoload files. We have to use the scoper one to load the aliasses but we want to load the normal
	# filename. Flip them around.
	#
	mv build/vendor/autoload.php build/vendor/composer-autoload.php
	sed -i 's/autoload.php/composer-autoload.php/g' build/vendor/scoper-autoload.php
	mv build/vendor/scoper-autoload.php build/vendor/autoload.php

	#
	# Finally, create a zip file with all built files.
	#
	cd build; zip -r ../mollie-api-php.zip examples src vendor composer.json LICENSE README.md

php-scoper.phar:
	wget -q https://github.com/humbug/php-scoper/releases/download/0.9.2/php-scoper.phar
	wget -q https://github.com/humbug/php-scoper/releases/download/0.9.2/php-scoper.phar.pubkey