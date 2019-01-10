FROM nginx:1.15.1-alpine

USER root
WORKDIR /apidist
COPY ./api/public /apidist/public

RUN ln -sf /apidist/storage/app/public /apidist/public/storage