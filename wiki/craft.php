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
    $id = SQLite3::escapeString($_GET['id']);
    $q = $GLOBALS['db']->query('SELECT id, output, mod, quantity, type, data FROM "craft" WHERE id = "' . $id . '"');
    $row = $q->fetchArray();
    $data = json_decode($row['data']);

    echo '<h1>Craft <small>' . ($row['type'] ? $row['type'] : 'unknown') . '</small></h1>';

    echo '<h2>Output</h2>';
    echo item($row['output'], $row['quantity']);

    if (isset($data->options) && isset($data->options->recipe)) {
        echo '<h2>Recipe</h2>';
        echo craft_recipe($data->options->recipe, $row['type']);
    }

    echo '<h2>Data</h2>';
    echo '<h3>mod:' . $row['mod'] . '</h3>';
    print '<pre>';
    print_r($data);
    print '</pre>';


    ?>

</div>
</body>
</html>