#Start the local server.
start:
	symfony server:start

#Update the status of expired offers.
update-offer:
	php bin/console app:update-expired-offers

#Running Cs-fixer to fix the code (check only src folder).
cs-fixer:
	vendor/bin/php-cs-fixer fix src

#Running Phpstan to check the code (check only src folder).
stan:
	vendor/bin/phpstan analyse src

#Clear the cache.
clear:
	php bin/console cache:clear