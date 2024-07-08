# Part Finder

### Overview

This application is designed to provide a comprehensive interface for searching and viewing detailed part information. After registering and verifying their account, users are able to search for part information. It features a search bar for querying parts and displays the search results dynamically. Users can click on any search result to view detailed information about the selected part in a structured tree view format.


### System Information

#### 1. Introduction

**1.1 Purpose**
The purpose of this document is to specify the requirements for a web application that allows users to register, log in, search, and view a database of part information.

**1.2 Scope**
The web application will be developed using Laravel 11, PHP 8.2, MySQL 5.7, and AWS SES for email services. It will include user authentication and a search functionality for part information stored to data base in JSON files.

#### 2. Functional

**2.1 Source Data Import**

-   Automatically data import and manual data import.

**2.2 User Registration**

-   The system shall allow users to register with a name, email, phone and password.
-   The system shall send a verification email using AWS SES.

**2.3 User Login**

-   The system shall authenticate users using their email and password.
-   The system shall prevent access to the database of part information until the user is logged in.
-   Logged in users are able to edit profile.

**2.4 Part Information Search**

-   The system shall allow users to search for part information.
-   The system shall display part information in a user-friendly format.

#### 3. Non-Functional

**3.1 Performance**

-   The system should respond to search queries within 2 seconds.

**3.2 Security**

-   The system should encrypt passwords and ensure secure data transmission.


### Process Flow

Source Data Importing Flow

![URL Shortening Flow](https://kavidu.com/dev/find/data.png)

Part Information Access Flow

![Redirection Flow](https://kavidu.com/dev/find/user.png)

### Prerequisites

-   **PHP**: Based on the dependencies, a minimum PHP version of 8.2. x is recommended.
-   **MySQL**: Based on the dependencies, a minimum MySQL version of 5.6. x is recommended.
-   **Composer**:  PHP 8.2 Support a minimum version.
-   **NPM**:  A minimum version of NPM 7.x is recommended.
-   **Git**:  Support your local environment.

### Setting Up the Application

-  **Clone Repository**:
    - Use this this URL to clone the repository - https://github.com/kavidu-hettiarachchi/part-finder/

-  **Configure Environment Variables**:

    -   Copy the `.env` sample file to create your environment configuration: `cp .env.example .env`.
    -   Edit the `.env` file to suit your environment, especially the database connection and Email Service (AWS SES) details.

        	    DB_CONNECTION=mysql   
        	    DB_HOST=127.0.0.1
        	    DB_PORT=3306
        	    DB_DATABASE=dbname   
        	    DB_USERNAME=username   
        	    DB_PASSWORD=password
        	    
        	    MAIL_MAILER=ses  
        	    MAIL_HOST=email-smtp.us-east-1.amazonaws.com  
        	    MAIL_PORT=587  
        	    MAIL_USERNAME=aws_ses_username  
        	    MAIL_PASSWORD=aws_ses_password  
        	    MAIL_ENCRYPTION=tls  
        	    MAIL_FROM_ADDRESS=no-reply@example.com  
        	    MAIL_FROM_NAME="${APP_NAME}"

-  **Application Config**:

    - Run the `composer install`
    - Run the `npm install`
    - Run the `npm run build`
    - Run the `php artisan key:generate`

      > Note: Before running the above command, make sure you are in the root
      > folder.

        - ##### NPM and Composer Packages

          The required NPM and composer packages and their versions are listed in`package.json and composer.json`. Key packages include:

-  **Database Import**:

    -  There are two methods to import a database. If you want to import a fresh table structure into your database, you can use the command `php artisan migrate`. Alternatively, if you want to test the application with demo data, you can import an SQL dump file into your database. You can find the database dump file in the `{project-root-folder}/database/db-dump`.

### Source Data Import

If you are using a fresh database or an existing database with demo data, and you want to import new part information into the database from a JSON file, you can do so in two ways. The first method involves manually running a command, while the other method involves setting up a cron job along with a specific time and command, so that the JSON files are automatically imported into the database. However, you need to copy or move your source data to the following folder, where it will be automatically accessed by the system.

Copy source JSON files to - `{project-root-folder}/public/source-data-json/in-progress`

![enter image description here](https://kavidu.com/dev/find/10.png)

If you wish to use manual command use the below command.

    php artisan import:json --manual

If you wish to setup with cron job use the below command.

    * * * * * cd /Users/kavidu/workspace/part-finder && php artisan import:json

### Access the Application

After you have completed the setup steps, the application should be ready to run. There are two ways to run the application. If you want to run the application without a web server, you can use the following command in your local project root folder: `php artisan serve`. After that, you can access the application in your web browser using the following URL: http://127.0.0.1:8000.

If you want to run the application with a local web server, first create a virtual host and point the virtual host directory to `{project-root-folder}/public`. Then you can access the application using your virtual host domain.

- Data Access
    - When you access the app on your web browser, you will see the screen below.

      ![enter image description here](https://kavidu.com/dev/find/1.png)

    - If you set up the application with demo data, you will be able to log in using the following credentials.
        - Username - admin@admin.com
        - Password - admin123

      ![enter image description here](https://kavidu.com/dev/find/2.png)
    - If you are not using a fresh database with the application, you will need to register first.

![enter image description here](https://kavidu.com/dev/find/3.png)

- Once you register you will receive account activation email.

![enter image description here](https://kavidu.com/dev/find/4.png)

- After verifying your email address you are able to login to the system.
- When you successfully logged in to the system you can see the field to search parts.
  ![enter image description here](https://kavidu.com/dev/find/5.png)

- You can search using IE Control Number, Media Number, Part Number, and Part Name. (Example : 0S1590).
- When you type more than four digits or characters, related to the above information a suggestions list will appear.  Once you select on any one of these suggestions, you can see part information tree.

![enter image description here](https://kavidu.com/dev/find/6.png)

- If you wanted to see more information related to the tree you can click the side tree arrow to expand and see the information.
  ![enter image description here](https://kavidu.com/dev/find/8.png)

- By clicking the profile name sign posted by a down arrow you can  visit your profile and edit information.
  ![enter image description here](https://kavidu.com/dev/find/7.png)

### Tools Used for the Development

- macOS
- iTerm2
- MAMP
- PhpStorm
- Google Chrom
- phpMyAdmin
- php artisan
- git

### Used Documenting and Services

- https://laravel.com
- https://tailwindcss.com
- https://jsoncrack.com
- https://stackedit.io
- https://app.diagrams.net
- https://github.com
