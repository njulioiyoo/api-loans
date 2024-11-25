# Loan Application API

This application provides a simple API for managing loan applications with validation features. Follow the steps below to set up, run the application, and execute unit tests.

---

## Requirements

-   PHP 8.1 or higher
-   Composer
-   A web server (e.g., Apache, Nginx, or PHP's built-in server)
-   PHPUnit (for unit testing)

---

## Installation

1. **Clone the repository**:
    ```bash
    git clone <repository-url>
    cd <repository-folder>
    ```
2. **Install dependencies**:
    ```bash
     composer install
    ```

## Running the Application

1. **Start the server**:

    Use PHP's built-in server for local development:

    ```bash
    php -S localhost:8000 -t public
    ```

2. **Access the API:**:

    Open your browser or API testing tool (like Postman) and navigate to:

    ```bash
     http://localhost:8000
    ```

---

## Endpoints

1. **POST** `/loan/apply` Submit a loan application.
    ```bash
    {
        "name": "John Doe",
        "ktp": "1234561501851238",
        "loan_amount": 5000,
        "loan_period": 12,
        "loan_purpose": "vacation",
        "date_of_birth": "1985-01-15",
        "sex": "male"
    }
    ```

## Running Unit Tests

1. Run all tests:
    ```bash
    vendor/bin/phpunit
    ```
2. Run tests with descriptive output:
    ```bash
    vendor/bin/phpunit --testdox
    ```
3. Run a specific test file:
    ```bash
    vendor/bin/phpunit tests/LoanRepositoryTest.php
    ```

---

## Directory Structure

-   src/ - Application logic (e.g., repositories and services).
-   public/ - Public-facing files (entry point for the API).
-   storage/ - File-based storage for loan applications.
-   tests/ - Unit tests for the application.

---

## Troubleshooting

-   Composer issues:

    Ensure Composer is installed and up to date:

    ```bash
    composer self-update

    ```

-   Port conflicts:

    If port `8000` is in use, specify another port when starting the server:

    ```bash
    php -S localhost:8080 -t public
    ```

---

## Contributing

Feel free to fork the repository, create feature branches, and submit pull requests!

---

## License

This project is licensed under the MIT License.
