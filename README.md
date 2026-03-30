
# DataPet

DataPet is a Web application built with Laravel.

**Made by:**
Mariana Carrasquilla, Juan Manuel Gallo, David García and Samuel LLano.

# Requirements

- PHP 8.2 or higher
- Composer
- npm
- XAMPP (for Apache and MySQL management)

# Clone the Repository (inside XAMPP htdocs)

1. Open a terminal and move to the htdocs folder:

 ``cd C:\xampp\htdocs ``

2. Clone the repository into htdocs:

``git clone URL``

3. Enter the project folder:

``cd datapet``

# Project Initialization

1. Create the environment file:

``copy .env.example .env``

2. Install PHP dependencies:

``composer install``

3. Install frontend dependencies:

``npm install``

4. Generate the Laravel application key:

``php artisan key:generate``

5. Configure MySQL credentials in the environment file( .env) with your XAMPP setup:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=datapet
DB_USERNAME=root
DB_PASSWORD=
```

6. Run database migrations:

``php artisan migrate``

<br>

# Run the Project

Run the project with the next command: 

``php artisan serve``

