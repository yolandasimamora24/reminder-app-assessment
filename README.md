
# Event Reminder App
A laravel-based Web Application and API for Event Reminder App.

### Setup Locally

Clone the repository

```bash
  git clone https://github.com/yolandasimamora24/reminder-app-assessment.git
```

Go to the project directory

```bash
  cd reminder-app-assessment
```

Install all the dependencies using composer

```bash
  composer install
```

Copy the example env file and make the required configuration changes in the .env file

```bash
  cp .env.example .env
```

Run the database migrations

```bash
  php artisan migrate
```

Start the local development server

```bash
  php artisan serve
```
You can now access the server at http://localhost:8000


### Running Tests

To run tests, run the following command:

**Mac:**

```bash
./vendor/bin/pest             # To run all tests
./vendor/bin/pest --filter TestFileName  # To run a specific test file
```

**Windows:**
```bash
.\vendor\bin\pest             # To run all tests
.\vendor\bin\pest --filter TestFileName  # To run a specific test file
```

### License

All rights reserved 

