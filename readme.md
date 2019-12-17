
# About Monero CCS

Monero CCS is a simple web system for capturing donations made to fund community projects

# CCS Deployment Quickstart

## Requirements
```
mysql >= 5.7.7
php >= 7.1
```

## Deployment

```
apt update
apt install -y cron git jekyll mysql-server nginx php php-curl php-fpm php-gd php-mbstring php-mysql php-xml unzip
```

Install `Composer` following the instructions at https://getcomposer.org/download/

Checkout and configure CCS backend, frontend and proposals repositories (replace `<REPOSITORY_CCS_BACKEND>`, `<REPOSITORY_CCS_FRONTEND>`, `<REPOSITORY_CCS_PROPOSALS>` with the actual URLs)
```
cd /var/www/html

git clone <REPOSITORY_CCS_BACKEND>
git clone <REPOSITORY_CCS_FRONTEND>
git clone <REPOSITORY_CCS_PROPOSALS> ccs-back/storage/app/proposals

rm -rf ccs-front/proposals
ln -s /var/www/html/ccs-back/storage/app/proposals ccs-front/proposals
ln -fs /var/www/html/ccs-back/storage/app/proposals.json ccs-front/_data/proposals.json
ln -fs /var/www/html/ccs-back/storage/app/complete.json ccs-front/_data/completed-proposals.json

cd ccs-back
composer update
cp .env.example .env
```

Spin up MYSQL server, create new database, user and grant user access to it  
Open `.env` in editor of choice and edit the following lines:  
> `COIN` - choose one of supported coins: `monero` or `zcoin`  
> `REPOSITORY_URL` - CCS proposals Github URL or GitLab API endpoint (e.g. https://\<GITLAB_DOMAIN>/api/v4/projects/\<PROJECT_ID>)>  
> `GITHUB_ACCESS_TOKEN` - leave empty if you are not using Github or visit https://github.com/settings/tokens to generate new `public_repo` token
```
APP_URL=http://<HOSTNAME>

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<DB_NAME>
DB_USERNAME=<DB_USER_NAME>
DB_PASSWORD=<DB_USER_PASSWORD>

RPC_URL=http://127.0.0.1:28080/json_rpc
RPC_USER=
RPC_PASSWORD=

COIN=<COIN>

REPOSITORY_URL=<REPOSITORY_URL>
GITHUB_ACCESS_TOKEN=
```

Initialize the system
```
php artisan migrate:fresh
php artisan up
php artisan key:generate
php artisan proposal:process
php artisan proposal:update
```

Grant `www-data` user access to the files
```
cd ..
chown -R www-data ccs-back/
chown -R www-data ccs-front/
```

Remove Nginx example config
```
rm /etc/nginx/sites-enabled/default
```
Create new file `/etc/nginx/sites-enabled/ccs` in editor of choice and paste the following lines replacing `<HOSTNAME>` and `<PHP_VERSION>` with appropriate values

```
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    root /var/www/html/ccs-front/_site/;
    index index.php index.html;
    server_name <HOSTNAME>;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # pass the PHP scripts to FastCGI server
    #

    location ~ \.php$ {
        root /var/www/html/ccs-back/public/;
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php<PHP_VERSION>-fpm.sock;
    }
}
```

```
service nginx reload
```

Set up a cron job that will run periodic updates (every minute) and generate static HTML files
```
* * * * * git -C /var/www/html/ccs-back/storage/app/proposals/ pull; php /var/www/html/ccs-back/artisan schedule:run; jekyll build --source /var/www/html/ccs-front --destination /var/www/html/ccs-front/_site
```

## Optional
Instead of scheduling a cron job you can run the following commands in no particular order
1. Update CCS system proposals intenal state
    ```
    php /var/www/html/ccs-back/artisan proposal:process
    php /var/www/html/ccs-back/artisan generate:addresses
    php /var/www/html/ccs-back/artisan wallet:notify
    php /var/www/html/ccs-back/artisan proposal:update
    ```
2. Process incoming donations  
*Run it either on new block/tx notification or schedule it to run every minute or so*
    ```
    php /var/www/html/ccs-back/artisan monero:notify
    ```
1. Generate static HTML files
    ```
    jekyll build --source /var/www/html/ccs-front --destination /var/www/html/ccs-front/_site
    ```
2. Get the full list of processed transactions in JSON format
    ```
    php /var/www/html/ccs-back/artisan deposit:list
    ```
