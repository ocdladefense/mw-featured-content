<?php

if (!defined("MEDIAWIKI")) die();

// White list the special page, so it is public.
$wgWhitelistRead[] = "Special:FeaturedContent";


# Autoload classes and files
$wgAutoloadClasses["SpecialFeaturedContent"] = __DIR__ . "/SpecialFeaturedContent.php";


# Tell MediaWiki about the new special page and its class name
$wgSpecialPages["FeaturedContent"] = "SpecialFeaturedContent";


$wgResourceModules["ext.caseReviews"] = array(
    "scripts" => array(),
    "styles" => array(
        "css/featured.css"
    ),
    "position" => "bottom",
    "remoteBasePath" => "/extensions/FeaturedContent",
    "localBasePath" => "extensions/FeaturedContent"
);