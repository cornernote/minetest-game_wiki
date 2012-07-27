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
    $q = $GLOBALS['db']->query('SELECT id, mod, data FROM "abm" WHERE id = "' . $id . '"');
    $row = $q->fetchArray();
    $data = json_decode($row['data']);
    echo '<h1>ABM</h1>';

    echo '<h2>Node Names</h2>';
    echo '<table class="table">';
    if (isset($data->options)) {
        if (!empty($data->options->nodenames)) {
            if (is_array($data->options->nodenames)) foreach ($data->options->nodenames as $nodename) {
                echo '<tr>';
                echo '<td>';
                echo item($nodename);
                echo '</td>';
                echo '</tr>';
            }
            else {
                echo '<tr>';
                echo '<td>';
                echo item($data->nodenames);
                echo '</td>';
                echo '</tr>';
            }
        }
    }
    echo '</table>';

    echo '<h2>Neighbors</h2>';
    echo '<table class="table">';
    if (isset($data->options)) {
        if (!empty($data->options->neighbors)) {
            if (is_array($data->options->neighbors)) foreach ($data->options->neighbors as $neighbor) {
                echo '<tr>';
                echo '<td>';
                echo item($neighbor);
                echo '</td>';
                echo '</tr>';
            }
            else {
                echo '<tr>';
                echo '<td>';
                echo item($data->neighbors);
                echo '</td>';
                echo '</tr>';
            }
        }
    }
    echo '</table>';

    echo '<h2>Data</h2>';
    echo '<h3>mod:' . $row['mod'] . '</h3>';
    print '<pre>';
    print_r($data);
    print '</pre>';
    ?>

</div>
</body>
</html>