# FWSTATS 🚀

Statistics website for the worlds _ActionFreewar_ and _ChaosFreewar_ in the browser game [freewar.de](https://www.freewar.de).

## TechStack

PHP 8.2, Bulma CSS, SQLite, Vanilla JS, Apache httpd with mod_php. All of this is shipped as a single docker image. 

## Quality assurance

- PHPUnit: Unit tests and application tests against a database
- PHPStan: Level max, 100% type coverage, strict rules + own custom rules
- PHP-CS-Fixer: PSR-12

## Deployment strategy

Creating a new tag and pushing this tag to GitHub runs a GitHub action which builds a new container image, tags the image with the new tag and pushes this image to the GitHub container registry.
After this I will just stop the running container on my server and start a new one.
No rolling update needed, not like I have tons of users. 🤷‍♂️

Check the newest docker image [here](https://github.com/jesperbeisner/fwstats/pkgs/container/fwstats).

## Local setup

### 1. Start the docker container

```bash
docker compose up -d
```

### 2. Install composer packages

Use the docker container to install composer packages. This way it's not important which PHP version you have installed locally, and you do not run into any errors.

```bash
docker compose exec php composer install
```

### 3. Load migrations

```bash
docker compose exec php php bin/console.php app:database-migration
```

### 4. Load fixtures

```bash
docker compose exec php php bin/console.php app:database-fixtures
```

### 5. Finished

Open your browser and visit http://localhost:8080. A test account with username `admin` and password `Password12345` was also created for you.


## First deployment


### 1. Create a named volume

```bash
docker volume create fwstats-prod
```

### 2. Run the docker image
```bash
docker run --detach --volume fwstats-prod:/var/www/html/data/database --publish 8888:80 --name fwstats-prod ghcr.io/jesperbeisner/fwstats:latest
```

### 3. Run the migrations
```bash
docker exec fwstats-prod php bin/console.php app:database-migration
```

### 4. Run one cronjob for everything every 5 minutes
```bash
*/5 * * * * docker exec fwstats-prod php bin/console.php app:run > /dev/null 2>&1 
```

### 4 1/2. Use the `/cronjob` endpoint

In addition to the cronjob, there is also a cronjob endpoint (`/cronjob`) that does the same thing as the normal cronjob. You need to send a post request with your bearer token which you can find in the admin panel when you log in.
For this to run reliably, a service such as [cron-job.org](https://cron-job.org) can be used. Just call the endpoint every 5 minutes and that's it.


### 5. Finished!

Point your reverse proxy on your published port (In this example 8888) and visit your domain. You can log in with the automatically created credentials `admin` as username and `Password12345` as password. **Change the password and e-mail after your first login**.  That's it, you're done. 🚀

## Additional deployments

### 1. Remove your running container

```bash
docker rm -f fwstats-prod
```

### 2. Remove the old image

```bash
docker rmi -f ghcr.io/jesperbeisner/fwstats:latest
```

### 3. Start a new container with the new image

```bash
docker run --detach --volume fwstats-prod:/var/www/html/data/database --publish 8888:80 --name fwstats-prod ghcr.io/jesperbeisner/fwstats:latest
```

### 4. Run the migrations

```bash
docker exec fwstats-prod php bin/console.php app:database-migration
```

### 5. Finished

Upgrade is done. You are now running the newest version. 🚀
