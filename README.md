About
========

The project is proof of concept where I tried to created feature where allow generate webhook url and accept request in this webhook url.

The main goal in this project is make the webhook url access huge volume of requests using PHP + Laravel.

Technologies
===============

- PHP 8(opcache and jit enabled)
- Laravel
- Laravel Octane(using swoole)
- DragonflyDB(is like REDIS) for queue
- Docker 
- Docker-compose
- Cockroach(Database)

Things I did to improve performance:
======================================

- Enabled PHP opcache 
- Enabled PHP jit
- Execute command **php artisan optimize** to cache the laravel **routes** and **config**
- Laravel octane + swoole to enable asynchronous programming for PHP, work as Node.js.
- DragonflyDB is solution to replace Redis where can handle more interactions using same machine resources.

Instructions to run project:
================================
- Clone repository
- Create file **.env** file based **.env.exmaple** file. WARN: you need changes some envs like:

    ```
        DATABASE_URL='postgresql://postgres:root@database:5432/postgres'
        DB_CONNECTION=pgsql  
        QUEUE_CONNECTION=redis
        
        REDIS_HOST=queue
        REDIS_PASSWORD=null
        REDIS_PORT=6379
        REDIS_CLIENT=predis      
    ```
- Execute command **docker-compose up -d --build** to run the follow containers: api, database, queue, worker and worker2. To access api address http://localhost:8000
- Execute command **docker exec -it website-hook-clone-application php artisan migrate** to run migrations on database container.


## Architecture:


![The project architecture](websitehook_architecture.drawio.png "The project architecture")

### Explaining the architecture

- The user make request using webhook url for Laravel application.
- The application take the request data like querystring and body request to send for queue, because prevent overload the database with many insert operations.
- The worker 1 and worker 2 get messages and process each message and save on DB.

Load test results(I used autocannon tool):
=============================================

- Total requests: 1000 | Total concurrent users: 100 | All application running in docker
- 1º battery of tests:

┌─────────┬───────┬────────┬────────┬────────┬───────────┬──────────┬────────┐
│ Stat    │ 2.5%  │ 50%    │ 97.5%  │ 99%    │ Avg       │ Stdev    │ Max    │
├─────────┼───────┼────────┼────────┼────────┼───────────┼──────────┼────────┤
│ Latency │ 62 ms │ 205 ms │ 293 ms │ 303 ms │ 200.98 ms │ 49.79 ms │ 348 ms │
└─────────┴───────┴────────┴────────┴────────┴───────────┴──────────┴────────┘
┌───────────┬─────────┬─────────┬────────┬────────┬─────────┬─────────┬─────────┐
│ Stat      │ 1%      │ 2.5%    │ 50%    │ 97.5%  │ Avg     │ Stdev   │ Min     │
├───────────┼─────────┼─────────┼────────┼────────┼─────────┼─────────┼─────────┤
│ Req/Sec   │ 66      │ 66      │ 442    │ 492    │ 333.34  │ 190.14  │ 66      │
├───────────┼─────────┼─────────┼────────┼────────┼─────────┼─────────┼─────────┤
│ Bytes/Sec │ 19.1 kB │ 19.1 kB │ 128 kB │ 142 kB │ 96.3 kB │ 54.9 kB │ 19.1 kB │
└───────────┴─────────┴─────────┴────────┴────────┴─────────┴─────────┴─────────┘

- 2º battery of tests:

┌─────────┬───────┬────────┬────────┬────────┬───────────┬──────────┬────────┐
│ Stat    │ 2.5%  │ 50%    │ 97.5%  │ 99%    │ Avg       │ Stdev    │ Max    │
├─────────┼───────┼────────┼────────┼────────┼───────────┼──────────┼────────┤
│ Latency │ 60 ms │ 211 ms │ 272 ms │ 282 ms │ 205.47 ms │ 45.16 ms │ 299 ms │
└─────────┴───────┴────────┴────────┴────────┴───────────┴──────────┴────────┘
┌───────────┬───────┬───────┬────────┬────────┬─────────┬─────────┬───────┐
│ Stat      │ 1%    │ 2.5%  │ 50%    │ 97.5%  │ Avg     │ Stdev   │ Min   │
├───────────┼───────┼───────┼────────┼────────┼─────────┼─────────┼───────┤
│ Req/Sec   │ 83    │ 83    │ 441    │ 476    │ 333.34  │ 177.59  │ 83    │
├───────────┼───────┼───────┼────────┼────────┼─────────┼─────────┼───────┤
│ Bytes/Sec │ 24 kB │ 24 kB │ 127 kB │ 138 kB │ 96.3 kB │ 51.3 kB │ 24 kB │
└───────────┴───────┴───────┴────────┴────────┴─────────┴─────────┴───────┘

- 3º battery of tests:

┌─────────┬───────┬────────┬────────┬────────┬───────────┬──────────┬────────┐
│ Stat    │ 2.5%  │ 50%    │ 97.5%  │ 99%    │ Avg       │ Stdev    │ Max    │
├─────────┼───────┼────────┼────────┼────────┼───────────┼──────────┼────────┤
│ Latency │ 56 ms │ 206 ms │ 278 ms │ 412 ms │ 204.66 ms │ 57.87 ms │ 570 ms │
└─────────┴───────┴────────┴────────┴────────┴───────────┴──────────┴────────┘
┌───────────┬─────────┬─────────┬────────┬────────┬─────────┬─────────┬─────────┐
│ Stat      │ 1%      │ 2.5%    │ 50%    │ 97.5%  │ Avg     │ Stdev   │ Min     │
├───────────┼─────────┼─────────┼────────┼────────┼─────────┼─────────┼─────────┤
│ Req/Sec   │ 78      │ 78      │ 429    │ 493    │ 333.34  │ 182.43  │ 78      │
├───────────┼─────────┼─────────┼────────┼────────┼─────────┼─────────┼─────────┤
│ Bytes/Sec │ 22.5 kB │ 22.5 kB │ 124 kB │ 143 kB │ 96.4 kB │ 52.7 kB │ 22.5 kB │
└───────────┴─────────┴─────────┴────────┴────────┴─────────┴─────────┴─────────┘
