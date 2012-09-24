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
    <title>List Mods :: <?php echo $GLOBALS['name']; ?></title>
    <?php include('include/head_tags.php'); ?>
</head>

<body>
<?php include('include/menu.php'); ?>
<div class="container">

    <h1>Mods</h1>

    <table class="table">
        <?php
        foreach (gamewiki::get_mods() as $mod) {
            ?>
            <tr>
                <th><a href="mod.php?mod=<?php echo $mod; ?>"><?php echo $mod; ?></a></th>
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
<div id="footer"></div>
</body>
</html>