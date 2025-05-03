start-app:
	clear
	php -S localhost:8000 -t ./app

start-daemon:
	clear
	php ./daemon.php $(QUEUE)