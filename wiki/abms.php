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
    <title>List ABMs :: <?php echo $GLOBALS['name']; ?></title>
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

    <h1>ABMs
        <small><?php echo $filters; ?></small>
    </h1>

    <?php
    $mods = gamewiki::get_mods();
    foreach ($mods as $mod) {
        $output_mod = false;
        ob_start();
        echo '<h2>mod:' . ($mod ? $mod : 'no-mod') . '</h2>';
        $q = $db->query('SELECT id, mod, data FROM "abm" ' . $filter_join . ' WHERE mod="' . $mod . '" ' . $filter_sql . ' ORDER BY mod');
        echo '<table class="table">';
        echo '<tr>';
        echo '<th style="width:100px;">Mod</th>';
        echo '<th>Node Names</th>';
        echo '<th>Neighbors</th>';
        echo '<th style="width:100px;">C / I</th>';
        echo '<th style="width:100px;">&nbsp;</th>';
        echo '<td>';
        while ($row = $q->fetchArray()) {
            $output_mod = true;
            $data = json_decode($row['data']);
            echo '<tr>';
            echo '<td>' . $row['mod'] . '</td>';
            echo '<td>';
            echo '<div class="itemgroup">';
            if (isset($data->options)) {
                if (!empty($data->options->nodenames)) {
                    if (is_array($data->options->nodenames)) foreach ($data->options->nodenames as $nodename) {
                        echo gamewiki::item($nodename, null, true);
                    }
                    elseif (isset($data->nodenames)) {
                        echo gamewiki::item($data->nodenames, null, true);
                    }
                }
            }
            echo '</div>';
            echo '</td>';
            echo '<td>';
            echo '<div class="itemgroup">';
            if (isset($data->options)) {
                if (!empty($data->options->neighbors)) {
                    if (is_array($data->options->neighbors)) foreach ($data->options->neighbors as $neighbor) {
                        echo gamewiki::item($neighbor, null, true);
                    }
                    else {
                        echo gamewiki::item($data->neighbors, null, true);
                    }
                }
            }
            echo '</div>';
            echo '</td>';
            echo '<td>' . (isset($data->options->chance) ? $data->options->chance : '?') . ' / ' . (isset($data->options->interval) ? $data->options->interval : '?') . '</td>';
            echo '<td><a href="abm.php?id=' . $row['id'] . '" class="btn">view abm</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        $contents = ob_get_clean();
        if ($output_mod) echo $contents;
    }
    ?>

</div>
<div id="footer"></div>
</body>
</html>