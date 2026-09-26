<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/catalog.php';
require_admin();

// Detailed errors stay in the server log. Roll back partially completed writes.
set_exception_handler(function (Throwable $error): void {
    if (db()->inTransaction()) db()->rollBack();
    error_log('PCForge admin: ' . $error->getMessage());
    http_response_code($error instanceof InvalidArgumentException ? 400 : 500);
    echo '<div role="alert">' . e($error instanceof InvalidArgumentException ? $error->getMessage() : 'This page could not be loaded. Please try again.') . ' <a href="' . e(url('admin/dashboard.php')) . '">Return to dashboard</a></div>';
});

function admin_query(string $sql, array $values = []): PDOStatement
{
    $statement = db()->prepare($sql);
    $statement->execute($values);
    return $statement;
}

function admin_text(array $source, string $key, string $default = ''): string
{
    return is_string($source[$key] ?? null) ? trim($source[$key]) : $default;
}

function admin_id($value): int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1, 'max_range'=>2147483647]]);
    if ($id === false) throw new InvalidArgumentException('Invalid record ID.');
    return $id;
}

function admin_record(string $sql, array $values): array
{
    $record = admin_query($sql, $values)->fetch();
    if (!$record) { http_response_code(404); exit('Record not found.'); }
    return $record;
}

function admin_product(string $type, int $id): array
{
    $type = component_type($type);
    return admin_record("SELECT * FROM `$type` WHERE id = ?", [$id]);
}

function admin_catalog_sql(): string
{
    $parts = [];
    foreach (component_categories() as $table => $label) {
        $model = in_array($table, ['cpu','gpu'], true) ? 'short_name' : "''";
        $parts[] = "SELECT '$table' AS category, id, name, $model AS model, brand, price, stock, status, image_url, created_at, updated_at FROM `$table`";
    }
    return '(' . implode(' UNION ALL ', $parts) . ') AS catalog';
}

function admin_money($amount, ?string $currency = null): string
{
    return $amount === null ? 'Unknown' : ($currency ?? store_settings()['currency']) . ' ' . number_format((float) $amount, 2);
}

function admin_badge(string $status): string
{
    $style = match ($status) {
        'active','completed','paid','in stock','compatible' => 'success',
        'cancelled','disabled','inactive','out of stock','incompatible' => 'danger',
        'pending','low stock','warning' => 'warning',
        'processing' => 'info', default => 'secondary',
    };
    return '<span class="badge text-bg-' . $style . '">' . e(ucfirst($status)) . '</span>';
}

function admin_stock_status($stock): string
{
    if ($stock === null) return 'unknown';
    if ((int) $stock === 0) return 'out of stock';
    return (int) $stock <= (int) store_settings()['low_stock_threshold'] ? 'low stock' : 'in stock';
}

function admin_start(string $title, string $subtitle = ''): void
{
    $pageTitle = $title;
    require __DIR__ . '/admin-header.php';
    echo '<div class="page-heading"><div><p class="eyebrow">PCFORGE / ADMINISTRATION</p><h1>' . e($title) . '</h1><p class="text-muted mb-0">' . e($subtitle) . '</p></div></div>';
}

function admin_end(): void
{
    echo '</main>';
    $adminLayout = true;
    require __DIR__ . '/footer.php';
}

function admin_error(Throwable $error): string
{
    if ($error instanceof InvalidArgumentException) return $error->getMessage();
    error_log('PCForge admin update: ' . $error->getMessage());
    return 'Something went wrong while saving. Please try again.';
}

function admin_alert(string $message): void
{
    if ($message !== '') echo '<div class="alert alert-danger" role="alert">' . e($message) . '</div>';
}

function admin_pagination(int $total): array
{
    $pages = max(1, (int) ceil($total / 20));
    $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]) ?: 1;
    $page = min($pages, $page);
    return [$page, ($page - 1) * 20, $pages];
}

function admin_pager(int $page, int $pages, int $total): void
{
    echo '<nav class="admin-pager" aria-label="Pagination"><span>' . $total . ' records · Page ' . $page . ' of ' . $pages . '</span><div>';
    foreach (['Previous'=>max(1,$page-1), 'Next'=>min($pages,$page+1)] as $label=>$target) {
        if ($target === $page) continue;
        $query = $_GET;
        $query['page'] = $target;
        echo '<a class="btn btn-outline-secondary btn-sm ms-2" href="?' . e(http_build_query($query)) . '">' . $label . '</a>';
    }
    echo '</div></nav>';
}

