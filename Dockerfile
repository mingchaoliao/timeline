FROM node:10.6.0-alpine as build-web
RUN npm install -g @angular/cli

WORKDIR /web
COPY web /web
RUN mkdir -p /webdist
RUN npm install --production && rm -f /webdist/* && ng build --aot --prod --output-path=/webdist

FROM nginx:1.15.1-alpine
COPY --from=build-web /webdist /webdist
COPY api/public /apidist/public
RUN ln -sf /apidist/storage/app/public /apidist/public/storage