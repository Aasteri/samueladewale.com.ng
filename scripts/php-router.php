<?php
/**
 * Local stand-in for public_html/.htaccess.
 * Start with: php -S localhost:8080 -t public_html scripts/php-router.php
 */
$root = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public_html';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rawurldecode($path);

if (preg_match('#^/backend/(?!api/)#', $path)) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

$local = $root . str_replace('/', DIRECTORY_SEPARATOR, $path);
if ($path !== '/' && is_file($local)) {
    return false;
}

if (preg_match('#^/projects/([a-z0-9-]+)/?$#', $path, $match)) {
    $_GET['slug'] = $match[1];
    require $root . '/project.php';
    return true;
}

if (preg_match('#^/admin/site/([a-z]+)/?$#', $path, $match)) {
    $_GET['page'] = $match[1];
    require $root . '/admin-site.php';
    return true;
}

$routes = [
    '#^/?$#' => '/index.php',
    '#^/about/?$#' => '/about.php',
    '#^/projects/?$#' => '/projects.php',
    '#^/services/?$#' => '/services.php',
    '#^/cvs/?$#' => '/cvs.php',
    '#^/contact/?$#' => '/contact.php',
    '#^/github-activity/?$#' => '/github.php',
    '#^/admin/projects/?$#' => '/admin-projects.php',
    '#^/admin/?$#' => '/admin.php',
];

foreach ($routes as $pattern => $script) {
    if (preg_match($pattern, $path)) {
        require $root . $script;
        return true;
    }
}

http_response_code(404);
require $root . '/404.php';
return true;
