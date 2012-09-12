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
    <title>View Item :: <?php echo $GLOBALS['name']; ?></title>
    <?php include('include/head_tags.php'); ?>
</head>

<body>
<?php include('include/menu.php'); ?>
<div class="container">

    <?php
    $name = SQLite3::escapeString($_GET['name']);
    $q = $db->query('SELECT id, mod, type, name, description, data FROM "item" WHERE name = "' . $name . '"');
    $row = $q->fetchArray();
    $data = json_decode($row['data']);
    ?>
    <h1><?php echo $row['description'] ? $row['description'] : $row['name']; ?>
        <small><?php echo '[' . $row['type'] . '][' . $row['name'] . ']'; ?></small>
    </h1>
    <?php

    // item
    echo gamewiki::item($row['name']);

    // images
    $images = array_merge(
        isset($data->options->inventory_image) ? is_array($data->options->inventory_image) ? $data->options->inventory_image : array($data->options->inventory_image) : array(),
        isset($data->options->tiles) ? (array)$data->options->tiles : array(),
        isset($data->options->tile_images) ? (array)$data->options->tile_images : array()
    );
    if (!empty($images)) {
        echo '<h2>Images</h2>';
        echo gamewiki::images($images, array('fullsize' => true, 'class' => 'image'));
    }

    // created by crafts
    $q = $db->query('SELECT id, type, data FROM "craft" WHERE output = "' . $name . '"');
    $rows = array();
    while ($row_c = $q->fetchArray()) {
        $data_c = json_decode($row_c['data']);
        $rows[] = '<a href="craft.php?id=' . $row_c['id'] . '" class="btn">view craft</a><br/><br/>' . gamewiki::craft_recipe($data_c->options->recipe, $row_c['type']);
    }
    if ($rows) {
        echo '<h2>Created By Crafts</h2>';
        echo implode('<hr/>', $rows);
    }

    // used for crafts
    $output = false;
    ob_start();
    echo '<h2>Used For Crafts</h2>';
    $q = $db->query('SELECT id, mod, type, output, quantity FROM "craft_to_itemname" LEFT JOIN "craft" ON "craft"."id"="craft_to_itemname"."craft_id" WHERE name = "' . $name . '" ORDER BY output');
    echo '<table class="table">';
    echo '<tr>';
    echo '<th style="width:100px">Mod</th>';
    echo '<th style="width:100px">Type</th>';
    echo '<th>Output</th>';
    echo '<th style="width:100px">&nbsp;</th>';
    echo '</tr>';
    while ($row_c = $q->fetchArray()) {
        echo '<tr>';
        echo '<td>' . $row_c['mod'] . '</td>';
        echo '<td>' . $row_c['type'] . '</td>';
        echo '<td>' . ($row_c['output'] ? gamewiki::item($row_c['output'], $row_c['quantity']) : 'no output') . '</td>';
        echo '<td><a href="craft.php?id=' . $row_c['id'] . '" class="btn">view craft</a></td>';
        echo '</tr>';
        $output = true;
    }
    echo '</table>';
    $contents = ob_get_clean();
    if ($output) echo $contents;

    // used by abms
    $rows = array();
    $q = $db->query('SELECT id FROM "abm_to_itemname" LEFT JOIN "abm" ON "abm"."id"="abm_to_itemname"."abm_id" WHERE name = "' . $name . '"');
    while ($row_a = $q->fetchArray()) {
        $rows[] = '<a href="abm.php?id=' . $row_a['id'] . '" class="btn">view abm</a>';
    }
    if ($rows) {
        echo '<h2>Used By ABMs</h2>';
        echo implode(' ', $rows);
    }

    echo '<h2>Data</h2>';
    echo '<h3>mod:' . $row['mod'] . '</h3>';
    print '<pre>';
    print_r($data);
    print '</pre>';
    ?>

</div>
<div id="footer"></div>
</body>
</html>