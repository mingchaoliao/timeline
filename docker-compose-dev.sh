#!/bin/bash

DOCKER_COMPOSE="$(which docker-compose)"
HOST_UID="$(id -u)"
HOST_GID="$(id -g)"
ARGS="${@:1:999}"

HOST_UID=${HOST_UID} HOST_GID=${HOST_GID} ${DOCKER_COMPOSE} ${ARGS}
