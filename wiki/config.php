<?php
// this is the title of your GameWiki
$GLOBALS['name'] = 'Minetest GameWiki';

// the main link on the menu
$GLOBALS['brand_url'] = 'https://sites.google.com/site/cornernote/minetest/game-wiki';

// this is your path to minetest:
$GLOBALS['path'] = 'C:/minetest';

// this is the name of your SQLite3 database file
$GLOBALS['db'] = new SQLite3('data/wiki.db');
?>