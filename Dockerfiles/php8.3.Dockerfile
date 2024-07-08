#!/bin/bash

FROM scratch

ADD alpine.tar.gz /

RUN echo "https://uk.alpinelinux.org/alpine/v3.20/main" > /etc/apk/repositories
RUN echo "https://uk.alpinelinux.org/alpine/v3.20/community" >> /etc/apk/repositories

RUN apk add php83 \
            php83-common

RUN php -v && mkdir /work

WORKDIR "/work"

CMD ["php"]
