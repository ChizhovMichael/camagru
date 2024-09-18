stop:
	docker ps -aq | xargs docker stop

rm:
	docker ps -aq | xargs docker rm

down:
	docker-compose down

rebuild:
	docker-compose down && \
	docker-compose build && \
	docker-compose up -d

ips:
	docker ps -aq | xargs docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}'

start:
	docker-compose up -d && \
	sleep 20 && \
	docker-compose exec www php bin/migration up && \
	docker-compose exec www php bin/fixture up


migration:
	docker-compose exec www php bin/migration up && \
	docker-compose exec www php bin/fixture up


