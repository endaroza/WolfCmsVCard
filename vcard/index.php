<?php
if (!defined('IN_CMS')) { exit(); }

require_once("utils/vcard.php");
require_once("utils/vbook.php");


Plugin::setInfos(array(
    'id'          => 'vcard',
    'title'       => 'VCard', 
    'description' => 'Show VCards.', 
    'version'     => '1.0',
    'license'     => 'GPL',
    'author'      => 'Enrico Da Ros',
    'website'     => 'http://www.kendar.org/',
    //'require_wolf_version' => '0.5.0',
    'type' => 'both'
));
Plugin::addController('vcard','Vcard', 'administrator');


// add filter "Eb gallery"
Filter::add('vcard_filter', 'vcard/filters/vcard_filter.php');

AutoLoader::addFile('PageVcard', CORE_ROOT.'/plugins/'.'vcard'.'/behaviours/vcard_behaviour.php');
Behavior::add('vcard', 'vcard'.'/behaviours/vcard_behaviour.php');
 
?>