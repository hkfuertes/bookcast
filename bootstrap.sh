#!/bin/bash

 docker run --rm --interactive --tty --volume $PWD:/app docker pull php:7.3.32-apache bash -c "cd /app && bash"