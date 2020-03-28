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

### Local App

Clone repo

Install composer, if required.

`composer install`

Provide:
- path to `db_config.json` file, which is supposed to be out of the publicly avaialble repository.
- `assets_path.json` if required

Run.

### DB

Runs on MySQL. Queries are stored in JSON. There are two buttons to delete and re-create the tables.

## Fulfillment report

### Implemented

- table creation - 2 buttons + Ajax
- import CSV. Fetching from URL + line-by-line parsing. Sanitation, security checks, tools to adjust performance. Customly written parser by me.
- Loading from an url - for simplicity curl fetching from URL.
- Data representation - DataTables + pagination, sorting.
- using POST instead of GET
- filters
- - date range from/to
- - client, deal search

### Features

- DataTables dynamic plugin
- custom PHP
- MySQL query builder lib

### Missing
- Laravel, worked with Lumen a bit, but did not setup a Laravel app.
- did not have time for login/logout
