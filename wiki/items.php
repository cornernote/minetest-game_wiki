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

    <?php
    $mods = array();
    $q = $GLOBALS['db']->query('SELECT mod FROM "item" WHERE mod!="unknown" AND mod!="" GROUP BY mod ORDER BY mod');
    while ($row = $q->fetchArray()) {
        $mods[] = $row['mod'];
    }
    $filters = '';
    $group_sql = $group_join = '';
    if (isset($_GET['group'])) {
        $filters .= '[group:' . $_GET['group'] . ']';
        $group_join = 'LEFT JOIN group_to_itemname ON "group_to_itemname"."name"="item"."name"';
        $group_sql = 'AND "group"="' . SQLite3::escapeString($_GET['group']) . '"';
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
                    ?>
                    <div class="span4">
                        <h3><?php echo 'type:' . $type; ?></h3>
                        <?php
                        $sql = '
							SELECT "item"."id", "item"."name", "item"."image", "item"."description" 
							FROM "item"
							' . $group_join . '
							WHERE "type"="' . $type . '"
							AND "mod"="' . $mod . '" 
							' . $group_sql . '
							ORDER BY "item"."name"
						';
                        $q = $GLOBALS['db']->query($sql);
                        echo '<ul>';
                        while ($row = $q->fetchArray()) {
                            echo '<li>' . item($row['name']) . '</li>';
                            $output_mod = true;
                            $output_type = true;
                        }
                        echo '</ul>';
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
</body>
</html>