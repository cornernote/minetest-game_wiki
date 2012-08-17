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
    <h1>ABMs</h1>

    <?php
    $q = $GLOBALS['db']->query('SELECT id, mod, data FROM "abm" ORDER BY mod');
    echo '<table class="table">';
    echo '<tr>';
    echo '<th width="100">Mod</th>';
    echo '<th width="30%">Node Names</th>';
    echo '<th width="30%">Neighbors</th>';
    echo '<th width="100">C / I</th>';
    echo '<th width="100">&nbsp;</th>';
    echo '<td>';
    while ($row = $q->fetchArray()) {
        $data = json_decode($row['data']);
        echo '<tr>';
        echo '<td>' . $row['mod'] . '</td>';
        echo '<td>';
        echo '<div class="itemgroup"><ul>';
        if (isset($data->options)) {
            if (!empty($data->options->nodenames)) {
                if (is_array($data->options->nodenames)) foreach ($data->options->nodenames as $nodename) {
                    echo '<li>';
                    echo item($nodename);
                    echo '</li>';
                }
                elseif (isset($data->nodenames)) {
                    echo '<li>';
                    echo item($data->nodenames);
                    echo '</li>';
                }
            }
        }
        echo '</div></ul>';
        echo '</td>';
        echo '<td>';
        echo '<div class="itemgroup"><ul>';
        if (isset($data->options)) {
            if (!empty($data->options->neighbors)) {
                if (is_array($data->options->neighbors)) foreach ($data->options->neighbors as $neighbor) {
                    echo '<li>';
                    echo item($neighbor);
                    echo '</li>';
                }
                else {
                    echo '<li>';
                    echo item($data->neighbors);
                    echo '</li>';
                }
            }
        }
        echo '</div></ul>';
        echo '</td>';
        echo '<td>' . (isset($data->options->chance) ? $data->options->chance : '?') . ' / ' . (isset($data->options->interval) ? $data->options->interval : '?') . '</td>';
        echo '<td><a href="abm.php?id=' . $row['id'] . '" class="btn">view abm</a></td>';
        echo '</tr>';
    }
    echo '</table>';
    ?>

</div>
</body>
</html>