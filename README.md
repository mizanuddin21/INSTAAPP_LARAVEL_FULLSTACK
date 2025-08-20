# Back-End Service For Invoice User

# Description
This project is a copy cat of an Instagram app with cheap and limited feature. Using laravel 4.5.1 and php version 7.4.33

# Prerequisites
Before running the project, ensure you have the following installed:

1. Laravel 4.5.1.

2. PHP (v7.4.33)

3. PostgreSQL (v8.13.1): An open-source relational database system.
    Download from: PostgreSQL [Download](https://www.postgresql.org/download/).
    Ensure PostgreSQL is running on your machine.

# Setup and Installation
Follow these steps to clone and run the project locally:
1. Clone the Repository
Start by cloning the repository to your local machine:
<div class="code-container">
  <pre id="command-text">
    git clone https://github.com/your-username/your-project-name.git
    cd your-project-name
  </pre>
</div>

2. Install Dependencies
Run the following command to install all project dependencies:
<div class="code-container">
  <pre id="command-text">
    composert install
  </pre>
</div>
This will install the required dependencies listed in composer.json.<br><br>

3. Set Up PostgreSQL
Ensure you have PostgreSQL running locally. You can use a database management tool like [pgAdmin](https://www.pgadmin.org/) or command-line tools to create a database for your project.
- Create a Database: Create a new database using the PostgreSQL command line or GUI:
<div class="code-container">
  <pre id="command-text">
    createdb your_database_name
  </pre>
</div>

- Create a table:
To make sure the backend service works, you need to create tables that run for this backend. Open your SQL tools or similar tools, and then run this command:
<div class="code-container">
  <pre id="command-text">
     php artisan migrate
  </pre>
</div>

- Set Up Environment Variables: Create a .env file in the root directory of the project to configure database connection settings. Example:
<div class="code-container">
  <pre id="command-text">
    PORT=your_port -> for localhost port
    DB_HOST=localhost
    DB_PORT=5432
    DB_USER=your_postgres_user
    DB_PASSWORD=your_postgres_password
    DB_NAME=your_database_name
  </pre>
</div>
Replace the values with your actual PostgreSQL credentials.

4. Run the Application
Once you have installed dependencies and set up the database, run the application from the terminal using:
<div class="code-container">
  <pre id="command-text">
    php artisan serve
  </pre>
</div>
This will start the app. By default, it will be running on http://127.0.0.1:8000. <br><br

You can test if everything is working by visiting the endpoint http://127.0.0.1:8000 in your browser or using tools like Postman.

5. API Usage with Postman
   - Import Postman Collection
     * Open Postman.
     * Click on the "Import" button in the top left corner.
     * Choose postman collection inside folder Postman.
     * Click "Import" to load the collection into Postman.
   - Set Up Postman Environment Variables
     * Go to the "Environment" dropdown in the top-right corner of Postman.
     * Select "Manage Environments."
     * Click "Add" to create a new environment and name it (e.g., base_url_widatech).
   - Run the APIs

