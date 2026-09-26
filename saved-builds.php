<?php

require_once __DIR__ . '/includes/functions.php';
require_login('saved-builds.php');
$userId = (int) auth_user()['id'];
require_once __DIR__ . '/includes/saved-builds.php';

$error = '';
$notice = $_SESSION['saved_build_notice'] ?? '';
unset($_SESSION['saved_build_notice']);
$justSaved = !empty($_SESSION['build_save_success']);
unset($_SESSION['build_save_success']);
$currentIds = [];
$currentPreview = null;
$savedBuilds = [];
$count = 0;
$pageCount = 1;
$page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
$buildName = is_string($_POST['build_name'] ?? null) ? $_POST['build_name'] : '';

try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        csrf_verify();
        $action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';
        if ($action === 'save') {
            $name = savedBuildName($_POST['build_name'] ?? null);
            $ids = savedBuildIds($_SESSION['build'] ?? []);
            if (!$ids) throw new InvalidArgumentException('Choose at least one component in the builder before saving.');
            $preview = savedBuildPreview($ids);
            if ($preview['unavailable']) throw new InvalidArgumentException('Replace unavailable components in your current build before saving.');
            savedBuildCreate(db(), $userId, $name, $ids);
            $_SESSION['saved_build_notice'] = 'Build saved. You can return to it any time.';
            $_SESSION['build_save_success'] = true;
        } elseif (in_array($action, ['load', 'rename', 'delete'], true)) {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($id === false) throw new InvalidArgumentException('Choose a valid saved build.');
            // Every read and change is restricted to the signed-in owner.
            $query = db()->prepare('SELECT build_data FROM saved_builds WHERE id = :id AND user_id = :user');
            $query->execute(['id' => $id, 'user' => $userId]);
            $saved = $query->fetch();
            if (!$saved) throw new InvalidArgumentException('That saved build is no longer available.');

            if ($action === 'load') {
                $ids = savedBuildIds(json_decode($saved['build_data'], true));
                if (!$ids) throw new InvalidArgumentException('This saved build has no components.');
                $_SESSION['build'] = $ids;
                redirect('builder.php');
            } elseif ($action === 'rename') {
                $query = db()->prepare('UPDATE saved_builds SET build_name = :name WHERE id = :id AND user_id = :user');
                $query->execute(['name' => savedBuildName($_POST['build_name'] ?? null), 'id' => $id, 'user' => $userId]);
                $_SESSION['saved_build_notice'] = 'Build renamed.';
            } else {
                $query = db()->prepare('DELETE FROM saved_builds WHERE id = :id AND user_id = :user');
                $query->execute(['id' => $id, 'user' => $userId]);
                $_SESSION['saved_build_notice'] = 'Saved build removed.';
            }
        } else {
            throw new InvalidArgumentException('Choose one of the available build actions.');
        }
        // Redirect after successful writes so refreshing does not repeat them.
        redirect('saved-builds.php');
    }
} catch (InvalidArgumentException $exception) {
    $error = $exception->getMessage();
} catch (PDOException $exception) {
    error_log('PCForge saved build action failed: ' . $exception->getMessage());
    $error = 'Your build could not be updated. Please try again later.';
}

$loadFailed = false;
try {
    try {
        $currentIds = savedBuildIds($_SESSION['build'] ?? []);
        $currentPreview = savedBuildPreview($currentIds);
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    }
    $query = db()->prepare('SELECT COUNT(*) FROM saved_builds WHERE user_id = :user');
    $query->execute(['user' => $userId]);
    $count = (int) $query->fetchColumn();
    $pageCount = max(1, (int) ceil($count / 6));
    $page = min($page, $pageCount);
    $offset = ($page - 1) * 6;
    $query = db()->prepare("SELECT id, build_name, build_data, created_at FROM saved_builds WHERE user_id = :user ORDER BY id DESC LIMIT 6 OFFSET $offset");
    $query->execute(['user' => $userId]);
    $savedBuilds = $query->fetchAll();
    foreach ($savedBuilds as &$saved) {
        try {
            $ids = savedBuildIds(json_decode($saved['build_data'], true));
            $saved['preview'] = $ids ? savedBuildPreview($ids) : null;
        } catch (InvalidArgumentException $exception) {
            $saved['preview'] = null;
        }
    }
    unset($saved);
} catch (PDOException $exception) {
    error_log('PCForge saved builds load failed: ' . $exception->getMessage());
    http_response_code(503);
    $loadFailed = true;
    $error = 'Saved builds could not be loaded. Please try again later.';
}

