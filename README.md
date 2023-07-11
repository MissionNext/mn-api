# Mission Next Api

Actual versions (updated 16.09.2022):
- PHP v5.4
- Laravel v5.0
- Composer v2.2

# Docker services

- **app**: main container with php-fpm
- **db**: we using postgres now
- **nginx**

# Install and Usage

We use docker-compose for up and running project

Please, create **.env** file inside mn-api folder. You can use .env.expample for it

1. Fits of all [download]('https://curl.se/docs/caextract.html') and save pem key to mn-api folder (it's can be skiped in future versions of php-fpm docker container) ('https://curl.se/docs/caextract.html')

2. Next, buld our app using command:
```
docker-compose --env-file ./docker-compose.env build app
```

We will build mn-api docker-compose service with php-fpm, instaled composer, etc.

*Also here we set `docker-compose.env` file with some additional envs for running docker-compose.yml 

3. Lets **run** our containers:
```
docker-compose --env-file ./docker-compose.env up -d
```

4. Install composer packages
```
docker-compose exec app composer install
```
and update it
```
docker-compose exec app composer update
```

5. (optional) Generate APP KEY:
```
docker-compose exec app php artisan key:generate
```

4. (optional) Sometimes we need to udpate premissions for nginx container, use:
```
docker-compose exec nginx chmod -R 777 /var/www/bootstrap /var/www/storage
```

It's will fixed errors for nginx with **bootstrap/autoload.php** reading file, when you trying to open app in browser
=======
127.0.0.1 missionnext.org
127.0.0.1 www.missionnext.org
127.0.0.1 explorenext.org
127.0.0.1 journey.explorenext.org
127.0.0.1 education.explorenext.org
127.0.0.1 it-technology.explorenext.org
127.0.0.1 short-term.explorenext.org
127.0.0.1 quickstart.explorenext.org
127.0.0.1 jg.explorenext.org
127.0.0.1 www.explorenext.org


72.167.50.62   api.missionnext.org
72.167.50.62   quickstart.missionnext.org
72.167.50.62   journey.missionnext.org
72.167.50.62   it-technology.missionnext.org
72.167.50.62   education.missionnext.org
72.167.50.62   short-term.missionnext.org
72.167.50.62   missionnext.org
72.167.50.62   www.missionnext.org

72.167.50.62   explorenext.org
72.167.50.62   journey.explorenext.org
72.167.50.62   education.explorenext.org
72.167.50.62   it-technology.explorenext.org
72.167.50.62   short-term.explorenext.org
72.167.50.62   quickstart.explorenext.org
72.167.50.62   jg.explorenext.org
72.167.50.62   www.explorenext.org

70.32.96.216   explorenext.org
70.32.96.216   journey.explorenext.org
70.32.96.216   education.explorenext.org
70.32.96.216  it-technology.explorenext.org
70.32.96.216   short-term.explorenext.org
70.32.96.216   quickstart.explorenext.org
70.32.96.216   jg.explorenext.org
70.32.96.216   www.explorenext.org

