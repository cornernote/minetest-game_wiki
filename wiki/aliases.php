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
    <title>List Aliases :: <?php echo $GLOBALS['name']; ?></title>
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

    <h1>Aliases
		<small><?php echo $filters; ?></small>
	</h1>

	<?php
    foreach ($mods as $mod) {
        $output_mod = false;
        ob_start();
        echo '<h2>mod:' . ($mod ? $mod : 'no-mod') . '</h2>';
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
        $contents = ob_get_clean();
        if ($output_mod) echo $contents;
	}
    ?>

</div>
<div id="footer"></div>
</body>
</html>