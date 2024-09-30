
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

Test sending email with running this command

```bash
  php artisan app:send-email
```
You can now access the server at http://localhost:8000/admin/login


Screenshots:

1. Login Page
<img width="1440" alt="Screenshot 2024-09-30 at 14 32 05" src="https://github.com/user-attachments/assets/c7c1d5fd-2c7d-44db-b5bb-1d73d767cbcf">
<br>
2. Dashboard Page
<img width="1440" alt="Screenshot 2024-09-30 at 14 34 01" src="https://github.com/user-attachments/assets/0cd99b11-6731-47f4-9c1c-c6315b072a05">



3. User List Page
<img width="1440" alt="Screenshot 2024-09-30 at 14 34 09" src="https://github.com/user-attachments/assets/4ab05119-39ae-4209-851a-f389cebf4958">



4. Add User Page
<img width="1440" alt="Screenshot 2024-09-30 at 14 34 15" src="https://github.com/user-attachments/assets/bfa721ce-ca36-416f-bf27-ea0288ca2372">



5. Edit User Page
<img width="1440" alt="Screenshot 2024-09-30 at 14 34 23" src="https://github.com/user-attachments/assets/2e19f150-bbf3-4f36-af3a-6b3c0562b498">

6. Reminder List Page
<img width="1440" alt="Screenshot 2024-09-30 at 14 34 26" src="https://github.com/user-attachments/assets/a63780ab-9567-4d5b-8853-f2ea7db52942">

7. Add Reminder Page
<img width="1440" alt="Screenshot 2024-09-30 at 14 34 32" src="https://github.com/user-attachments/assets/e15f3d8d-a627-4f1e-af09-eb8202a92482">

8. Reminder Detail Page
<img width="1440" alt="Screenshot 2024-09-30 at 14 34 40" src="https://github.com/user-attachments/assets/f33d4be3-569c-43b5-9080-79551c2fd746">

9. Reminder Edit Page
<img width="1440" alt="Screenshot 2024-09-30 at 14 35 08" src="https://github.com/user-attachments/assets/ff8c0834-e30a-4066-ae73-07c7fa29eab1">

10. Queue Job for sending email once the time is reaching. If the job failed sending email, it will be saved in failed_job table and will be re run once cron job start executing.
<img width="1440" alt="Screenshot 2024-09-30 at 14 35 27" src="https://github.com/user-attachments/assets/de6050d2-5832-48d4-8ee0-8c4083a7bdce">

11. Email reminder result with prefix on email subject.
<img width="1440" alt="Screenshot 2024-09-30 at 17 24 20" src="https://github.com/user-attachments/assets/7ac5f04f-d3d2-4929-a795-9bc0faad1f47">


### License

All rights reserved 

