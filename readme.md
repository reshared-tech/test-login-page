# Test Login Page Project Structure

## Code Structure

- Admin `Management System Business Logic Code`
    - Controllers `Controller layer code`
        - Controller.php    `Abstract controller with common method implementations`
        - HomeController `Home Page Controller Layer`
        - ChatController.php `Chat Page Controller Layer`
        - UserController.php `User Page Controller Layer`
    - Models `Model layer code`
      - AdministratorModel.php `Model layer related to administrators`
- App `Business application logic code`
    - Controllers `Controller layer code`
        - Controller.php    `Abstract controller with common method implementations`
        - AuthController.php  `Auth-related controller logic (login, logout, registration)`
        - HomeController.php  `Business Logic Code Controller Layer`
        - ChatController.php  `User's Chat Page Controller Layer`
    - Models  `Model layer code`
        - BaseModel.php  `Abstract Model with common method implementations`
        - UserModel.php  `User-related Model methods (database operations)`
        - ChatModel.php  `User-related Chat functions`
- Tools  `Utility classes`
    - Auth.php  `Permission Verification Related Method Encapsulation`
    - Config.ini.php
      `Config Template - Please copy this file as Config.php and modify the corresponding configurations`
    - Config.php  `Project configuration class (videos properties). Access via Config::database etc.`
    - Database.php  `Database class wrapper (PDO singleton and common method implementations)`
    - Language.php
      `Multi-language configuration. Configure language mappings (English as key) and set language in index.php entry point`
    - Router.php   `Routing Configuration Related Method Encapsulation`
    - Utils.php   `Utility class (currently includes IP address and UserAgent retrieval methods)`
    - Validator.php   `Parameter Validation Related Method Encapsulation`
- views  `Frontend code`
    - basic
        - head.view.php  `Common header template`
        - foot.view.php  `Common footer template`
    - errors
        - 404.view.php    `404 Page`
        - 500.view.php    `Server 500 Error Page`
    - admin
      - sidebar.view.php    `Sidebar tempalte`
      - paginator.view.php   `Paginator tempalte`
      - dashboard.view.php  `Admin dashboard page template`
      - users.view.php       `Users list dashboard page template`
      - chats.view.php       `Chats list dashboard page template`
    - home.view.php  `Home page template (after login)`
    - login.view.php  `Login page template`
    - register.view.php  `Registration page template`
    - chat.view.php   `User chat room page`
- index.php   `Application entry point (request routing, function declarations, language setup, business logic invocation)`
- migrate.php `For database migration tools, to update the db structure, simply run php migration.php`
- assets
    - js  `JavaScript files`
        - chat
          - create.js   `Create javascript related to chat interaction logic`
          - dialog.js   `js logic related to the chat window`
          - list.js     `Chat list-related js`
        - auth.js  `Auth-related JavaScript logic`
    - css  `CSS files`
        - styles.css  `All style definitions`
- .htaccess  `Apache redirect config`
- videos     `Some images for readme file, ***Unrelated*** to code`
- migrations  `Database migrations sql files`
- readme.md  `Project documentation`

### about administrator
> Administrators also have independent tables for storage, but for the convenience of testing, you can log in with the account 'admin' and the password 'admin', which is hardcoded in the code.
> 
> After entering the administrator list, you can subsequently add an administrator management module. At that time, you can perform operations such as "viewing", "adding", and "restricting" on administrators

### Set up
- First, set the root directory of your `httpd` or `nginx` service to this project directory
```apacheconf
<Directory "/path/to/your/webroot/test-login-page">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted

    # make sure enable "mod_rewrite"
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php/$1 [L]
    </IfModule>
</Directory>
```
```shell
server {
    listen 80;
    listen [::]:80;
    index index.html index.php
    server_name localhost;
    root /path/to/your/webroot/test-login-page;
    autoindex on;
    charset utf-8;
    location ~ ^/([^/]+)/ {
        try_files $uri $uri/ /$1/index.html /$1/index.php;
    }
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
}
```

- Then copy the initialization configuration file: Config.ini.php => Config.php
```shell
cp Tools/Config.ini.php Tools/Config.php
```

- And then configure the DB configuration, time zone, and your server domain name + path:
```php
const database = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'dbname' => 'your db',
    'username' => 'your username',
    'password' => 'your password',
    'charset' => 'utf8mb4', // change it if you need
];
const timezone = 'Asia/Shanghai';
const domain = 'http://localhost/test-login-page';
```

- And then just open the browser:`http://localhost/test-login-page`

## Function demonstration
### User registration
![user registration demo](videos/1-user%20registration%20demo.mp4 "user registration demo")
### User login
![user login demo](videos/2-user%20login%20demo.mp4 "user login demo")
### Administrator permission verification, can view all the user lists
![users list demo](videos/3-users%20list%20demo.mp4 "users list demo")
### The administrator can view all users by turning pages
![users list pages demo](videos/4-users%20list%20pages%20demo.mp4 "users list pages demo")
### Administrators can select two users to initiate a chat
![start chat demo](videos/5-start%20chat%20demo.mp4 "start chat demo")
### After logging in, the user's home page displays a list of all chats
![user chat list demo](videos/6-user%20chat%20list%20demo.mp4 "user chat list demo")
### Clicking on a single session will redirect you to the chat room page
![user chat window demo](videos/7-user%20chat%20window%20demo.mp4 "user chat window demo")
### After entering the content, click the Send button or press `shift + entry` to send the message
![send message demo](videos/8-send%20message%20demo.mp4 "send message demo")
### After the other party reads the message, the message status will be automatically updated to "read"
![read status update demo](videos/9-read%20status%20update%20demo.mp4 "read status update demo")
### Click the button at the top to get more historical messages
![show more history demo](videos/10-show%20more%20history.mp4 "show more history")

## Database migrate
### A complete database migration can be performed by running `php migration.php`, including initializing to a new database
![init_database_demo](videos/11-init_database_demo.mp4 "init_database_demo")
### When you attempt to use a non-empty database, it will perform a check and report an error because old tables will be deleted during the operation process and the data is invaluable
![check_database_is_empty_demo](videos/12-check_database_is_empty_demo.mp4 "check_database_is_empty_demo")
### When there is a new change in the database structure, you can run `php migrate.php new` to create a new sql file with the current timestamp. If you run `php migrate.php` again at this time, only incremental changes will be made
![only_incremental_changes_demo](videos/13-only_incremental_changes_demo.mp4 "only_incremental_changes_demo")