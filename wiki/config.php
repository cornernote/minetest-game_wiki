<?php
/**
 * GameWiki for Minetest
 *
 * Copyright (c) 2012 cornernote, Brett O'Donnell <cornernote@gmail.com>
 *
 * Source Code: https://github.com/cornernote/minetest-gamewiki
 * License: GPLv3
 */

// this is the title of your GameWiki
$GLOBALS['name'] = 'Minetest GameWiki';

// the main link on the menu
$GLOBALS['brand_url'] = 'https://sites.google.com/site/cornernote/minetest/game-wiki';

// this is your path to minetest:
$GLOBALS['path'] = 'C:/minetest';

// this is the name of your SQLite3 database file
$db = new SQLite3('data/wiki.db');
