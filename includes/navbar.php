<?php

// Include this after header.php, which loads e() and url().
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');

$navLinks = [
    'index.php' => 'Home',
    'products.php' => 'Components',
    'prebuilts.php' => 'Prebuilt PCs',
    'compare.php' => 'Compare',
];

$currentUser = auth_user();

?>
<!-- Scoped styles keep this navigation update in one file. -->
<style>
    @view-transition { navigation: auto; }

    ::view-transition-old(root),
    ::view-transition-new(root) {
        animation-duration: 180ms;
    }

    .forge-nav {
        position: sticky;
        top: 1rem;
        z-index: 1020;
        width: calc(100% - 2rem);
        max-width: 1080px;
        margin: 1rem auto;
        padding: 0.65rem 1rem;
        border: 1px solid #dedede;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.9);
        -webkit-backdrop-filter: blur(14px) saturate(130%);
        backdrop-filter: blur(14px) saturate(130%);
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.09);
    }

    .forge-nav .navbar-brand {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-right: 1.5rem;
        font-size: 1rem;
        letter-spacing: -0.025em;
        color: #171717;
    }

    .forge-nav-mark {
        display: grid;
        place-items: center;
        width: 1.9rem;
        height: 1.9rem;
        border-radius: 0.5rem;
        background: #171717;
        color: #ffffff;
        font-size: 0.8rem;
    }

    .forge-nav-logo-image {
        display: block;
        width: auto;
        height: 2rem;
        max-width: 150px;
        object-fit: contain;
    }

    .forge-nav .nav-link {
    display: flex;
    align-items: center;
    justify-content: center;

    width: auto;
    padding: 0.5rem 0.5rem;

    border-radius: 0.6rem;
    color: #606060;
    font-size: 0.875rem;
    font-weight: 600;
    white-space: nowrap;
    position: relative;
    z-index: 1;

    transition: color 180ms ease;
    }

    .forge-nav .nav-link:hover,
    .forge-nav .nav-link:focus-visible {
        color: #171717;
    }

    .forge-nav .nav-link.active {
        color: #171717;
        font-weight: 600;
        text-decoration: none;
    }

    .forge-nav-links {
        position: relative;
    }

