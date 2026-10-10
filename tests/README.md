# Validation

Run commands from the project root with PHP 8.2 or newer. In XAMPP on Windows,
replace `php` with `C:/xampp/php/php.exe` if PHP is not on your PATH.

```powershell
php tests/run.php
php tests/run.php --database
php tests/run.php --http
```

The default suite uses SQLite and test doubles. It needs `pdo_sqlite` and
`mbstring`, but does not need Apache, MariaDB, email delivery, or an AI API key.
Use `--all` to run every suite, or invoke any test script directly.

| Suite | Scripts | Requirements and cleanup |
| --- | --- | --- |
| Offline | `order-workflow.php`, `ai-assistant.php`, `ai-endpoint.php`, `profile-settings.php` | In-memory SQLite, mocked mail/provider responses, and temporary PHP sessions. No live accounts or inventory are changed. |
| Database | `catalog-install.php`, `web-catalog-import.php`, `admin-access.php`, `admin-database.php` | MariaDB configured in `config/database.php` and `pdo_mysql`. Catalog tests create and drop randomly named temporary databases; access/database tests roll back their fixtures. The database account needs CREATE and DROP permission for the catalog tests. |
| HTTP | `admin-workflows.php`, `checkout-builds.php` | A running development site, its configured database, and PHP cURL. Tests use real HTTP handlers and remove only their own disposable records and uploads in `finally` blocks. |

Run database and HTTP suites against a development installation. The runner
executes scripts sequentially in separate PHP processes and exits nonzero when
a script fails.

HTTP tests default to `http://localhost/PCForge/`. To use another local server,
set its project base URL (with or without a trailing slash):

```powershell
$env:PCFORGE_TEST_URL = 'http://localhost:8080/PCForge/'
php tests/run.php --http
```

`admin-workflows.php --preview` and `checkout-builds.php --preview` also save
local HTML snapshots. These generated previews are ignored by Git.
