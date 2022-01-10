# Migrator
Migrator is a GUI migration manager for Laravel which you can create, manage and delete your migration.

Also, with Migrator you will be able to use a feature called "Safe Migrate" which allows you to run migration without fear of foreign key sorting, it will automatically run migrations in the correct order and you don't need to change the migrations filename.

![Migrator photo](https://user-images.githubusercontent.com/86796762/148734667-b50955b3-e8d8-4a6d-8057-8a1c293eb75a.png)
## Installation:

To install Migrator you can execute this command:
```bash
composer require rezaamini-ir/migrator
```

Then you will access to `/migrator` route.

## Config
To access config file you need to publish config files to your project with this command:
```bash
php artisan vendor:publish --tag=migrator-config
```

Now you will be able to change config as you want!

To change the route path you can change the `route` key in migrator config.

And, with `middleware` key you can set your middleware to authenticate your user.

**If you don't need to authenticate users to access migrator you can set the value to `web`.**
