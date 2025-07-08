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
    git clone https://github.com/wofun/test-task ./
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

Run docker containers:
~~~
    docker compose -f docker-compose.arm64v8.yml -f docker-compose.yml up -d
~~~

Exec bash of the server container 
~~~
    # Run docker container bash
    sh bash.sh
~~~

Run migrations:
~~~
    # Run dektrium/yii2-user migrations
    ./yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations

    # Run yii2-rbac migrations
    ./yii migrate/up --migrationPath=@yii/rbac/migrations

    # Run custom rbac migrations
    ./yii migrate --migrationPath=@app/rbac/migrations

    # Run app migrations
    ./yii migrate
~~~

Database seeding.
Наповнення бази даних проходить відповідно до вимог ТЗ. Це тривалий процес, і може зайняти більше двох годин:
~~~
    ./yii seeder
~~~

Допоки наповнюється база даних, можно створити адміністратора і залогінитись у адмінку. Для цього відкриваємо окрему вкладку терміналу, та запускаємо bash контейнера сервера.
~~~
    sh bash.sh
~~~

Create an administrator.

Replace placeholders with your own data:
```php
    # Create a user
    ./yii user/create YOUR_USER_EMAIL YOUR_USER_LOGIN YOUR_USER_PASSWORD

    # Assign  administrator permissions
    ./yii rbac-role/assign YOUR_USER_EMAIL admin
```

You can then access the application through the following URL:
    http://127.0.0.1:8000

