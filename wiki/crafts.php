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
    <title>List Crafts :: <?php echo $GLOBALS['name']; ?></title>
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
        $filter_sql .= 'AND mod="' . SQLite3::escapeString($_GET['mod']) . '" ';
    }
    ?>

    <h1>Crafts
        <small><?php echo $filters; ?></small>
    </h1>

    <?php
    $mods = gamewiki::get_mods();
    foreach ($mods as $mod) {
        $output_mod = false;
        $pasteable = array();
        $q = $db->query('SELECT id, mod, type, data, output, quantity FROM "craft" ' . $filter_join . ' WHERE mod="' . $mod . '" ' . $filter_sql . ' ORDER BY output');
        $rows = array();
        while ($row = $q->fetchArray()) {
            $data = json_decode($row['data']);
            $rows[] = $row;
            if (isset($data->options->recipe)) {
                $pasteable[] = gamewiki::craft_recipe_paste($row['output'] . ' ' . $row['quantity'], $data->options->recipe, $row['type']);
            }
        }
        ob_start();
        echo '<h2>mod:' . ($mod ? $mod : 'no-mod') . '</h2>';
        echo '<a href="javascript:return false;" onclick="$(\'#pasteable_' . $mod . '\').toggle();$(\'#table_' . $mod . '\').toggle();">toggle pasteable</a>';
        echo '<pre id="pasteable_' . $mod . '" style="display:none;">' . implode("\n\n", $pasteable) . '</pre>';
        echo '<table class="table" id="table_' . $mod . '">';
        echo '<tr>';
        echo '<th width="100">Type</th>';
        echo '<th width="100">Mod</th>';
        echo '<th>Recipe</th>';
        echo '<th>Output</th>';
        echo '<th width="100">&nbsp;</th>';
        echo '</tr>';
        foreach ($rows as $row) {
            $output_mod = true;
            $data = json_decode($row['data']);
            echo '<tr>';
            echo '<td>' . $row['type'] . '</td>';
            echo '<td>' . $row['mod'] . '</td>';
            echo '<td>' . (isset($data->options->recipe) ? gamewiki::craft_recipe($data->options->recipe, $row['type'], true) : $row['type']) . '</td>';
            echo '<td>' . ($row['output'] ? gamewiki::item($row['output'], $row['quantity']) : 'no output') . '</td>';
            echo '<td><a href="craft.php?id=' . $row['id'] . '" class="btn">view craft</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        $contents = ob_get_clean();
        if ($output_mod)
            echo $contents;
    }
    ?>

</div>
<div id="footer"></div>
</body>
</html>