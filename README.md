# timelines-api

## Setup

### ealasticsearch:

#### Linux
```bash
sudo sysctl -w vm.max_map_count=262144
```
### API

```bash
php artisan passport:keys
php artisan passport:client --password --name=WebPasswordGrantClient

ln -s storage/app/public public/storage
```
