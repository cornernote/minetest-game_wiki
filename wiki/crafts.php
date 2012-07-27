<?php require('globals.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MTGW</title>
    <?php echo head_tags(); ?>
</head>

<body>
<?php echo menu(); ?>
<div class="container">
    <h1>Crafts</h1>

    <?php
    $mods = array();
    $q = $GLOBALS['db']->query('SELECT mod FROM "craft" GROUP BY mod ORDER BY mod');
    while ($row = $q->fetchArray()) {
        $mods[] = $row['mod'];
    }
    foreach ($mods as $mod) {
        $q = $GLOBALS['db']->query('SELECT id, mod, type, output, quantity FROM "craft" WHERE mod="' . $mod . '" ORDER BY output');
        echo '<h2>mod:' . ($mod ? $mod : 'no-mod') . '</h2>';
        echo '<table class="table">';
        echo '<tr>';
        echo '<th width="100">Type</th>';
        echo '<th width="100">Mod</th>';
        echo '<th>Output</th>';
        echo '<th width="100">&nbsp;</th>';
        echo '</tr>';
        while ($row = $q->fetchArray()) {
            echo '<tr>';
            echo '<td>' . $row['type'] . '</td>';
            echo '<td>' . $row['mod'] . '</td>';
            echo '<td>' . ($row['output'] ? item($row['output'], $row['quantity']) : 'no output') . '</td>';
            echo '<td><a href="craft.php?id=' . $row['id'] . '" class="btn">view craft</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    ?>

</div>
</body>
</html>