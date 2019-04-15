# Formation OpenClassrooms PHP/Symfony - Project 5 : Blog

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=nicordev_formation-oc-php-projet5-blog&metric=alert_status)](https://sonarcloud.io/dashboard?id=nicordev_formation-oc-php-projet5-blog)

Contains UML diagrams and the files of the website.

Website written in plain PHP with a bit of JavaScript. I also used [Twig](https://twig.symfony.com/) and embedded a bootstrap blog template named [Clean Blog](https://startbootstrap.com/themes/clean-blog/).

As a bonus, I added [TinyMCE](https://www.tiny.cloud/) and used [intervention/image](https://packagist.org/packages/intervention/image) library to write beautiful blog posts.

In terms of security I implemented some protections against XSS, SQL injection, CSRF and brute force.

Design patterns used: MVC, DIC and singleton. 

I've learnt a lot during this project and it was really fun!

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

## Demo data

Here are the demo accounts:

Same password for all accounts: pwdSucks!1

| email | roles |
|:------|:------|
| mentor.validateur@benice.plz | member author editor moderator admin |
| jean.tenbien@yahoo.fr | member |
| sarah.croche@gmail.com | member author |
| jim.nastique@gmail.com | member editor |
| larry.viere@gmail.com | member moderator |
| paul.emploi@gmail.com | member admin |
| lenny.bards@gmail.com | member author editor |
