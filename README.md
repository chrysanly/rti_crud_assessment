<p align="center">
    <h1 align="center">RTI Assessment CRUD Application</h1>
    <br>
</p>


REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 7.4.


INSTALLATION
------------

### Install via Composer

If you do not have [Composer](https://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](https://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
~~~

Now you should be able to access the application through the following URL, assuming `basic` is the directory
directly under the Web root.

~~~
http://localhost/basic/web/
~~~

### Install from an Archive File

Extract the archive file downloaded from [yiiframework.com](https://www.yiiframework.com/download/) to
a directory named `basic` that is directly under the Web root.

Set cookie validation key in `config/web.php` file to some random secret string:

```php
'request' => [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '<secret random string goes here>',
],
```

You can then access the application through the following URL:

~~~
http://localhost/basic/web/
~~~


### Install with Docker

Update your vendor packages

    docker-compose run --rm php composer update --prefer-dist
    
Run the installation triggers (creating cookie validation code)

    docker-compose run --rm php composer install    
    
Start the container

    docker-compose up -d
    
You can then access the application through the following URL:

    http://127.0.0.1:8000

**NOTES:** 
- Minimum required Docker engine version `17.04` for development (see [Performance tuning for volume mounts](https://docs.docker.com/docker-for-mac/osxfs-caching/))
- The default configuration uses a host-volume in your home directory `.docker-composer` for composer caches


CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the README in the `tests` directory for information specific to basic application tests.


TESTING
-------

Tests are located in `tests` directory. They are developed with [Codeception PHP Testing Framework](https://codeception.com/).
By default, there are 3 test suites:

- `unit`
- `functional`
- `acceptance`

Tests can be executed by running

```
vendor/bin/codecept run
```

The command above will execute unit and functional tests. Unit tests are testing the system components, while functional
tests are for testing user interaction. Acceptance tests are disabled by default as they require additional setup since
they perform testing in real browser. 


### Running  acceptance tests

To execute acceptance tests do the following:  

1. Rename `tests/acceptance.suite.yml.example` to `tests/acceptance.suite.yml` to enable suite configuration

2. Replace `codeception/base` package in `composer.json` with `codeception/codeception` to install full-featured
   version of Codeception

3. Update dependencies with Composer 

    ```
    composer update  
    ```

4. Download [Selenium Server](https://www.seleniumhq.org/download/) and launch it:

    ```
    java -jar ~/selenium-server-standalone-x.xx.x.jar
    ```

    In case of using Selenium Server 3.0 with Firefox browser since v48 or Google Chrome since v53 you must download [GeckoDriver](https://github.com/mozilla/geckodriver/releases) or [ChromeDriver](https://sites.google.com/a/chromium.org/chromedriver/downloads) and launch Selenium with it:

    ```
    # for Firefox
    java -jar -Dwebdriver.gecko.driver=~/geckodriver ~/selenium-server-standalone-3.xx.x.jar
    
    # for Google Chrome
    java -jar -Dwebdriver.chrome.driver=~/chromedriver ~/selenium-server-standalone-3.xx.x.jar
    ``` 
    
    As an alternative way you can use already configured Docker container with older versions of Selenium and Firefox:
    
    ```
    docker run --net=host selenium/standalone-firefox:2.53.0
    ```

5. (Optional) Create `yii2basic_test` database and update it by applying migrations if you have them.

   ```
   tests/bin/yii migrate
   ```

   The database configuration can be found at `config/test_db.php`.


6. Start web server:

    ```
    tests/bin/yii serve
    ```

7. Now you can run all available tests

   ```
   # run all available tests
   vendor/bin/codecept run

   # run acceptance tests
   vendor/bin/codecept run acceptance

   # run only unit and functional tests
   vendor/bin/codecept run unit,functional
   ```

### Code coverage support

By default, code coverage is disabled in `codeception.yml` configuration file, you should uncomment needed rows to be able
to collect code coverage. You can run your tests and collect coverage with the following command:

```
#collect coverage for all tests
vendor/bin/codecept run --coverage --coverage-html --coverage-xml

#collect coverage only for unit tests
vendor/bin/codecept run unit --coverage --coverage-html --coverage-xml

#collect coverage for unit and functional tests
vendor/bin/codecept run functional,unit --coverage --coverage-html --coverage-xml
```

You can see code coverage output under the `tests/_output` directory.


# Task API Documentation

This document describes the available API endpoints for managing tasks, including creating, updating, deleting, retrieving, and listing tasks. All endpoints return JSON responses.

---

## Endpoints

### 1. List Tasks

**Endpoint:** `GET /task/index`  

**Query Parameters:**

| Parameter     | Type    | Description |
|---------------|---------|-------------|
| showDeleted   | boolean | Include deleted tasks (optional, default: false) |
| hideDeleted   | boolean | Hide deleted tasks (optional, default: false) |
| status        | string  | Filter by status (`pending` or `completed`) |
| priority      | string  | Filter by priority (`low`, `medium`, `high`) |
| from          | string  | Filter by start date (YYYY-MM-DD) |
| to            | string  | Filter by end date (YYYY-MM-DD) |
| keyword       | string  | Search keyword in task title |
| sort          | string  | Sort by column (default: `created_at`) |
| order         | string  | Sort order (`ASC` or `DESC`) |
| page          | int     | Page number (default: 0) |
| limit         | int     | Number of tasks per page (default: 10) |

**Response:**

```json
{
  "tasks": {
    "items": [
      {
        "id": 1,
        "title": "Finish report",
        "status": "pending",
        "priority": "high",
        "due_date": "2025-09-01",
        "tags": [
          { "id": 1, "name": "Work" }
        ]
      }
    ],
    "total": 1,
    "page": 0,
    "limit": 10
  },
  "allTags": [
    { "id": 1, "name": "Work" },
    { "id": 2, "name": "Personal" }
  ]

}
```

---

### 1. View Tasks

**Endpoint:** `GET /task/view?id={id}`  

**Response:**

```json
{
  "id": 1,
  "title": "Finish report",
  "status": "pending",
  "priority": "high",
  "due_date": "2025-09-01",
  "tags": [
    { "id": 1, "name": "Work" }
  ]
}
```
---

### 1. Create Tasks

**Endpoint:** `POST /task/create`  

**Request Body:**

```json
{
  "title": "New Task",
  "status": "pending",
  "priority": "medium",
  "due_date": "2025-09-05",
  "tags": [1, 2]
}

```

**Response (Success 201):**

```json
{
  "id": 2,
  "title": "New Task",
  "status": "pending",
  "priority": "medium",
  "due_date": "2025-09-05",
  "tags": [
    { "id": 1, "name": "Work" },
    { "id": 2, "name": "Personal" }
  ]
}


```
**Response (Validation Error 422):**

```json
{
  "title": ["Title cannot be blank."]
}


```

---

### 1. Update Tasks

**Endpoint:** `PUT /task/update/?id={id}`  
**Request Body:** Same as Create
**Response (Success 200):**

```json
{
  "id": 2,
  "title": "Updated Task",
  "status": "completed",
  "priority": "high",
  "due_date": "2025-09-05",
  "tags": [
    { "id": 1, "name": "Work" }
  ]
}


```
---

### 1. Delete Tasks

**Endpoint:** `DELETE /task/delete/?id={id}`  
**Response (Success 200):**

```json
{
  "message": "Task deleted successfully"
}

```
---

### 1. Retrieve Tasks

**Endpoint:** `DELETE /task/retrieve/?id={id}`  
**Response (Success 200):**

```json
{
  "message": "Task retrieved successfully"
}

```
---

### 1. Toggle Status

**Endpoint:** `POST /task/toggle-status?id={id}`  
**Response:**

```json
{
  "id": 2,
  "title": "Updated Task",
  "status": "completed",
  "priority": "high",
  "due_date": "2025-09-05",
  "tags": [
    { "id": 1, "name": "Work" }
  ]
}

```

---

## Notes

- All requests and responses are in JSON format.
- `tags` is an array of tag IDs in create/update requests, and an array of objects in responses.
- Deleted tasks are soft-deleted using `is_deleted = true`.
- The API does **not require CSRF validation** for simplicity.
- All date fields should use `YYYY-MM-DD` format.

---

## How to Run Frontend

1. Ensure your PHP server (XAMPP, Laragon, Homestead) is running.

2. Navigate to the project root directory:
```bash
cd C:\Users\Window\OneDrive\Desktop\John\Assessments\rti-solution-llc\crud_assessment
```

3. Start the PHP built-in server for development:
```bash
php yii serve
```

4. Open your browser and go to:
```bash
http://localhost:8080

```
---

## Database Configuration for Testing

1. Create the test database:
```bash
CREATE DATABASE rti_assessment_db_test;
```

2. Configure config/test_db.php:
```bash

<?php $db = require __DIR__ . '/db.php'; // test database! Important not to run tests on production or development databases $db['dsn'] = 'mysql:host=localhost;dbname=rti_assessment_db_test'; return $db;
```

---

## Migrations for Test Database

# Run all migrations in the test database:

```bash
php yii migrate/fresh --interactive=0 --db=testDb
```

This will drop all tables and recreate them in the rti_assessment_db_test database.

# Running Unit Tests

1. Make sure phpunit and codeception are installed:
```bash
composer require --dev phpunit/phpunit codeception/codeception
```

2. Run the TaskControllerTest:
```bash
vendor/bin/codecept run unit tests/unit/controllers/TaskControllerTest.php
```

3. The console will display the test results with passed/failed assertions.

---

### Assumptions and Known Issues ###
## Assumptions
- The application is running on a local PHP server (XAMPP, Laragon, Homestead, or PHP built-in server).

- The database user has full privileges to create, drop, and modify tables in both development and test databases.

- test_db.php is correctly configured to point to rti_assessment_db_test.

- Migrations are assumed to be up-to-date; the test database will be fully rebuilt with migrate/fresh.

- All API requests/responses are expected to be in JSON format.

- Tags used in tasks are pre-existing in the tag table.

- Date fields are always formatted as YYYY-MM-DD.

- Soft-deletion is used (is_deleted = true) instead of permanently removing records.

- CSRF validation is disabled for API simplicity
## Known Issues
- Unit tests will fail if rti_assessment_db_test is not created or migrations have not been run.

- Running tests against a production or development database may corrupt data.

- Some older PHP versions (<7.4) may not be fully compatible with the current Yii2 setup.

- Soft-deleted tasks are not excluded by default in some custom queries; ensure filters are applied.

- API error responses may not be fully standardized for all edge cases.

- If phpunit or codeception are not installed, test execution will fail.

- Concurrent modifications to tasks or tags may lead to inconsistent test results.

- Some frontend pages may depend on seeded data; empty databases might show empty lists.

### Postman Collection ###
[Download RTI Solutions LLC Assessment Collection](https://drive.google.com/uc?export=download&id=146fg_V9S7MNRJ1NDSdu5D0SRjx6ndxrg)