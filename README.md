# Docker - PHP

[Docker PHP][1] is a **Docker and PHP** repository which accompanies [a YouTube tutorial][2].

Setup
------------

* Create app folder 
```composer create-project symfony/skeleton:"6.3.*" app```
* ```cd app```
* ```composer require webapp```
* Put in your .env.local file 
  ```
    MYSQL_PORT=3336
    MYSQL_PASSWORD=secret
    MYSQL_DATABASE=docker-php
    MYSQL_USER=user
    REDIS_PORT=6389
    BUILD_TARGET=app_dev
    DATABASE_URL="mysql://user:secret@db:3306/docker-php?serverVersion=8.0.34&charset=utf8mb4"
    ```
* For a standard build / setup, simply run
```docker compose up -d ```
* For a development build which exposes DB ports and includes Xdebug, you can run the dev-mode shell script like so
```sh ./bin/dev-mode.sh -d```
* To run with Xdebug enabled, run 
```XDEBUG_MODE=debug sh ./bin/dev-mode.sh -d --build```


Branches
-------------

Each branch (except main, dev, and branches prefixed with 'feature') corresponds to an accompanying series lesson.   

Contributing
------------

Docker PHP is an Open Source project and contributions are welcome. The 'main' branch is read-only as this should not differ from the tutorials so please send pull requests to the develop branch.

[1]: https://github.com/GaryClarke/docker-php
[2]: https://youtu.be/qv-P_rPFw4c
