
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

  Please modify .env file with your local environment for DB and Mail service
```

Run this command to generate Laravel key

```bash
  php artisan key:generate
```

Run the database migrations

```bash
  php artisan migrate --seed
```

Start the local development server

```bash
  php artisan serve
```
You can now access the server at http://localhost:8000/admin/login

### License

All rights reserved 

