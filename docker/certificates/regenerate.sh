#!/usr/bin/env bash

openssl genrsa -out app-dev.key 4096
openssl req -new -x509 -key app-dev.key -sha256 -out app-dev.pem -days 3650 -config openssl.cnf
cp /etc/ssl/certs/ca-certificates.crt bundle.crt
cat app-dev.pem >> bundle.crt
