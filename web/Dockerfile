FROM node:10.6.0-alpine
RUN npm install -g @angular/cli

WORKDIR /timeline/web
COPY . /timeline/web
RUN npm install && rm -f /webdist/* && ng build --aot --prod --output-path=/webdist
