# Hotel Management (Symfony 7)

Quick setup notes for local development (Windows / XAMPP):

1. Enable pdo_mysql in XAMPP

-   Edit `C:\xampp\php\php.ini` (or the CLI `php.ini` from `php --ini`) and remove the leading `;` on:
    -   `extension=pdo_mysql`
    -   (if needed) `extension=pdo`
-   Restart Apache / XAMPP services.

2. Set `DATABASE_URL`

-   Edit `.env` and set (example):

```
DATABASE_URL="mysql://root:YOUR_PASSWORD@127.0.0.1:3306/hotel_db?serverVersion=8.0"
```

3. Create DB and run migrations

```powershell
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

4. Load demo data (fixtures) or run the demo command

Using fixtures (requires `doctrine/doctrine-fixtures-bundle`):

```powershell
php bin/console doctrine:fixtures:load
```

Or use the provided CLI command:

```powershell
php bin/console app:create-demo-data
```

5. Start server

```powershell
symfony server:start
# or
php -S 127.0.0.1:8000 -t public
```

Demo accounts:

-   Admin: admin@example.com / adminpass
-   Client: client@example.com / clientpass

## Testing

-   **Local tests:** run the PHPUnit suite using the test environment (SQLite):

```powershell
php -d memory_limit=2G bin/phpunit --colors=always
```

-   **CI:** this project includes a GitHub Actions workflow that runs the same command on PHP 8.2 using the repository's `.env.test`/SQLite setup. No external DB service is required.

If you need to run tests using a different DB locally, set `DATABASE_URL` in your environment or `.env.test` accordingly.