.forge-nav-indicator {
    position: absolute;
    z-index: 0;
    top: 0;
    left: 0;
    height: 100%;
    border: 1px solid #dedede;
    border-radius: 0.6rem;
    background: transparent;
    opacity: 0;
    pointer-events: none;
    transform: translateX(0);

    transition:
    opacity 180ms ease,
    transform 420ms cubic-bezier(.34, 1.2, .5, 1);

    view-transition-name: forge-nav-indicator;
}



    .forge-nav .btn {
        padding: 0.55rem 0.9rem;
        border-radius: 0.6rem;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .forge-nav .nav-account-toggle {
        color: #606060;
        font-weight: 600;
        text-decoration: none;
    }

    .forge-nav .nav-account-toggle:hover,
    .forge-nav .nav-account-toggle:focus-visible {
        color: #171717;
    }

    .forge-nav .dropdown-menu {
        min-width: 12rem;
        margin-top: 0.5rem;
        padding: 0.45rem;
        border: 1px solid #dedede;
        border-radius: 0.8rem;
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }

    .forge-nav .dropdown-item {
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }

    .forge-nav .dropdown-item:active,
    .forge-nav .dropdown-item:focus,
    .forge-nav .dropdown-item:hover {
        background: #f1f1f1;
        color: #171717;
    }

    .forge-nav .navbar-toggler {
        padding: 0.35rem 0.5rem;
        border-color: #dedede;
        font-size: 1rem;
    }

    .forge-nav .forge-theme-toggle {
        display: inline-grid;
        place-items: center;
        width: 2.35rem;
        height: 2.35rem;
        padding: 0;
        font-size: 1.15rem;
        line-height: 1;
    }

    .forge-nav-mobile-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .forge-theme-toggle-mobile {
        display: none !important;
    }

    @media (max-width: 1199.98px) {
        .forge-theme-toggle-mobile {
            display: inline-grid !important;
        }

        .forge-nav .navbar-collapse {
            max-height: calc(100dvh - 7rem);
            overflow-y: auto;
        }

        .forge-nav .navbar-nav {
            gap: 0.25rem;
            padding: 0.75rem 0;
        }

        .forge-nav-indicator { display: none; }

        .forge-nav-actions {
            flex-wrap: wrap;
            padding-top: 0.75rem;
            border-top: 1px solid #dedede;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .forge-nav .nav-link,
        .forge-nav-indicator {
            transition: none;
        }
    }
</style>

<nav class="forge-nav navbar navbar-expand-xl" aria-label="Main navigation">
    <div class="container-fluid p-0">
        <a class="navbar-brand fw-bold" href="<?= e(url('index.php')) ?>">
            <img class="forge-nav-logo-image" src="<?= e(url('assets/images/logo_nobg.png')) ?>" alt="PCForge">
            <span>PCForge</span>
        </a>

        <div class="forge-nav-mobile-actions">
            <button class="btn btn-outline-dark forge-theme-toggle forge-theme-toggle-mobile" type="button" data-theme-toggle-mobile aria-label="Switch to dark mode" aria-pressed="false" title="Switch theme">&#9790;</button>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <button class="btn btn-outline-dark forge-theme-toggle d-none d-xl-inline-grid me-xl-2 flex-shrink-0" type="button" data-theme-toggle aria-label="Switch to dark mode" aria-pressed="false" title="Switch theme">&#9790;</button>
            <ul class="forge-nav-links navbar-nav mx-xl-auto gap-xl-1">
                <span class="forge-nav-indicator" aria-hidden="true"></span>
                <?php foreach ($navLinks as $path => $label): ?>
                    <?php $isActive = $currentPage === $path; ?>
                    <li class="nav-item">
                        <a
                            class="nav-link<?= $isActive ? ' active' : '' ?>"
                            href="<?= e(url($path)) ?>"
                            <?= $isActive ? 'aria-current="page"' : '' ?>
                        >
                            <?= e($label) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="forge-nav-actions d-flex align-items-center gap-2 ms-xl-3">
                <a class="nav-link<?= $currentPage === 'cart.php' ? ' active' : '' ?>"
                   href="<?= e(url('cart.php')) ?>"
                   <?= $currentPage === 'cart.php' ? 'aria-current="page"' : '' ?>>Cart</a>
                <?php if ($currentUser): ?>
                    <div class="dropdown">
                        <button class="btn btn-link nav-account-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Hi, <?= e($currentUser['username']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-header">Your account</span></li>
                            <?php if ($currentUser['role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?= e(url('admin/dashboard.php')) ?>">Admin dashboard</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= e(url('profile.php')) ?>">Your profile</a></li>
                            <li><a class="dropdown-item" href="<?= e(url('build-summary.php')) ?>">Review current build</a></li>
                            <li><a class="dropdown-item" href="<?= e(url('saved-builds.php')) ?>">Saved builds</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="post" action="<?= e(url('logout.php')) ?>" class="m-0">
                                    <?= csrf_field() ?><button class="dropdown-item" type="submit">Sign out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="nav-link<?= $currentPage === 'login.php' ? ' active' : '' ?>" href="<?= e(url('login.php')) ?>">Sign in</a>
                    <a class="btn btn-outline-dark" href="<?= e(url('register.php')) ?>">Create account</a>
                <?php endif; ?>
                <a class="btn btn-primary"
                   href="<?= e(url('builder.php')) ?>"
                   <?= $currentPage === 'builder.php' ? 'aria-current="page"' : '' ?>>Build Your PC</a>
            </div>
        </div>
    </div>
</nav>
<script>
    (() => {
        const navigation = document.querySelector('.forge-nav-links');
        const indicator = navigation?.querySelector('.forge-nav-indicator');
        const links = navigation ? [...navigation.querySelectorAll('.nav-link')] : [];
        const activeLink = links.find((link) => link.getAttribute('aria-current') === 'page');

        if (!navigation || !indicator || !activeLink) return;

        const moveIndicator = (link) => {
            const navigationBox = navigation.getBoundingClientRect();
            const linkBox = link.getBoundingClientRect();
            indicator.style.width = `${linkBox.width}px`;
            indicator.style.transform = `translateX(${linkBox.left - navigationBox.left}px)`;
            indicator.style.opacity = '1';
        };

        indicator.style.transition = 'none';
        moveIndicator(activeLink);
        window.requestAnimationFrame(() => {
            indicator.style.transition = '';
        });
        window.addEventListener('resize', () => moveIndicator(activeLink));
    })();

    (() => {
        const toggles = [...document.querySelectorAll('[data-theme-toggle], [data-theme-toggle-mobile]')];
        if (!toggles.length) return;

        const syncToggle = (toggle) => {
            const dark = document.documentElement.dataset.theme === 'dark';
            toggle.textContent = dark ? '\u2600' : '\u263e';
            toggle.setAttribute('aria-pressed', dark ? 'true' : 'false');
            toggle.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
            toggle.setAttribute('title', dark ? 'Switch to light mode' : 'Switch to dark mode');
        };

        toggles.forEach((toggle) => {
            syncToggle(toggle);
            toggle.addEventListener('click', () => {
                const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
                document.documentElement.dataset.theme = nextTheme;
                try { localStorage.setItem('pcforge-theme', nextTheme); } catch (error) { /* Storage may be unavailable. */ }
                toggles.forEach(syncToggle);
            });
        });
    })();
</script>
