<?php
include_once __DIR__ . '/init.php';
header('Content-Type: text/xml; charset=utf-8', true); //set document header content type to be XML

$rss = new SimpleXMLElement('<rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom"></rss>');
$rss->addAttribute('version', '2.0');

$channel = $rss->addChild('channel'); //add channel node

$atom = $rss->addChild('atom:atom:link'); //add atom node
$atom->addAttribute('href', BASE_URL); //add atom node attribute
$atom->addAttribute('rel', 'self');
$atom->addAttribute('type', 'application/rss+xml');

$title = $rss->addChild('title', $types['webapp']['headmeta_title']);
$description = $rss->addChild('description', $types['webapp']['headmeta_description']);
$link = $rss->addChild('link', BASE_URL);

//Create RFC822 Date format to comply with RFC822
$date_f = date("D, d M Y H:i:s T", time());
$build_date = gmdate(DATE_RFC2822, strtotime($date_f)); 
$lastBuildDate = $rss->addChild('lastBuildDate',$date_f); //feed last build date

$generator = $rss->addChild('generator','tribe-rss-feed-v2-xml'); //add generator node

//Get objects from types listed in feedable_types
$ids = $sql->executeSQL("SELECT `id` FROM `data` WHERE `content_privacy`='public' AND `type` IN ('".implode("', '", $types['webapp']['feedable_types'])."') ORDER BY `id` DESC LIMIT 20");
$posts = $dash->getObjects($ids);

foreach ($posts as $post) {

    $field_title = $types[$post['type']]['headmeta_title'];
    $field_description = $types[$post['type']]['headmeta_description'];

    $item = $rss->addChild('item'); //add item node
    $title = $item->addChild('title', $post[$field_title]); //add title node under item
    $link = $item->addChild('link', BASE_URL.'/'.$post['type'].'/'.$post['slug']); //add link node under item
    $guid = $item->addChild('guid', BASE_URL.'/'.$post['type'].'/'.$post['slug']); //add guid node under item
    $guid->addAttribute('isPermaLink', 'true'); //add guid node attribute
    
    $description = $item->addChild('description', '<![CDATA['. htmlentities($post[$field_description]) . ']]>'); //add description
    
    $date_rfc = gmdate(DATE_RFC2822, $post['updated_on']);
    $item = $item->addChild('pubDate', $date_rfc); //add pubDate node

}

echo $rss->asXML(); //output XML