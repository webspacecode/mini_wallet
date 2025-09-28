Mini Wallet Application

Backend Setup

Note: Use PHP 8.x or above.

Steps:

1. Install dependencies:
   composer install

2. Rename the environment file:
   cp .env.example .env

3. Add your database details in .env:
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password

4. Configure Pusher in .env:
   PUSHER_APP_ID=2056028
   PUSHER_APP_KEY=f1523796bce885f72ea3
   PUSHER_APP_SECRET=868c6a07e3bb57c9c07a
   PUSHER_HOST=
   PUSHER_PORT=443
   PUSHER_SCHEME=https
   PUSHER_APP_CLUSTER=ap2

5. Run migrations:
   php artisan migrate

6. Seed the database (if required):
   php artisan db:seed
   Note - for sample user and password from seed (Username - user<Number>@example.com Password - password123)

7. Generate Laravel application key:
   php artisan key:generate

Frontend Setup

Note: Use Node.js v20.19.5 and npm v10.8.2.

Steps:

1. Install dependencies:
   npm install

2. Add environment variables in frontend/.env:
   VITE_API_BASE_URL=http://127.0.0.1:8000
   VITE_PUSHER_APP_KEY=f1523796bce885f72ea3
   VITE_PUSHER_APP_CLUSTER=ap2

3. Build the frontend:
   npm run build

Final Step

After building the frontend, go back to the backend and run:
   php artisan serve


And visit http://127.0.0.1:8000