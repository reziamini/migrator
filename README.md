# Migrator
Migrator is a GUI migration manager for Laravel which you can create, manage and delete your migration.

![Migrator photo](https://podcode.ir/img/migrator.png)
## Installation:

To install migrator you can execute this command:
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
