<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Test Task (Based on Yii2)</h1>
    <br>
</p>


INSTALLATION
------------
Clone git project:
~~~
git clone https://github.com/wofun/test-task.git ./
~~~

Update dependencies with Composer:
~~~
composer update
~~~


CONFIGURATION
-------------

Copy the `.env.example` configuration file:
~~~
cp .env.example .env
~~~

Edit the following `.env` file settings:
```php
DB_DATABASE='your_database_name'
DB_USERNAME='your_database_user'
DB_PASSWORD='user_password'
```


RUN DOCKER
-------------
ONLY for MacOS chip M1 and more:
~~~
docker compose -f docker-compose.yml -f docker-compose.arm64.yml up -d 
~~~

In other situations:
~~~
docker compose up -d
~~~


DATABASE
-------------
Run bash of the server container:
~~~
sh bash.sh
~~~

Run dektrium/yii2-user migrations:
~~~
./yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations
~~~

Run yii2-rbac migrations:
~~~
./yii migrate/up --migrationPath=@yii/rbac/migrations
~~~

Run init RBAC migrations:
~~~
./yii rbac/migrate
~~~

Run app migrations:
~~~
./yii migrate
~~~

Database seeding:
~~~
./yii seeder
~~~

Наповнення бази даних проходить відповідно до вимог ТЗ. Це тривалий процес, і може зайняти більше двох годин.

Допоки наповнюється база даних, можно створити адміністратора і залогінитись у адмінку. Для цього відкриваємо окрему вкладку терміналу, та запускаємо bash контейнера сервера.

CREATING AN ADMINISTRATOR
-------------
Run bash of the server container:
~~~
sh bash.sh
~~~

Create a user (Replace placeholders with your own data):
```php
./yii user/create YOUR_USER_EMAIL YOUR_USER_LOGIN YOUR_USER_PASSWORD
```

Assign administrator permissions:
```php
./yii rbac-role/assign YOUR_USER_EMAIL admin
```

You can then access the application through the following URL:
    http://127.0.0.1:8000

