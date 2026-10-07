<?php

require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Build a PC That Fits You';
$categories = [
    'cpu' => 'Processors',
    'gpu' => 'Graphics Cards',
    'mb' => 'Motherboards',
    'memory' => 'Memory',
    'storage' => 'Storage',
    'psu' => 'Power Supplies',
    'case_box' => 'Cases',
    'cooling' => 'CPU Cooling',
    'fans' => 'Case Fans',
    'monitor' => 'Monitors',
];

$featuredProducts = [];
$catalogError = false;

try {
    $connection = db();

    // Table names come only from this fixed list, never from request input.
    // Show one recent active product from each of five component categories.
    foreach (['cpu', 'gpu', 'memory', 'storage', 'mb'] as $category) {
        $statement = $connection->prepare(
            "SELECT id, name, brand, price, image_url, stock
             FROM `$category` WHERE status = :status ORDER BY id DESC LIMIT 1"
        );
        $statement->execute(['status' => 'active']);
        $product = $statement->fetch();

        if ($product) {
            $product['category'] = $category;
            $featuredProducts[] = $product;
        }
    }
} catch (PDOException $exception) {
    error_log('PCForge homepage query failed: ' . $exception->getMessage());
    $featuredProducts = [];
    $catalogError = true;
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content" tabindex="-1">
    <!-- Hero styles stay here to keep this update limited to one file. -->
    <style>
    .pc-hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        margin: 0 1rem;
        padding: clamp(3rem, 7vw, 6rem) 1.25rem 2rem;
        border: 1px solid #dedede;
        border-radius: 1.5rem;
        background: #0a1018;
        color: #ffffff;
        text-align: center;
    }

    .pc-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: -1;
        pointer-events: none;
        background-image: linear-gradient(180deg, rgba(5, 10, 18, 0.65), rgba(5, 10, 18, 0.78) 65%, #0a1018),
            url("<?= e(url('assets/images/tai-bui-Q-xGz9NOVOE-unsplash.jpg')) ?>");
        background-size: cover;
        background-position: center;
    }

    .pc-hero-inner {
        max-width: 780px;
        margin: 0 auto;
    }

    .pc-hero h1 {
        margin: 0 0 1.5rem;
        font-size: clamp(2.3rem, 6vw, 4.5rem);
        line-height: 1.05;
        letter-spacing: -0.045em;
    }

    .pc-hero h1 span {
        color: #c8e6ed;
    }

    .pc-hero-description {
        max-width: 56ch;
        margin: 0 auto;
        color: #606060;
        font-size: clamp(1rem, 2vw, 1.15rem);
    }

    .pc-hero-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.75rem;
        margin: 2rem 0;
    }

    .pc-hero .btn {
        padding: 0.8rem 1.3rem;
    }

    .pc-hero :focus-visible {
        outline: 3px solid #ffffff;
        outline-offset: 4px;
    }

    .pc-hero .btn-primary {
        background: #f1f1f1;
        border-color: #f1f1f1;
        color: #171717;
    }

    .pc-hero .btn-primary:hover {
        background: #ffffff;
        border-color: #ffffff;
    }

    .pc-hero .btn-outline-dark {
        color: #ffffff;
        border-color: #ffffff80;
        background: #0a101866;
    }

    .pc-hero .btn-outline-dark:hover {
        color: #171717;
        background: #ffffff;
        border-color: #ffffff;
    }

    .pc-hero .pc-hero-description,
    .pc-hero .pc-hero-steps span,
    .pc-hero .pc-hero-strip-title,
    .pc-hero .pc-hero-pause-label {
        color: #d0d6df;
    }

    .pc-hero-steps {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1.5rem 3rem;
        margin: 2rem 0 0;
        padding: 0;
        list-style: none;
    }

    .pc-hero-steps strong {
        display: block;
        font-size: 1.15rem;
    }

    .pc-hero-steps span {
        color: #606060;
        font-size: 0.85rem;
    }

    .pc-hero-strip {
        max-width: 1100px;
        margin: 3rem auto 0;
    }

    .pc-hero-strip-title {
        color: #606060;
        font-size: 0.75rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
    }

    .pc-hero-pause {
        accent-color: #171717;
    }

    .pc-hero-pause-label {
        margin: 0 0 1rem 0.35rem;
        color: #606060;
        font-size: 0.8rem;
        cursor: pointer;
    }

    .pc-hero-window {
        overflow: hidden;
        mask-image: linear-gradient(90deg, transparent, #000000 8%, #000000 92%, transparent);
    }

    .pc-hero-track {
        display: flex;
        width: max-content;
        animation: pc-hero-scroll 40s linear infinite;
    }

    .pc-hero-group {
        display: flex;
        flex-shrink: 0;
    }

    .pc-hero-part {
        margin: 0 0.5rem;
        padding: 0.65rem 1.1rem;
        border: 1px solid #dedede;
        border-radius: 0.75rem;
        background: #f5f5f5;
        color: #505050;
        font-weight: 600;
        white-space: nowrap;
    }

    .pc-hero-pause:checked~.pc-hero-window .pc-hero-track,
    .pc-hero-window:hover .pc-hero-track {
        animation-play-state: paused;
    }

    @keyframes pc-hero-scroll {
        to {
            transform: translateX(-50%);
        }
    }

    @media (max-width: 460px) {
        .pc-hero {
            margin: 0 0.5rem;
            border-radius: 1rem;
        }

        .pc-hero-actions .btn {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .pc-hero-track {
            animation: none;
            width: 100%;
        }

        .pc-hero-window {
            mask-image: none;
        }

        .pc-hero-group {
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .pc-hero-group+.pc-hero-group,
        .pc-hero-pause,
        .pc-hero-pause-label {
            display: none;
        }

        .pc-hero-part {
            margin: 0;
            white-space: normal;
        }
    }

    .build-showcase {
        padding-bottom: 0;
    }

    .build-showcase-art {
        height: 100%;
        display: grid;
        place-items: center;
        padding: 0.5rem;
        background: #f5f5f5;
        border: 1px solid #dedede;
        border-radius: 1.25rem;
    }

    .build-showcase-art img {
        display: block;
        width: 100%;
        height: auto;
        border-radius: 0.75rem;
    }

    .build-purpose {
        display: flex;
        gap: 1rem;
        padding: 1.1rem 0;
        border-bottom: 1px solid #dedede;
    }

    .build-purpose:last-child {
        border-bottom: 0;
    }

    .build-purpose-icon {
        display: grid;
        place-items: center;
        flex: 0 0 44px;
        height: 44px;
        border: 1px solid #dedede;
        border-radius: 0.8rem;
        background: #f5f5f5;
    }

    .build-purpose h3 {
        font-size: 1rem;
        margin-bottom: 0.35rem;
    }

    .build-purpose p {
        font-size: 0.9rem;
        color: #606060;
        margin: 0;
    }

    .component-arc {
        position: relative;
        margin-top: 1.5rem;
    }

    .component-arc-track {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
    }

    .component-arc.is-ready .component-arc-track {
        position: relative;
        display: block;
        overflow: hidden;
        touch-action: pan-y;
    }

    .component-arc-card {
        width: min(330px, 100%);
        display: flex;
        flex-direction: column;
        text-align: left;
        padding: 0;
        overflow: hidden;
        border: 1px solid var(--forge-border);
        border-radius: 1.2rem;
        background: var(--forge-surface-raised);
        color: var(--bs-body-color);
        box-shadow: 0 14px 25px -20px #0007;
        cursor: pointer;
        text-decoration: none;
    }

    .component-arc.is-ready .component-arc-card {
        position: absolute;
        top: 20px;
        left: 50%;
        transition: transform 550ms cubic-bezier(.22, .61, .36, 1), opacity 350ms ease;
    }

    .component-arc-card.is-center {
        border-color: var(--forge-muted);
        box-shadow: 0 20px 35px -24px #0009;
    }

    .component-arc-card:focus-visible {
        outline: 2px solid var(--forge-focus);
        outline-offset: 4px;
    }

    .component-arc-art {
        position: relative;
        display: grid;
        place-items: center;
        flex: 0 0 55%;
        min-height: 0;
        padding: 1.25rem;
        background: var(--forge-surface);
        user-select: none;
    }

    .component-arc-art img {
        width: 100%;
        height: 100%;
        min-height: 0;
        object-fit: contain;
        background: var(--forge-surface);
        pointer-events: none;
    }

    .component-arc-meta {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        padding: 1.1rem 1.25rem;
        min-height: 0;
    }

    .component-arc-name {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
        overflow-wrap: anywhere;
        font-size: 1.1rem;
        line-height: 1.3;
        min-height: 2.6em;
        font-weight: 700;
    }

    .component-arc-date,
    .component-arc-caption {
        color: var(--forge-muted);
        font-size: 0.75rem;
    }

    .component-arc-price {
        margin-top: auto;
        font-size: 1.3rem;
        font-weight: 700;
    }

    .component-arc-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin: 1rem 0 2rem;
    }

    .component-arc-controls[hidden] {
        display: none;
    }

    .component-arc-arrow {
        width: 44px;
        height: 44px;
        padding: 0;
        border: 1px solid var(--forge-border);
        border-radius: 50%;
        background: var(--forge-surface-raised);
        color: var(--bs-body-color);
        font-size: 1.7rem;
        cursor: pointer;
    }

    .component-arc-counter {
        min-width: 4rem;
        text-align: center;
        color: var(--forge-muted);
        font-size: 0.85rem;
    }

    .component-arc-play {
        border: 0;
        padding: 0.5rem;
        background: none;
        color: var(--forge-muted);
        text-decoration: underline;
        text-underline-offset: 4px;
        font-size: 0.85rem;
    }

    @media (prefers-reduced-motion: reduce) {
        .component-arc.is-ready .component-arc-card {
            transition: none;
        }
    }
    </style>

    <section class="pc-hero" aria-labelledby="hero-heading">
        <div class="pc-hero-inner">
            <h1 id="hero-heading">Choose your parts.<br><span>Make it yours.</span></h1>
            <p class="pc-hero-description">
                Plan a PC around your work, games, and budget. Explore the components,
                understand how they fit, and build with a clear plan.
            </p>
            <div class="pc-hero-actions">
                <a class="btn btn-primary" href="<?= e(url('builder.php')) ?>">Build Your PC</a>
                <a class="btn btn-outline-dark" href="<?= e(url('products.php')) ?>">Browse Components <span
                        aria-hidden="true">&rarr;</span></a>
            </div>
            <ol class="pc-hero-steps">
                <li><strong>01. Choose</strong><span>Find your components</span></li>
                <li><strong>02. Check</strong><span>Review fit and compatibility</span></li>
                <li><strong>03. Plan</strong><span>Consider price and power</span></li>
            </ol>
        </div>
        <div class="pc-hero-strip">
            <p class="pc-hero-strip-title">Every part of your next build</p>
            <input class="pc-hero-pause" type="checkbox" id="pause-hero" aria-controls="hero-category-strip">
            <label class="pc-hero-pause-label" for="pause-hero">Pause scrolling</label>
            <!-- Decorative copies; accessible category links appear below the hero. -->
            <div class="pc-hero-window" id="hero-category-strip" aria-hidden="true">
                <div class="pc-hero-track">
                    <?php for ($copy = 0; $copy < 2; $copy++): ?>
                    <div class="pc-hero-group">
                        <?php foreach ($categories as $label): ?>
                        <span class="pc-hero-part"><?= e($label) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding build-showcase" aria-labelledby="showcase-heading">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-6">
                    <figure class="build-showcase-art mb-0">
                        <img src="<?= e(url('assets/images/pc2.jpg')) ?>"
                            alt="Gaming PC with a tempered-glass case and RGB lighting" width="3240" height="3240">
                    </figure>
                </div>
                <div class="col-lg-6">
                    <p class="small fw-semibold text-uppercase text-secondary">Built around you</p>
                    <h2 id="showcase-heading">What will you build?</h2>
                    <p class="text-secondary">Start with what you love doing. Then choose the parts that make sense for
                        your setup.</p>
                    <div class="build-purpose">
                        <span class="build-purpose-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 7h10c3 0 5 10 3 11-2 1-4-3-5-3H9c-1 0-3 4-5 3C2 17 4 7 7 7Z" />
                                <path d="M7 10v4m-2-2h4" />
                                <circle cx="16" cy="10.5" r=".7" />
                                <circle cx="18" cy="13" r=".7" />
                            </svg>
                        </span>
                        <div>
                            <h3>For your next adventure</h3>
                            <p>Gaming builds start with a balanced CPU and graphics card, matched to your games and
                                display.</p>
                        </div>
                    </div>
                    <div class="build-purpose">
                        <span class="build-purpose-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="14" rx="2" />
                                <path d="M8 21h8m-4-4v4M8 12l3-3 3 3 3-5" />
                            </svg>
                        </span>
                        <div>
                            <h3>For your next creation</h3>
                            <p>Make room for your projects with memory, storage, and processing power suited to your
                                creative tools.</p>
                        </div>
                    </div>
                    <div class="build-purpose">
                        <span class="build-purpose-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="6" y="2" width="12" height="20" rx="2" />
                                <path d="M9 7h6m-6 4h6m-6 4h6" />
                                <circle cx="12" cy="19" r=".5" />
                            </svg>
                        </span>
                        <div>
                            <h3>For your everyday space</h3>
                            <p>Plan a practical PC for study, work, and daily tasks, with a case that fits your desk and
                                your parts.</p>
                        </div>
                    </div>
                    <a class="btn btn-primary mt-4" href="<?= e(url('builder.php')) ?>">Plan Your Build <span
                            aria-hidden="true">&rarr;</span></a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding" aria-labelledby="categories-heading">
        <div class="container">
            <h2 id="categories-heading">Every part has a purpose.</h2>
            <p class="text-secondary mb-4">Find the components for your next build.</p>
            <div class="row g-3">
                <?php foreach ($categories as $category => $label): ?>
                <div class="col-sm-6 col-lg-4">
                    <a class="category-card" href="<?= e(url('products.php?category=' . $category)) ?>">
                        <span class="fw-semibold"><?= e($label) ?></span>
                        <span class="float-end" aria-hidden="true">&rarr;</span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section-padding bg-light" aria-labelledby="featured-heading">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <h2 id="featured-heading" class="mb-0">Explore components</h2>
                <a class="fw-semibold" href="<?= e(url('products.php')) ?>">View all components &rarr;</a>
            </div>

            <?php if ($catalogError): ?>
            <p class="mb-0" role="status">Components are temporarily unavailable. Please try again later.</p>
            <?php elseif (!$featuredProducts): ?>
            <p class="mb-0">No components are available yet. Check back soon.</p>
            <?php else: ?>
            <div class="component-arc" data-component-carousel role="group" aria-roledescription="carousel"
                aria-label="Featured components" tabindex="0">
                <div class="component-arc-track" data-component-track>
                    <?php foreach ($featuredProducts as $product): ?>
                    <?php
                        $imageUrl = null;
                        $storedImage = $product['image_url'] ?? '';

                        // Accept HTTPS images or existing files in assets/images.
                        if (filter_var($storedImage, FILTER_VALIDATE_URL)
                            && strtolower(parse_url($storedImage, PHP_URL_SCHEME) ?? '') === 'https') {
                            $imageUrl = $storedImage;
                        } elseif ($storedImage !== '') {
                            $imageName = basename($storedImage);
                            if (is_file(__DIR__ . '/assets/images/' . $imageName)) {
                                $imageUrl = url('assets/images/' . rawurlencode($imageName));
                            }
                        }

                        $productUrl = url('product.php?' . http_build_query([
                            'category' => $product['category'],
                            'id' => $product['id'],
                        ]));
                        ?>
                    <a class="component-arc-card" href="<?= e($productUrl) ?>" data-component-card
                        data-name="<?= e($product['name']) ?>" aria-label="<?= e('View ' . $product['name']) ?>">
                        <div class="component-arc-art">
                            <?php if ($imageUrl !== null): ?>
                            <img src="<?= e($imageUrl) ?>" alt="<?= e($product['name']) ?>" loading="lazy" width="400"
                                height="300">
                            <?php else: ?>
                            <span class="component-arc-caption">Image unavailable</span>
                            <?php endif; ?>
                        </div>
                        <div class="component-arc-meta">
                            <span class="component-arc-date"><?= e($categories[$product['category']]) ?></span>
                            <strong class="component-arc-name"><?= e($product['name']) ?></strong>
                            <?php if (!empty($product['brand'])): ?><span
                                class="component-arc-caption"><?= e($product['brand']) ?></span><?php endif; ?>
                            <span class="component-arc-price"><?= e(money($product['price'])) ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <div class="component-arc-controls" data-component-controls hidden>
                    <button class="component-arc-arrow" type="button" data-component-prev
                        aria-label="Previous component">&lsaquo;</button>
                    <span class="component-arc-counter" data-component-counter></span>
                    <button class="component-arc-arrow" type="button" data-component-next
                        aria-label="Next component">&rsaquo;</button>
                    <button class="component-arc-play" type="button" data-component-play>Pause rotation</button>
                </div>
                <span class="visually-hidden" data-component-live aria-live="polite" aria-atomic="true"></span>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section-padding" aria-labelledby="planning-heading">
        <div class="container">
            <h2 id="planning-heading" class="mb-4">Plan with the details in mind.</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <h3 class="h5">Choose your components</h3>
                    <p class="text-secondary">Start with a CPU and motherboard, then choose memory, graphics, storage,
                        cooling, a power supply, and a case.</p>
                </div>
                <div class="col-md-4">
                    <h3 class="h5">Check how parts fit</h3>
                    <p class="text-secondary">Socket, memory type, and physical clearance matter. Missing specifications
                        need checking; they do not guarantee compatibility.</p>
                </div>
                <div class="col-md-4">
                    <h3 class="h5">Review cost and power</h3>
                    <p class="text-secondary">Consider the full component cost and allow power supply headroom.
                        Estimated system power is not an exact wall-power measurement.</p>
                </div>
            </div>
            <div class="border rounded-4 p-4 mt-4">
                <h3 class="h5">Prefer a prebuilt PC?</h3>
                <p class="text-secondary mb-0">Start with one of our current-catalogue builds, then customize every part
                    in the PC builder. <a href="<?= e(url('prebuilts.php')) ?>">Explore prebuilt PCs &rarr;</a></p>
            </div>
        </div>
    </section>
    <script>
    (() => {
        const root = document.querySelector('[data-component-carousel]');
        if (!root) return;
        const track = root.querySelector('[data-component-track]');
        const cards = [...root.querySelectorAll('[data-component-card]')];
        const controls = root.querySelector('[data-component-controls]');
        const play = root.querySelector('[data-component-play]');
        const counter = root.querySelector('[data-component-counter]');
        const live = root.querySelector('[data-component-live]');
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        let current = 0;
        let playing = !reducedMotion.matches;
        let timer;
        let drag = null;
        let suppressClick = false;
        let visible = false;

        function render() {
            const width = track.clientWidth;
            const cardWidth = Math.min(330, Math.round(width * 0.78));
            const cardHeight = width < 576 ? 420 : 440;
            const visibleSides = width >= 900 ? 2 : 1;
            const step = Math.PI / 7;
            const radius = cardWidth * 0.84 / Math.sin(step);
            const outer = Math.min(visibleSides, Math.floor(cards.length / 2));
            track.style.height = `${cardHeight + Math.round(radius * (1 - Math.cos(outer * step)) * 0.48) + 55}px`;
            cards.forEach((card, index) => {
                let distance = (index - current + cards.length) % cards.length;
                if (distance > cards.length / 2) distance -= cards.length;
                const absolute = Math.abs(distance);
                const angle = Math.max(-3, Math.min(3, distance)) * step;
                const x = Math.round(radius * Math.sin(angle));
                const y = Math.round(radius * (1 - Math.cos(angle)) * 0.48);
                card.style.width = `${cardWidth}px`;
                card.style.height = `${cardHeight}px`;
                card.style.marginLeft = `${-Math.round(cardWidth / 2)}px`;
                card.style.transform =
                    `translate(${x}px, ${y}px) scale(${Math.max(0.7, 1 - absolute * 0.09)})`;
                card.style.zIndex = String(20 - absolute);
                card.style.opacity = absolute > visibleSides ? '0' : '1';
                card.style.pointerEvents = absolute > visibleSides ? 'none' : 'auto';
                card.tabIndex = index === current ? 0 : -1;
                card.setAttribute('aria-current', String(index === current));
                card.setAttribute('aria-hidden', String(absolute > visibleSides));
                card.classList.toggle('is-center', index === current);
            });
            counter.textContent = `${current + 1} / ${cards.length}`;
        }

        function go(index, announce = true) {
            const focusOnCard = cards.includes(document.activeElement);
            current = (index + cards.length) % cards.length;
            render();
            if (focusOnCard) cards[current].focus({
                preventScroll: true
            });
            if (announce) live.textContent =
                `${cards[current].dataset.name}, component ${current + 1} of ${cards.length}`;
            restart();
        }

        function restart() {
            window.clearInterval(timer);
            play.textContent = playing ? 'Pause rotation' : 'Play rotation';
            play.setAttribute('aria-pressed', String(playing));
            // Pause while users inspect cards or leave the page.
            const interacting = root.matches(':hover') || root.contains(document.activeElement);
            if (playing && cards.length > 1 && visible && !document.hidden && !interacting && !drag) {
                timer = window.setInterval(() => go(current + 1, false), 5000);
            }
        }

        root.querySelector('[data-component-prev]').addEventListener('click', () => go(current - 1));
        root.querySelector('[data-component-next]').addEventListener('click', () => go(current + 1));
        play.addEventListener('click', () => {
            playing = !playing;
            restart();
        });
        cards.forEach((card, index) => card.addEventListener('click', event => {
            if (suppressClick) {
                event.preventDefault();
                return;
            }
            if (index !== current) {
                event.preventDefault();
                go(index);
            }
        }));
        root.addEventListener('keydown', event => {
            if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                event.preventDefault();
                go(current + (event.key === 'ArrowRight' ? 1 : -1));
            }
        });
        track.addEventListener('dragstart', event => event.preventDefault());
        track.addEventListener('pointerdown', event => {
            if (!event.isPrimary || event.button !== 0) return;
            suppressClick = false;
            drag = {
                x: event.clientX,
                y: event.clientY,
                id: event.pointerId
            };
            restart();
        });
        track.addEventListener('pointermove', event => {
            if (!drag || drag.id !== event.pointerId) return;
            if (Math.abs(event.clientX - drag.x) > 12 && Math.abs(event.clientX - drag.x) > Math.abs(event
                    .clientY - drag.y)) {
                suppressClick = true;
                track.setPointerCapture(event.pointerId);
            }
        });
        track.addEventListener('pointerup', event => {
            if (!drag || drag.id !== event.pointerId) return;
            const dx = event.clientX - drag.x;
            const dy = event.clientY - drag.y;
            drag = null;
            if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) go(current + (dx < 0 ? 1 : -1));
            if (track.hasPointerCapture(event.pointerId)) track.releasePointerCapture(event.pointerId);
            restart();
            window.setTimeout(() => {
                suppressClick = false;
            }, 0);
        });
        track.addEventListener('pointercancel', event => {
            if (!drag || drag.id !== event.pointerId) return;
            drag = null;
            restart();
        });
        track.addEventListener('lostpointercapture', event => {
            // Touch starts with implicit capture on the card's child element.
            // Its capture loss bubbles here when the track takes over a swipe.
            if (event.target !== track || !drag || drag.id !== event.pointerId) return;
            drag = null;
            restart();
        });
        [root].forEach(element => {
            ['pointerenter', 'pointerleave', 'focusin'].forEach(name => element.addEventListener(name,
                restart));
            element.addEventListener('focusout', () => window.setTimeout(restart, 0));
        });
        document.addEventListener('visibilitychange', restart);
        reducedMotion.addEventListener('change', () => {
            playing = !reducedMotion.matches;
            restart();
        });
        new ResizeObserver(render).observe(track);
        new IntersectionObserver(entries => {
            visible = entries[0].isIntersecting;
            restart();
        }, {
            threshold: 0.5
        }).observe(root);
        root.classList.add('is-ready');
        controls.hidden = cards.length < 2;
        render();
        restart();
    })();
    </script>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>