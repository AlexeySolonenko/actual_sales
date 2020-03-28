# CSV Uploader Actual Sales


**Table of Contents**

**[App Usage](#app-usage)**

[Deployed App](#deployed-app)

[Local App](#local-deploy)

[DB](#db)



**[Fulfilment report](#fulfilment-report)**

[Implemented](#implemented)

[Features](#features)

[Missing](#missing)



---

## APP USAGE

### Deployed App

Deployed on <a href="http://tab4lioz.beget.tech/developer_trial/index.php" target="_blank">`heroku`</a>
Please, Ctrl+F5 if there are problems with CDN.

I apology for a lot of things missing, such as ssl, phpdoc, proper sanitation, and many others. But I had a relatively short time - just one evening, to do the task.

### Local App

Clone repo

Install composer, if required.

`composer install`

For the app to work it needs:
- path to `db_config.json` file. This file is supposed to be out of the publicly avaialble repository. This file should provide a json object for DB connection, [like](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class#initialization)
- `assets_path.json`, if needed


### DB

Runs on MySQL. Queries are stored in JSON. There are two buttons to delete and re-create the tables.

## Fulfillment report

### Implemented

- table creation - 2 buttons + Ajax
- import CSV. line-by-line parsing. Sanitation, security checks, tools to adjust performance. Customly written parser by me.
- import CSV - you can either drag&drop a file, or do nothing - the file will be downloaded from a remote host.
- Data representation - DataTables + pagination, sorting.
- using POST instead of GET
- filters
- - date range from/to
- - client, deal search

### Features

- DataTables dynamic plugin
- custom PHP
- MySQL query builder lib
- preventing duplicate usernames and deal types

### Missing
- Laravel, worked with Lumen a bit, but did not setup a Laravel app.
- did not have time for login/logout
- did not understand 'group_by' hour/day/month - how should the aggregate data be presented, please? Only accepts/refuses over a given period?
- did not understand what kind of script is required to provide for DB? Like ``;
