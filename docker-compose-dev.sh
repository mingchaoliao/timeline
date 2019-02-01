#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
DOCKER_COMPOSE="$(which docker-compose)"
HOST_UID="$(id -u)"
HOST_GID="$(id -g)"
ARGS="${@:1:999}"
CONFIG_FILE="${DIR}/docker-compose.dev.yml"

HOST_UID=${HOST_UID} HOST_GID=${HOST_GID} ${DOCKER_COMPOSE} -f ${CONFIG_FILE} ${ARGS}
