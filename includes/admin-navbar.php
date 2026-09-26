<header class="admin-navbar">
    <div class="d-flex align-items-center gap-3">
        <button class="icon-button d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#admin-sidebar" aria-controls="admin-sidebar" aria-label="Open navigation"><i class="bi bi-list" aria-hidden="true"></i></button>
        <span class="admin-breadcrumb">Workspace <span aria-hidden="true">/</span> <strong><?= e($pageTitle) ?></strong></span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <form method="get" action="<?= e(url('admin/products.php')) ?>" class="d-none d-xl-flex"><label class="visually-hidden" for="nav-search">Search products</label><input class="form-control form-control-sm" id="nav-search" name="q" placeholder="Search products…"></form>
        <a class="store-link" href="<?= e(url('index.php')) ?>">View store <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        <button class="btn btn-outline-dark admin-theme-toggle" type="button" id="admin-theme-toggle" aria-label="Switch to dark mode" aria-pressed="false" title="Switch theme" hidden>&#9790;</button>
    </div>
</header>
