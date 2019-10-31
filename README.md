# Timeline
Display a list of events in chronological order.

## Features:
 - View historical events in chronological order
 - Search events using key words
 - Filter events using different criteria
 - Create a event
 - Update a event
 - Delete a event
 - User management
 - Backup management
 - Configure catalogs
 - Configure periods
 - Configure date attributes
 - Bulk import events from a excel file and/or multiple image files
 
## Development Environment Setup
The following tools must be installed:
 - A [kubernetes](https://kubernetes.io/docs/setup/) cluster (e.g. minikube, Docker Machine, etc.)
   - make sure [ingress-nginx](https://kubernetes.github.io/ingress-nginx/deploy/) is install
 - [Skaffold](https://skaffold.dev/docs/install/): sync code into kubernetes pods

And then, simply run `skaffold dev` (depending on your choice of kubernetes cluster, it may take forever to build the dev image ...)

To setup database and search engine, run the following commands inside the `api` pod:
```bash
php artisan migrate
php artisan elasticsearch:fresh
``` 

To create the first super user, run `php artisan make:user` inside the `api` pod.

## Production Deployment
There are tons of ways to deploy this application. Adjust any configurations as needed.

### Using Kubernetes
Make a copy of `k8s` folder and exclude any file in the `k8s/development` folder.

#### Database, Queue & Search Engine
|Type          |Options       |
|:------------:|:------------:|
|Database      |MySQL         |  
|Queue         |Redis         |
|Search Engine |ElasticSearch |  

To configure database credential, create a file called `db-secret.yaml`:
```text
apiVersion: v1
kind: Secret
metadata:
  name: db-secret
data:
  database: <database_name_with_base64_encoded>
  username: <db_username_with_base64_encoded>
  password: <db_password_with_base64_encoded>
```

> **Note:**  
> If you are going to use an external pre-provisioned MySQL, Redis or ElasticSearch, remove files matching `db-*.yaml`, `queue-*.yaml` or `search-engine-*.yaml`, respectively.

#### API Config & Secret
To configure api (no confidential data), create a file called `api-cm.yaml` (change those attributes as needed):
```text
apiVersion: v1
kind: ConfigMap
metadata:
  name: api-cm
data:
  APP_NAME: Timeline
  APP_ENV: production
  APP_DEBUG: "false"
  APP_URL: https://api.timeline.test
  APP_TIMEZONE: America/New_York
  APP_BACKUP: "true"
  LOG_CHANNEL: stack
  DB_CONNECTION: mysql
  DB_HOST: db-cluster-ip-service
  DB_PORT: "3306"
  DEFAULT_DATE_ATTRIBUTE: Year
  ELASTICSEARCH_HOST: search-engine-cluster-ip-service
  ELASTICSEARCH_PORT: "9200"
  ELASTICSEARCH_SCHEME: http
  CACHE_DRIVER: redis
  SESSION_DRIVER: redis
  SESSION_CONNECTION: session
  REDIS_HOST: queue-cluster-ip-service
  QUEUE_CONNECTION: redis
```

To configure secrets, create a file called `api-secret.yaml`:
```text
apiVersion: v1
kind: Secret
metadata:
  name: api-secret
data:
  # Generate key and base64 encoded: 
  # php artisan key:generate --show | base64
  APP_KEY: <app_key_with_base64_encoded>

  # Generate secret and base64 encoded: 
  # php artisan jwt:secret --show | base64
  JWT_SECRET: <jwt_secret_with_base64_encoded>
```

#### Ingress Service
The ingress service is the true entry point for the client to access this application. To define an ingress service, create a file called `ingress-service.yaml` with similar content from the sample [ingress-service.yaml](k8s/development/ingress-service.yaml) file.

#### One Last Step
Once all kubernetes configuration files are ready, apply all those files in to the cluster.
```bash
kubectl apply -f <folder_contains_yaml_files> -n <namespace>
```

### Using Docker-Compose
TBD

## License
[Apache 2.0](LICENSE)