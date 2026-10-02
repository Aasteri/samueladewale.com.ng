<?php
require __DIR__ . '/backend/bootstrap.php';
header('Content-Type: application/xml; charset=utf-8');
$base = site()['url'];
$urls = ['/', '/about', '/projects', '/services', '/cvs', '/contact', '/github-activity'];
foreach (projects() as $p) {
    $urls[] = '/projects/' . $p['slug'];
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
foreach ($urls as $u) {
    echo '  <url><loc>' . e($base . $u) . "</loc></url>\n";
}
echo "</urlset>\n";
