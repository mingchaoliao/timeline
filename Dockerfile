FROM node:10.6.0-alpine as web-build-stage
RUN npm install -g @angular/cli

WORKDIR /timeline/web
COPY ./web /timeline/web
RUN mkdir -p /webdist
RUN npm install && rm -f /webdist/* && ng build --aot --prod --output-path=/webdist

FROM nginx:1.15.1-alpine

USER root
WORKDIR /apidist
COPY ./api/public /apidist/public
COPY ./etc/production/nginx/conf.d /etc/nginx/conf.d
COPY ./etc/production/nginx/ssl /etc/nginx/ssl
COPY --from=web-build-stage /webdist /webdist

RUN ln -sf /apidist/storage/app/public /apidist/public/storage