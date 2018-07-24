FROM node:10.6.0-alpine as build-stage
RUN npm install -g @angular/cli

WORKDIR /timeline/web
COPY ./ /timeline/web
RUN npm install && ng build --aot --prod

FROM nginx:1.15.1-alpine
COPY --from=build-stage /timeline/web/dist/web /usr/share/nginx/html
COPY --from=build-stage /timeline/web/nginx/conf.d/nginx.prod.conf /etc/nginx/conf.d/default.conf

WORKDIR /usr/share/nginx/html
