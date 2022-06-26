## How to setup

Configure Laravel App

1. after cloning the project, create .env file in the main project folder
2. copy content from .env.example to the new created .env file
3. open [this shortcut link](https://app.shortcut.com/shkolo/settings/account/api-tokens) and generate new API Token for your account
4. copy the generated API Token and place it in the .env file right after SHORTCUT_API_TOKEN
5. in the .env file right after BACKUP_SHORTCUT_API_TOKENS as many comma separated tokens as needed could be added in order to avoid shortcut's request limitations
6. open new Terminal in the main folder of your laravel application and type in following command: composer update

Start Laravel App

1. open new Terminal in the main folder of your laravel application run following commands:

- php artisan config:cache
- php artisan route:cache
- php artisan view:cache
- php artisan cache:clear

2. after done, run command: php artisan serve, to start your server
3. next you will see "Starting Laravel development server:" in the terminal window. You can either copy the whole link and paste it in a
   browser or type in http:\\localhost:{port}.


(in case css or javascript files don't work, please, take a look at the port of your running application
in the terminal, where your server is running, copy the port - the 4 numbers after ':' in the generated url,
change it in the .env file CDN_URL=http://localhost:{HERE} and clear the cache in new terminal window again)


## USEFUL LINKS

[Datatable Documentation](https://datatables.net/examples/index)


[Shortcut API Documentation](https://shortcut.com/api/rest/v3#We-changed-our-name)
