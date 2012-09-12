<?php require('globals.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Craft :: <?php echo $GLOBALS['name']; ?></title>
    <?php echo head_tags(); ?>
</head>

<body>
<?php echo menu(); ?>
<div class="container">

    <?php
    $mods = get_mods();
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
    $mods = get_mods();
    foreach ($mods as $mod) {
        $output_mod = false;
        ob_start();
        echo '<h2>mod:' . ($mod ? $mod : 'no-mod') . '</h2>';
        $q = $GLOBALS['db']->query('SELECT id, mod, type, data, output, quantity FROM "craft" ' . $filter_join . ' WHERE mod="' . $mod . '" ' . $filter_sql . ' ORDER BY output');
        echo '<table class="table">';
        echo '<tr>';
        echo '<th width="100">Type</th>';
        echo '<th width="100">Mod</th>';
        echo '<th>Recipe</th>';
        echo '<th>Output</th>';
        echo '<th width="100">&nbsp;</th>';
        echo '</tr>';
        while ($row = $q->fetchArray()) {
			$output_mod = true;
			$data = json_decode($row['data']);
            echo '<tr>';
            echo '<td>' . $row['type'] . '</td>';
            echo '<td>' . $row['mod'] . '</td>';
            echo '<td>' . (isset($data->options->recipe) ? craft_recipe($data->options->recipe, $row['type'], true) : $row['type']) . '</td>';
            echo '<td>' . ($row['output'] ? item($row['output'], $row['quantity']) : 'no output') . '</td>';
            echo '<td><a href="craft.php?id=' . $row['id'] . '" class="btn">view craft</a></td>';
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