$pageTitle = 'Saved Builds';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main id="main-content" tabindex="-1">
    <style>
        .saved-build-page { padding-inline: 1rem; }
        .saved-build-panel { padding: clamp(1.25rem, 3vw, 2rem); border: 1px solid var(--forge-border); border-radius: 1.25rem; background: var(--forge-surface-raised); }
        .saved-build-intro { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center; margin: 2rem 0 3rem; }
        .saved-build-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.5rem; align-items: start; }
        .saved-build-card { min-width: 0; display: flex; flex-direction: column; }
        .saved-build-card h3 { overflow-wrap: anywhere; }
        .saved-build-images { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.65rem; margin: 1.25rem 0; }
        .saved-build-image { min-width: 0; text-align: center; color: var(--forge-muted); font-size: 0.7rem; }
        .saved-build-image-box { position: relative; display: grid; place-items: center; aspect-ratio: 4 / 3; margin-bottom: 0.4rem; border-radius: 0.65rem; overflow: hidden; background: var(--forge-surface); }
        .saved-build-image img { position: absolute; inset: 0; width: 100%; height: 100%; padding: 0.5rem; object-fit: contain; background: var(--forge-surface); }
        .saved-build-part { display: grid; grid-template-columns: 6rem minmax(0, 1fr); gap: 0.75rem; padding: 0.65rem 0; border-bottom: 1px solid var(--forge-border); font-size: 0.85rem; overflow-wrap: anywhere; }
        .saved-build-part dt { color: var(--forge-muted); font-weight: 400; }
        .saved-build-part dd { margin: 0; }
        .saved-build-total { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-top: auto; padding: 1.25rem 0; }
        .saved-build-manage { margin-top: 1rem; font-size: 0.85rem; }
        .saved-build-manage summary { cursor: pointer; color: var(--forge-muted); }
        @media (max-width: 767.98px) {
            .saved-build-intro, .saved-build-grid { grid-template-columns: minmax(0, 1fr); }
            .saved-build-intro { gap: 1rem; }
            .saved-build-images { gap: 0.4rem; }
        }
    </style>
    <div class="container section-padding saved-build-page">
        <p class="small fw-semibold text-uppercase text-secondary">Your build library</p>
        <h1>Keep your next PC in mind.</h1>
        <p class="lead text-secondary">Save your ideas, then pick up where you left off.</p>
        <?php if ($notice): ?><p class="alert alert-secondary" role="status"><?= e($notice) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="alert alert-warning" role="alert"><?= e($error) ?></p><?php endif; ?>

        <?php if (!$loadFailed): ?>
            <section class="saved-build-panel saved-build-intro" aria-labelledby="current-build-heading">
                <div>
                    <h2 class="h4" id="current-build-heading">Save your current build</h2>
                    <?php if ($currentIds && $currentPreview): ?>
                        <p class="text-secondary mb-0"><?= count($currentIds) ?> of 8 parts selected. You can save a build while it is still in progress.</p>
                        <?php savedBuildImages($currentPreview['parts']); ?>
                        <a href="<?= e(url('build-summary.php')) ?>">Review current build &rarr;</a>
                    <?php else: ?>
                        <p class="text-secondary">Start with a few components, then come back to save your idea.</p>
                        <a class="btn btn-primary" href="<?= e(url('builder.php')) ?>">Start a build &rarr;</a>
                    <?php endif; ?>
                </div>
                <?php if ($currentIds && $currentPreview && !$currentPreview['unavailable']): ?>
                    <form method="post" action="<?= e(url('saved-builds.php')) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save">
                        <label class="form-label fw-semibold" for="save-build-name">Build name</label>
                        <input class="form-control" id="save-build-name" name="build_name" required maxlength="100" placeholder="e.g. My everyday workstation" value="<?= e(($_POST['action'] ?? '') === 'save' ? $buildName : '') ?>">
                        <p class="small text-secondary mt-2">Saves a separate copy of your selected parts.</p>
                        <button class="forge-save-btn w-100" type="submit" data-build-save <?= $justSaved ? 'data-save-success' : '' ?> aria-label="Save build">
                            <span class="forge-save-label forge-save-idle" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h12a1 1 0 0 1 1 1v15l-7-4-7 4V5a1 1 0 0 1 1-1z"/></svg>Save build</span>
                            <span class="forge-save-label forge-save-done" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 4h12a1 1 0 0 1 1 1v15l-7-4-7 4V5a1 1 0 0 1 1-1z"/></svg>Saved</span>
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-secondary mb-0"><?= $currentPreview && $currentPreview['unavailable'] ? 'Some selected parts are unavailable. Replace them in the builder before saving.' : 'Your saved builds belong to your account, so you can return to them after signing in again.' ?></p>
                <?php endif; ?>
            </section>

            <section aria-labelledby="saved-builds-heading">
                <h2 class="h3" id="saved-builds-heading">Saved builds <span class="text-secondary">(<?= $count ?>)</span></h2>
                <p class="text-secondary small mb-4">Prices use the current catalogue. Opening a saved build replaces your current builder selection; saved copies stay here.</p>
                <?php if (!$savedBuilds): ?>
                    <div class="saved-build-panel text-center py-5">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 5V3h10v2M8 11h8m-8 4h5"/></svg>
                        <h3 class="h5 mt-3">Room for your first idea.</h3>
                        <p class="text-secondary mb-0">Name your current build above to add it to your library.</p>
                    </div>
                <?php else: ?>
                    <div class="saved-build-grid">
                        <?php foreach ($savedBuilds as $saved): ?>
                            <?php $preview = $saved['preview']; ?>
                            <article class="saved-build-panel saved-build-card" aria-labelledby="build-<?= (int) $saved['id'] ?>">
                                <p class="small text-secondary mb-2">Saved <?= e(date('M j, Y', strtotime($saved['created_at']))) ?></p>
                                <h3 class="h4 mb-0" id="build-<?= (int) $saved['id'] ?>"><?= e($saved['build_name']) ?></h3>
                                <?php if ($preview): ?>
                                    <?php savedBuildImages($preview['parts']); ?>
                                    <details>
                                        <summary>View <?= count($preview['parts']) ?> selected parts</summary>
                                        <dl class="mb-0">
                                            <?php foreach ($preview['parts'] as $category => $product): ?>
                                                <div class="saved-build-part"><dt><?= e($categories[$category]) ?></dt><dd><?= e($product['name'] ?? 'No longer available') ?></dd></div>
                                            <?php endforeach; ?>
                                        </dl>
                                    </details>
                                    <?php if ($preview['unavailable'] || $preview['missing_prices']): ?><p class="small text-secondary mt-3">Some parts are unavailable or missing prices. The subtotal is incomplete.</p><?php endif; ?>
                                    <div class="saved-build-total">
                                        <span class="small text-secondary"><?= $preview['unavailable'] || $preview['missing_prices'] ? 'Known-price subtotal' : 'Current parts total' ?></span>
                                        <strong class="fs-4"><?= e(savedBuildPrice($preview['cents'])) ?></strong>
                                    </div>
                                    <form method="post" action="<?= e(url('saved-builds.php')) ?>">
                                        <?= csrf_field() ?><input type="hidden" name="action" value="load"><input type="hidden" name="id" value="<?= (int) $saved['id'] ?>">
                                        <button class="btn btn-primary w-100" type="submit" aria-label="<?= e('Open ' . $saved['build_name'] . ' in builder') ?>">Open in builder &rarr;</button>
                                    </form>
                                <?php else: ?>
                                    <p class="text-secondary my-4">This build has no readable components. Save a new copy from the builder.</p>
                                <?php endif; ?>
                                <details class="saved-build-manage">
                                    <summary>Manage build</summary>
                                    <form method="post" action="<?= e(url('saved-builds.php')) ?>" class="mt-3">
                                        <?= csrf_field() ?><input type="hidden" name="action" value="rename"><input type="hidden" name="id" value="<?= (int) $saved['id'] ?>">
                                        <label class="form-label" for="rename-<?= (int) $saved['id'] ?>">Build name</label>
                                        <input class="form-control" id="rename-<?= (int) $saved['id'] ?>" name="build_name" required maxlength="100" value="<?= e($saved['build_name']) ?>">
                                        <button class="btn btn-sm btn-outline-dark mt-2" type="submit">Rename</button>
                                    </form>
                                    <form method="post" action="<?= e(url('saved-builds.php')) ?>" class="border-top mt-3 pt-3">
                                        <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $saved['id'] ?>">
                                        <p class="small text-secondary">Delete this saved copy permanently. Your current builder selection will stay as it is.</p>
                                        <button class="btn btn-sm btn-outline-dark" type="submit" aria-label="<?= e('Delete saved build ' . $saved['build_name']) ?>">Delete saved copy</button>
                                    </form>
                                </details>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($pageCount > 1): ?>
                        <nav class="d-flex flex-wrap align-items-center justify-content-center gap-3 mt-4" aria-label="Saved builds pages">
                            <?php if ($page > 1): ?><a class="btn btn-outline-dark" href="<?= e(url('saved-builds.php?page=' . ($page - 1))) ?>">Previous</a><?php endif; ?>
                            <span class="small">Page <?= $page ?> of <?= $pageCount ?></span>
                            <?php if ($page < $pageCount): ?><a class="btn btn-outline-dark" href="<?= e(url('saved-builds.php?page=' . ($page + 1))) ?>">Next</a><?php endif; ?>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</main>
<script>
    (() => {
        const button = document.querySelector('[data-build-save]');
        if (!button) return;
        if (button.hasAttribute('data-save-success')) {
            requestAnimationFrame(() => requestAnimationFrame(() => {
                button.classList.add('is-saved');
                button.setAttribute('aria-label', 'Saved');
            }));
        }
        button.form.addEventListener('input', () => {
            button.classList.remove('is-saved');
            button.setAttribute('aria-label', 'Save build');
        });
        button.form.addEventListener('submit', () => {
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
        });
    })();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
