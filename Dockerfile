FROM php:apache

RUN apt-get update && \
    apt-get install net-tools

COPY . .