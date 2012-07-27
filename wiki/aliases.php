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
    <h1>Aliases</h1>

    <?php
    $q = $GLOBALS['db']->query('SELECT id, mod, data FROM "alias" ORDER BY mod');
    echo '<table class="table">';
    echo '<tr>';
    echo '<th width="100">Mod</th>';
    echo '<th width="100">Alias</th>';
    echo '<th>Item</th>';
    echo '</tr>';
    while ($row = $q->fetchArray()) {
        $data = json_decode($row['data']);
        echo '<tr>';
        echo '<td>' . $row['mod'] . '</td>';
        echo '<td>' . $data->name . '</td>';
        echo '<td>' . item($data->options) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    ?>

</div>
</body>
</html>