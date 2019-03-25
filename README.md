# Formation OpenClassrooms PHP/Symfony - Project 5 : Blog

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=nicordev_formation-oc-php-projet5-blog&metric=alert_status)](https://sonarcloud.io/dashboard?id=nicordev_formation-oc-php-projet5-blog)

Contains UML diagrams and the files of the website.

## Diagrams

You'll find them in the **uml_diagrams** folder.

The `.pdf` file contains the diagrams:
* class diagram
* use case diagram
* sequence diagram
    * connection
    * add a comment
    * add an article
    * update member profile

The `.xml` file is for `draw.io`.

You'll find a file `p5_mpd.png` in the **database** folder which shows a diagram of the database.

## Setup

1. Clone the project
1. Create the database using the file **database/p5_create_db.sql**
1. Create the tables using the file **database/p5_tables.sql**
1. *Optional: you can insert a set of demo data in the database with **database/p5_sample_data.sql***
1. Run `composer install`
1. Fill the **config/sample_config.cfg** file with your database log in data and rename it to **config.cfg**:
    
    ```
    # Database connexion
    #
    # Host
    host=localhost
    # Database name
    dbname=put_your_database_name_here
    # User
    user=put_your_database_user_name_here
    # Password
    password=put_your_database_password_here
    # Charset
    charset=utf8mb4
    # Enjoy!
    ```
    
1. Enjoy