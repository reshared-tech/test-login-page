# Test Login Page Project Structure

## Code Structure

- Admin `Management System Business Logic Code`
    - Controllers `Controller layer code`
        - Controller.php    `Abstract controller with common method implementations`
        - DashboardController `Dashboard Page Controller Layer`
- App `Business application logic code`
    - Controllers `Controller layer code`
        - Controller.php    `Abstract controller with common method implementations`
        - AuthController.php  `Auth-related controller logic (login, logout, registration)`
        - HomeController.php  `Business Logic Code Controller Layer`
    - Models  `Model layer code`
        - BaseModel.php  `Abstract Model with common method implementations`
        - UserModel.php  `User-related Model methods (database operations)`
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
    - basic  `Common frontend components`
        - head.view.php  `Common header template`
        - foot.view.php  `Common footer template`
    - errors    ``
        - 404.view.php    `404 Page`
        - 500.view.php    `Server 500 Error Page`
    - home.view.php  `Home page template (after login)`
    - login.view.php  `Login page template`
    - register.view.php  `Registration page template`
- public  `Public directory (web root)`
    - index.php
      `Application entry point (request routing, function declarations, language setup, business logic invocation)`
    - assets  `Static files (js+css+img)`
        - js  `JavaScript files`
            - auth.js  `Auth-related JavaScript logic`
        - css  `CSS files`
            - styles.css  `All style definitions`
- table.sql  `Database schema SQL file`
- readme.md  `Project documentation`