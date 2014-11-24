<?php
header("Content-type: text/xml; charset=utf-8");

require_once('lib/db.inc');

function printUrl($loc,$freq='yearly'){
    print "<url><loc>$loc</loc><changfreq>$freq</changfreq></url>\n";
}

print '<?xml version="1.0" encoding="UTF-8"?>
<urlset 
xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 
http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

$base = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

printUrl($base);

foreach(getCategories() as $category){
    printUrl("$base/category/{$category['name']}");
}

$specials = Array(
    'meal',
    'quick',
    'favorites',
    'tips',
    'ingredients'
    );

foreach($specials as $special){
    printUrl("$base/$special",'monthly');
}

$res = getAll();
while($row = pg_fetch_assoc($res)){
    printUrl("$base/recipe/" . urlencode($row['name']));
}

$res = getAllIngredients();
while($row = pg_fetch_assoc($res)){
    printUrl("$base/ingredient/" . urlencode($row['ingredient']));
}

print '</urlset>';
