# Variables
DC=docker compose --file docker-compose.yml --env-file .env
DC_TEST=docker compose --file docker-compose.yml --env-file ./src/.env.testing

.PHONY: go up setup down sh test paratest test-report logs db-migrate db-migrate-test db-seed db-rollback db-reset horizon log clear

%:
	@:

go:
	make down
	make up
	sleep 10
	make setup
	make db-migrate
	make vite

go-test:
	make down
	docker volume rm -f msg-crafter-db-test-volume
	make up
	sleep 10
	make setup
	make db-migrate-test

up:
	$(DC) up -d --build

setup:
	$(DC) exec msg-crafter composer install
	$(DC) exec msg-crafter-nodejs npm install

down:
	$(DC) down

sh:
	$(DC) exec msg-crafter sh

node-sh:
	$(DC) exec msg-crafter-nodejs sh

test:
	docker exec -it msg-crafter php artisan test $(wordlist 2, $(words $(MAKECMDGOALS)), $(MAKECMDGOALS)) --coverage

paratest:
	$(DC) exec msg-crafter php artisan test --coverage --parallel --processes=10 $(wordlist 2, $(words $(MAKECMDGOALS)), $(MAKECMDGOALS))

test-coverage:
	$(DC) exec msg-crafter php artisan test --coverage-html=coverage $(wordlist 2, $(words $(MAKECMDGOALS)), $(MAKECMDGOALS))

db-migrate:
	$(DC) exec msg-crafter php artisan migrate

db-migrate-test:
	$(DC_TEST) exec msg-crafter php artisan migrate --env=testing

db-seed:
	$(DC) exec msg-crafter php artisan db:seed

db-rollback:
	$(DC) exec msg-crafter php artisan migrate:rollback

db-reset:
	make db-rollback
	make db-migrate
	make db-seed

horizon:
	$(DC) exec msg-crafter php artisan horizon

clear:
	$(DC) exec msg-crafter php artisan cache:clear
	$(DC) exec msg-crafter php artisan view:clear
	$(DC) exec msg-crafter php artisan route:clear
	$(DC) exec msg-crafter php artisan config:clear
	$(DC) exec msg-crafter php artisan optimize:clear

vite:
	$(DC) exec msg-crafter-nodejs npm run dev

logs:
	$(DC) logs -f -n 10

log:
	$(DC) exec msg-crafter tail -f storage/logs/laravel.log -n 0
