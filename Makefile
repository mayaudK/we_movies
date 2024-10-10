go_php:
	docker exec -it php bash

launch_server:
	symfony serve -d
	
install_yarn:
	yarn install

install_composer:
	composer install

start_server:
	symfony serve -d

compile_assets:
	yarn encore dev

compile_assets_live:
	yarn encore dev --watch

stop_server:
	symfony server:stop

install_dependencies:
	make install_composer
	make install_yarn

run:
	make install_dependencies
	make compile_assets
	#make start_server
	
	