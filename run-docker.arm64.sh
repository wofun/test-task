#!/bin/bash

docker compose -f docker-compose.yml -f docker-compose.arm64.yml up -d
