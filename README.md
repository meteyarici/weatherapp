# weatherapp

Installation
============

## Docker


   ```
sudo docker-compose up -d
   ```
   
   Composer install
   ```
sudo docker-compose run --rm frontend composer install
   ```
   
   Application init
   ```
docker-compose run --rm backend /app/init
([0] for development)
   ```
   
   Migration
   ```
sudo docker-compose run --rm frontend yii migrate
   ```

   Run
    
   ```
  frontend: http://127.0.0.1:20080/v1/apiinfo
   ```


## Local Setup
## About Yii Setup
https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/start-installation.md


#Api methods
```
/v1/user/signup
/v1/user/login
/v1/user/activate
/v1/user/update
/v1/weather/get
```


## Tests
 ```
vendor/bin/codecept run
 ```

## Cron
```
/php yii send
```
Cron job example
```
0 * * * * php /var/www/html/wappi/yii send
```



Application 

  
   ```
common/
    config/              contains general config files
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    modules/             containts weather api files
        v1/
          controlers/    containts api controlers
          models/        contains api models     
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
 ```