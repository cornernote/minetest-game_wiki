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
    <title>View Mod :: <?php echo $GLOBALS['name']; ?></title>
    <?php include('include/head_tags.php'); ?>
</head>

<body>
<?php include('include/menu.php'); ?>
<div class="container">

    <?php
    $mod = SQLite3::escapeString($_GET['mod']);
    $filter_join = $filter_sql = '';
    ?>
    <h1><?php echo $_GET['mod']; ?></h1>

    <div class="itemgroup">
        <h2>Items</h2>

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

    <div class="itemgroup">
        <h2>Crafts</h2>

        <div class="row">
            <?php
            foreach (array(array('crafting', 'shapeless'), array('cooking'), array('fuel')) as $type) {
                $pasteable = array();

                // format the sql as "crafting" instead of crafting
                $_type = array();
                foreach ($type as $_types) {
                    $_type[] = '"' . $_types . '"';
                }
                $sql = '
                        SELECT id, mod, type, data, output, quantity
                        FROM "craft" ' . $filter_join . '
                        WHERE "type" IN (' . implode(', ', $_type) . ') AND mod="' . $mod . '" ' . $filter_sql . '
                        ORDER BY output
                    ';
                $q = $db->query($sql);
                $rows = array();
                while ($row = $q->fetchArray()) {
                    $output_mod = true;
                    $data = json_decode($row['data']);
                    $rows[] = $row;
                    if (isset($data->options->recipe)) {
                        $pasteable[] = gamewiki::craft_recipe_paste($row['output'] . ' ' . $row['quantity'], $data->options->recipe, $row['type']);
                    }
                }
                ?>
                <div class="span4">
                    <h3><?php echo 'types:' . implode(',', $type); ?></h3>
                    <?php
                    echo '<p style="text-align:right;"><a href="javascript:return false;" onclick="$(\'#pasteable_' . $type . '_' . $mod . '\').toggle();$(\'#table_' . $type . '_' . $mod . '\').toggle();">toggle pasteable</a></p>';
                    echo '<pre id="pasteable_' . $type . '_' . $mod . '" style="display:none;">' . implode("\n\n", $pasteable) . '</pre>';
                    echo '<table class="table" id="table_' . $type . '_' . $mod . '">';
                    echo '<tr>';
                    echo '<th>Recipe</th>';
                    echo '<th>Output</th>';
                    echo '<th style="width:100px;">&nbsp;</th>';
                    echo '</tr>';
                    foreach ($rows as $row) {
                        $data = json_decode($row['data']);
                        echo '<tr>';
                        echo '<td>' . (isset($data->options->recipe) ? gamewiki::craft_recipe($data->options->recipe, $row['type'], true) : $row['type']) . '</td>';
                        if ($type == 'fuel') {
                            echo '<td>' . gamewiki::item('default:furnace_active', null, true) . '</td>';
                        }
                        else {
                            echo '<td>' . ($row['output'] ? gamewiki::item($row['output'], $row['quantity'], true) : 'no output') . '</td>';
                        }
                        echo '<td><a href="craft.php?id=' . $row['id'] . '" class="btn">view craft</a></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <h2>ABMs</h2>
    <?php
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
    ?>

    <h2>Aliases</h2>
    <?php
    $q = $db->query('SELECT id, mod, data FROM ' . $filter_join . ' "alias" WHERE mod="' . $mod . '" ' . $filter_sql . ' ORDER BY mod');
    echo '<table class="table">';
    echo '<tr>';
    echo '<th style="width:100px;">Mod</th>';
    echo '<th style="width:100px;">Alias</th>';
    echo '<th>Item</th>';
    echo '</tr>';
    while ($row = $q->fetchArray()) {
        $output_mod = true;
        $data = json_decode($row['data']);
        echo '<tr>';
        echo '<td>' . $row['mod'] . '</td>';
        echo '<td>' . $data->name . '</td>';
        echo '<td>' . gamewiki::item($data->options, null, true) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    ?>

</div>
<div id="footer"></div>
</body>
</html>