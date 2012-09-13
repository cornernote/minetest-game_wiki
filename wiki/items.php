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
    <title>List Items :: <?php echo $GLOBALS['name']; ?></title>
    <?php include('include/head_tags.php'); ?>
</head>

<body>
<?php include('include/menu.php'); ?>
<div class="container">

    <?php
    $mods = gamewiki::get_mods();
    $filters = $filter_sql = $filter_join = '';
    if (isset($_GET['mod'])) {
        $filters .= '[mod:' . $_GET['mod'] . ']';
        $filter_sql .= 'AND "mod"="' . SQLite3::escapeString($_GET['mod']) . '" ';
    }
    if (isset($_GET['group'])) {
        $filters .= '[group:' . $_GET['group'] . ']';
        foreach (explode(',', $_GET['group']) as $group) {
            $filter_join .= 'LEFT JOIN group_to_itemname group_' . $group . ' ON "group_' . $group . '"."name"="item"."name"';
            $filter_sql .= 'AND "group_' . $group . '"."group"="' . SQLite3::escapeString($group) . '" ';
        }
    }
    ?>

    <h1>Items
        <small><?php echo $filters; ?></small>
    </h1>

    <?php
    foreach ($mods as $mod) {
        $output_mod = false;
        ob_start();
        ?>
        <div class="itemgroup">
            <h2><?php echo $mod ? 'mod:' . $mod : 'unknown'; ?></h2>

            <div class="row">
                <?php
                foreach (array('tool', 'node', 'craft') as $type) {
                    $sql = '
                        SELECT "item"."id", "item"."name", "item"."image", "item"."description"
                        FROM "item"
                        ' . $filter_join . '
                        WHERE "type"="' . $type . '"
                        AND "mod"="' . $mod . '"
                        ' . $filter_sql . '
                        ORDER BY "item"."name"
                    ';
                    $q = $db->query($sql);
                    ?>
                    <div class="span4">
                        <h3><?php echo 'type:' . $type; ?></h3>
                        <?php
                        while ($row = $q->fetchArray()) {
                            echo gamewiki::item($row['name'], null, true);
                            $output_mod = true;
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
        $contents = ob_get_clean();
        if ($output_mod) echo $contents;
    }
    ?>
</div>
<div id="footer"></div>
</body>
</html>