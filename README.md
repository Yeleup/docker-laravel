# Docker for laravel

Our Docker PHP project is a simple, yet powerful, Docker setup for PHP development. It is based on the official PHP Docker image and is designed to be used with Laravel.

Setup
------------

1. Clone the submodule in the root of the project ```git clone --recurse-submodules -j8 <laravel-repo> app```

2. Put in your .env file
     ```
       APP_PORT=8300
       MYSQL_PORT=3323
       MYSQL_PASSWORD=${DB_PASSWORD}
       MYSQL_DATABASE=${DB_DATABASE}
       MYSQL_USER=${DB_USERNAME}
       BUILD_TARGET=app_dev
     ```

3. For a development build which exposes DB ports and includes Xdebug, you can run the dev-mode shell script like so ```sh ./bin/dev-mode.sh -d```
   * For first time setup, run ```sh ./bin/dev-mode.sh -d --build```
   * To run with Xdebug enabled, run ```XDEBUG_MODE=debug sh ./bin/dev-mode.sh -d --build```
   
4. Composer install with docker
   * ```docker run --rm --interactive --tty -v $(pwd)/app:/app composer install``` or
   * ```docker exec -it <container_id> composer install```

> **_INFO:_**  Sometimes we need to change the permissions of the storage folder. To do this, run the following command: ``chmod -R 777 app/storage/``