function admin_empty(int $columns, string $text): void
{
    echo '<tr><td colspan="' . $columns . '" class="empty-state">' . e($text) . '</td></tr>';
}

function admin_image($stored): ?string
{
    if (!is_string($stored) || $stored === '') return null;
    if (filter_var($stored, FILTER_VALIDATE_URL) && strtolower(parse_url($stored, PHP_URL_SCHEME) ?? '') === 'https') return $stored;
    return is_file(__DIR__ . '/../assets/images/' . basename($stored)) ? url('assets/images/' . rawurlencode(basename($stored))) : null;
}

function admin_product_link(array $product, string $action = 'view'): string
{
    return url('admin/product-' . $action . '.php?type=' . rawurlencode($product['category']) . '&id=' . (int) $product['id']);
}

function admin_product_filters(): array
{
    $where = []; $values = [];
    $search = admin_text($_GET, 'q');
    if ($search !== '') {
        $where[] = '(name LIKE ? OR brand LIKE ? OR model LIKE ?)';
        array_push($values, "%$search%", "%$search%", "%$search%");
    }
    foreach (['category','brand','status'] as $field) {
        $value = admin_text($_GET, $field);
        if ($value !== '') { $where[] = "$field = ?"; $values[] = $value; }
    }
    foreach (['min'=>' >= ', 'max'=>' <= '] as $field=>$operator) {
        $value = admin_text($_GET, $field);
        if ($value !== '') { $where[] = 'price' . $operator . '?'; $values[] = cents_decimal(price_cents($value)); }
    }
    $stock = admin_text($_GET, 'filter');
    if ($stock === 'low') { $where[] = 'stock > 0 AND stock <= ?'; $values[] = (int) store_settings()['low_stock_threshold']; }
    if ($stock === 'out') $where[] = 'stock = 0';
    if ($stock === 'unknown') $where[] = 'stock IS NULL';
    if ($stock === 'in') { $where[] = 'stock > ?'; $values[] = (int) store_settings()['low_stock_threshold']; }
    return [$where ? ' WHERE ' . implode(' AND ', $where) : '', $values];
}

function admin_filter_form(): void
{
    $brands = admin_query('SELECT DISTINCT brand FROM ' . admin_catalog_sql() . " WHERE brand IS NOT NULL AND brand <> '' ORDER BY brand")->fetchAll(PDO::FETCH_COLUMN);
    echo '<form method="get" class="admin-filters"><div><label for="q">Search products</label><input class="form-control" id="q" name="q" placeholder="Name, brand or model" value="' . e(admin_text($_GET,'q')) . '"></div>';
    foreach (['category'=>component_categories(), 'brand'=>array_combine($brands,$brands) ?: [], 'status'=>['active'=>'Active','inactive'=>'Inactive'], 'filter'=>['in'=>'In stock','low'=>'Low stock','out'=>'Out of stock','unknown'=>'Unknown stock']] as $key=>$options) {
        echo '<div><label for="' . $key . '">' . e(ucfirst($key === 'filter' ? 'stock' : $key)) . '</label><select class="form-select" id="' . $key . '" name="' . $key . '"><option value="">All</option>';
        foreach ($options as $value=>$label) echo '<option value="' . e((string)$value) . '" ' . (admin_text($_GET,$key) === (string)$value ? 'selected' : '') . '>' . e($label) . '</option>';
        echo '</select></div>';
    }
    foreach (['min'=>'Min price','max'=>'Max price'] as $key=>$label) echo '<div><label for="' . $key . '">' . $label . '</label><input class="form-control" type="number" min="0" step="0.01" id="' . $key . '" name="' . $key . '" value="' . e(admin_text($_GET,$key)) . '"></div>';
    echo '<button class="btn btn-dark">Filter</button><a class="btn btn-outline-secondary" href="' . e(basename($_SERVER['SCRIPT_NAME'])) . '">Clear</a></form>';
}
