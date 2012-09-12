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
	
	<div class="row">
		<div class="span8">
			<ul>
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
		<div class="span4">
			
			<h2>Mods</h2>
			<table class="table">
			<?php
			foreach(gamewiki::get_mods() as $mod) {
				?>
				<tr>
					<th><?php echo $mod; ?></th>
					<td><a href="items.php?mod=<?php echo $mod; ?>">Items</a></td>
					<td><a href="crafts.php?mod=<?php echo $mod; ?>">Crafts</a></td>
					<td><a href="abms.php?mod=<?php echo $mod; ?>">ABMs</a></td>
					<td><a href="aliases.php?mod=<?php echo $mod; ?>">Aliases</a></td>
				</tr>
				<?php
			}
			?>
			</table>
		</div>
	</div>

</div>
<div id="footer"></div>
</body>
</html>