<?php

header('Content-Type: text/html; charset=ISO-8859-15');
error_reporting(E_ALL);
ini_set('display_errors', '1');

$doc = new DOMDocument();
$doc->load('http://www.aftenposten.no/rss/?kat=forbruker_digital');

// Gets the title for the rss stream
$nodes = $doc->getElementsByTagName('title');
$title = is_object($nodes->item(0))? $nodes->item(0)->nodeValue : '';

// Gets all the items
$items = $doc->getElementsByTagName('item');
$output = '';

foreach ($items as $item) {
    $nodes = $item->getElementsByTagName('title');
    $headline = is_object($nodes->item(0))? $nodes->item(0)->nodeValue : '';

    $nodes = $item->getElementsByTagName('description');
    $description = is_object($nodes->item(0))? $nodes->item(0)->nodeValue : '';
    
    $nodes = $item->getElementsByTagNameNS('http://purl.org/dc/elements/1.1/', 'date');
    $date = is_object($nodes->item(0))? $nodes->item(0)->nodeValue : '';

    $nodes = $item->getElementsByTagName('enclosure');
    $picture = is_object($nodes->item(0))? $nodes->item(0)->getAttribute('url') : '';
    
    $nodes = $item->getElementsByTagName('link');
    $link = is_object($nodes->item(0))? $nodes->item(0)->nodeValue : '';
    
    $output .= "<h2>" . htmlspecialchars($headline) . "</h2>
    <p>" . (empty($picture)? '' : "<img src=\"" . htmlspecialchars($picture) . "\" alt=\"\" title=\"\" />") . "</p>
    <p>" . htmlspecialchars($description) . "</p>
    <p><a href=\"" . htmlspecialchars($link) . "\">Gå til artikkel</a></p>
    <p>" . htmlspecialchars($date) . "</p>\n";
}
  
?>

<!DOCTYPE html>
<html>
<head>
  <title><?php echo $title; ?></title>
</head>
<body>

Oblig 5 høsten 2012  IMT2571 2
<h1><?php echo $title; ?></h1>

<?php echo $output; ?>

</body>
</html>