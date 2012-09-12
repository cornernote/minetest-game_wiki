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
    <title>View Craft :: <?php echo $GLOBALS['name']; ?></title>
    <?php include('include/head_tags.php'); ?>
</head>

<body>
<?php include('include/menu.php'); ?>
<div class="container">

    <?php
    $id = SQLite3::escapeString($_GET['id']);
    $q = $db->query('SELECT id, output, mod, quantity, type, data FROM "craft" WHERE id = "' . $id . '"');
    $row = $q->fetchArray();
    $data = json_decode($row['data']);
    echo '<h1>Craft <small>' . ($row['type'] ? $row['type'] : 'unknown') . '</small></h1>';

    echo '<h2>Output</h2>';
    echo gamewiki::item($row['output'], $row['quantity']);

    if (isset($data->options) && isset($data->options->recipe)) {
        echo '<h2>Recipe</h2>';
        echo gamewiki::craft_recipe($data->options->recipe, $row['type']);
        echo '<h2>Pasteable</h2>';
        debug(gamewiki::craft_recipe_paste($row['output'] . ' ' . $row['quantity'], $data->options->recipe, $row['type']));
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