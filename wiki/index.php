<?php
/**
 * GameWiki for Minetest
 *
 * Copyright (c) 2012 cornernote, Brett O'Donnell <cornernote@gmail.com>
 *
 * Source Code: https://github.com/cornernote/minetest-gamewiki
 * License: GPLv3
 */
require('globals.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $GLOBALS['name']; ?></title>
    <?php include('include/head_tags.php'); ?>
</head>

<body>
<?php include('include/menu.php'); ?>
<div class="container home">

    <div class="hero-unit">
        <h1><?php echo $GLOBALS['name']; ?></h1>

        <p>This site contains a list of all Items, Crafts, ABMs and Aliases.</p>
    </div>
	
	<ul>
		<li>
			<h2><a href="mods.php">Mods
				<small>which provide items, crafts, abms or aliases</small>
			</a></h2>
		</li>
		<li>
			<h2><a href="items.php">Items
				<small>tools, nodes, crafts</small>
			</a></h2>
		</li>
		<li>
			<h2><a href="crafts.php">Crafts
				<small>learn to craft items</small>
			</a></h2>
		</li>
		<li>
			<h2><a href="abms.php">ABMs
				<small>active block modifiers</small>
			</a></h2>
		</li>
		<li>
			<h2><a href="aliases.php">Aliases
				<small>list of available item aliases</small>
			</a></h2>
		</li>
	</ul>

</div>
<div id="footer"></div>
</body>
</html>