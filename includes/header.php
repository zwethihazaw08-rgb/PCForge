<?php

require_once __DIR__ . '/functions.php';

// Set $pageTitle before including this file to give each page its own title.
$pageTitle = $pageTitle ?? 'Build Your PC';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <style>
        /* Paint the page canvas before external stylesheets arrive. */
        html {
            color-scheme: light;
            background-color: #ffffff;
            color: #202020;
        }

        @media (prefers-color-scheme: dark) {
            html:not([data-theme]) {
                color-scheme: dark;
                background-color: #121212;
                color: #f1f1f1;
            }
        }

        html[data-theme="dark"] {
            color-scheme: dark;
            background-color: #121212;
            color: #f1f1f1;
        }

        html body {
            background-color: inherit !important;
            color: inherit;
        }
    </style>
    <script>
        // Desktop browsers report trackpad pinch zoom as Ctrl + wheel.
        document.addEventListener('wheel', (event) => {
            if (event.ctrlKey) event.preventDefault();
        }, { passive: false });

        // Safari exposes pinch zoom through gesture events instead.
        ['gesturestart', 'gesturechange'].forEach((eventName) => {
            document.addEventListener(eventName, (event) => {
                event.preventDefault();
            }, { passive: false });
        });

        (() => {
            try {
                const saved = localStorage.getItem('pcforge-theme');
                const preferred = saved === 'dark' || saved === 'light'
                    ? saved
                    : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.dataset.theme = preferred;
            } catch (error) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>
    <title><?= e($pageTitle) ?> | PCForge</title>
    <link rel="icon" type="image/png" href="<?= e(url('assets/images/logo_nobg.png')) ?>">
    <link rel="apple-touch-icon" href="<?= e(url('assets/images/logo_nobg.png')) ?>">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css?v=' . (string) @filemtime(__DIR__ . '/../assets/css/style.css'))) ?>">
    <script
        defer
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"
    ></script>
</head>
<body class="bg-white text-dark d-flex flex-column min-vh-100">
    <!-- Each page should put its content inside <main id="main-content">. -->
    <a href="#main-content" class="visually-hidden-focusable p-3 bg-dark text-white">
        Skip to main content
    </a>
