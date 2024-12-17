## Getting Started for E-Vote Central System

*Run the commands below to install the composer and view*

- composer install
- npm i vue-loader
- npm run build

### Set up database
- replace the .env.example to .env
- php artisan migrate

### Can seeder the data into your database
- php artisan db:seed

### Run the commands below to use the print pdf function and import excel function
- composer require barryvdh/laravel-dompdf
- composer require maatwebsite/excel

### Sample Excel upload
- in root file name as Terms sample.xlsx

### Run the command below to use the print pdf function
- composer require barryvdh/laravel-dompdf

### Run the command below to use the storage link function
- php artisan storage:link  