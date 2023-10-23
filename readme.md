Installation
===============
1. run "composer install"
2. create local postgresql database , change credentials on app/config/app.php
        $dbHost = 'localhost';
        $dbName = 'levart-test-db';
        $dbUser = 'postgres';
        $dbPassword = '1234';
3. execute this SQL query to create table
    ====================================================
        CREATE TABLE email_log (
            id serial PRIMARY KEY,
            from_email VARCHAR(255) NOT NULL,
            to_email VARCHAR(255) NOT NULL,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            sent_at TIMESTAMPTZ NOT NULL
        );
    ========================================================
4. create SMTP email, in this case i use mailtrap to do tests, change the credentials on app/config/app.php
        $smtpServer = 'sandbox.smtp.mailtrap.io';
        $smtpUsername = '58227eb102dd2f';
        $smtpPassword = '7599873514db8c';
        $smtpPort = 2525;
5. run "docker-compose up --build" (worker and redis will work on docker container)
6. run "php -S localhost:8000" and dont close terminal.
7. send POST request to http://localhost:8000/index.php?controller=email&action=sendEmail (sample on file api.postman_collection.json)
    Body (JSON):
    ===============================
    {
        "from":"from@gmail.com",
        "to":"to@gmail.com",
        "subject":"this is email's subject",
        "body":"I this is email's body"
    }
    ===============================

