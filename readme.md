# Test Login Page Project Structure

## Code Structure

- Admin `Management System Business Logic Code`
    - Controllers `Controller layer code`
        - Controller.php    `Abstract controller with common method implementations`
        - DashboardController `Dashboard Page Controller Layer`
        - ChatController.php `Chat Page Controlle Layer`
    - Models `Model layer code`
      - BaseModel.php `Abstract Model with common method implementations`
      - AdministratorModel.php `Model layer related to administrators`
      - ChatModel.php `Model layer related to the administrator operation chat`
- App `Business application logic code`
    - Controllers `Controller layer code`
        - Controller.php    `Abstract controller with common method implementations`
        - AuthController.php  `Auth-related controller logic (login, logout, registration)`
        - HomeController.php  `Business Logic Code Controller Layer`
    - Models  `Model layer code`
        - BaseModel.php  `Abstract Model with common method implementations`
        - UserModel.php  `User-related Model methods (database operations)`
        - ChatModel.php ``
- Tools  `Utility classes`
    - Auth.php  `Permission Verification Related Method Encapsulation`
    - Config.ini.php
      `Config Template - Please copy this file as Config.php and modify the corresponding configurations`
    - Config.php  `Project configuration class (static properties). Access via Config::database etc.`
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
      - dashboard.view.php  `Admin dashboard page template`
    - home.view.php  `Home page template (after login)`
    - login.view.php  `Login page template`
    - register.view.php  `Registration page template`
- public  `Public directory (web root)`
    - index.php
      `Application entry point (request routing, function declarations, language setup, business logic invocation)`
    - assets
        - js  `JavaScript files`
            - auth.js  `Auth-related JavaScript logic`
            - chat.js  `Initiate new chat related js, for admin interface logic`
            - chat_user.js  `Get conversation list related js, for user interface chat interaction`
        - css  `CSS files`
            - styles.css  `All style definitions`
- table.sql  `Database schema SQL file`
- readme.md  `Project documentation`

### about administrator
> Administrators also have independent tables for storage, but for the convenience of testing, you can log in with the account 'admin' and the password 'admin', which is hardcoded in the code.
> 
> After entering the administrator list, you can subsequently add an administrator management module. At that time, you can perform operations such as "viewing", "adding", and "restricting" on administrators