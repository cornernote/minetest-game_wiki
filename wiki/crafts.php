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
        ob_start();
        ?>
        <div class="itemgroup">
            <h2><?php echo $mod ? 'mod:' . $mod : 'unknown'; ?></h2>

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

        <?php
        $contents = ob_get_clean();
        if ($output_mod)
            echo $contents;
    }
    ?>

</div>
<div id="footer"></div>
</body>
</html>