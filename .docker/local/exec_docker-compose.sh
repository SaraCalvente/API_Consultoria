#!/bin/sh
cd "$(dirname "$0")/../../" || return

docker image rm -f base-image
docker build . --file .docker/common/Dockerfile --tag base-image
chmod +x .docker/local/setup.sh
docker-compose -f docker-compose-local.yaml --project-name demo-project up --build