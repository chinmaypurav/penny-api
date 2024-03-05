## About Penny

Penny is aimed to be a simple yet powerful expense management system to handle complex transactions.

## Setting up the project locally

The project can be set up locally using [Docker](https://www.docker.com/) with [Docker Compose](https://docs.docker.com/compose/) plugin.

Ensure your environment variable values are set before you proceed to spin up docker.

Run the following command to spin up the app on docker:

```bash
docker compose up -d
```

To ssh into the php container:

```bash
docker exec -it penny-php bash
```

This will spin up the following services:

| Service    | Default Port | Purpose                  |
|------------|--------------|--------------------------|
| php        | 80           | The main application api |
| mysql      | 3306         | The mysql DB service     |
| phpmyadmin | 8080         | PHPMyAdmin service       |
