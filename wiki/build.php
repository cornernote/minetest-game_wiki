<?php
require('globals.php');
$path = '/vagrant/minetest/minetest-skyblock-gh-pages';


//
// Build items.md
//
$contents = '---' . "\n";
$contents .= 'layout: default' . "\n";
$contents .= 'title: Items' . "\n";
$contents .= 'heading: Items' . "\n";
//$contents .= 'description:  ' . "\n";
$contents .= 'permalink: /items/' . "\n";
$contents .= '---' . "\n";
foreach (array('tool', 'craft', 'node') as $type) {
    $contents .= "\n\n" . '## ' . ucfirst($type) . 's' . "\n\n";
    $sql = '
        SELECT "item"."id", "item"."name", "item"."image", "item"."description"
        FROM "item"
        WHERE "type"="' . $type . '"
        AND "hidden" = 0
        ORDER BY "item"."name"
    ';
    $q = $GLOBALS['db']->query($sql);
    $contents .= '<ul class="list-items clearfix">' . "\n";
    while ($row = $q->fetchArray()) {
        $contents .= '<li>' . item($row['name']) . '</li>' . "\n";
    }
    $contents .= '</ul>' . "\n";
}
file_put_contents($path . '/items.md', $contents);


//
// Build items/*.md
//

$sql = '
	SELECT "id", "name", "image", "description", "type"
	FROM "item"
	WHERE "hidden" = 0
	ORDER BY "name"
';
$q = $GLOBALS['db']->query($sql);
while ($row = $q->fetchArray()) {
    $contents = '---' . "\n";
    $contents .= 'layout: default' . "\n";
    $contents .= 'title: ' . (isset($row['description']) ? $row['description'] : $row['name']) . "\n";
    $contents .= 'heading: ' . (isset($row['description']) ? $row['description'] : $row['name']) . "\n";
    $contents .= 'description: "[' . $row['type'] . '][' . trim($row['name'], ':') . ']"' . "\n";
    $contents .= 'permalink: /items/' . str_replace(array(':', '_'), '-', trim($row['name'], ':')) . '/' . "\n";
    $contents .= 'icon: /items/' . item_image_file($row) . "\n";
    $contents .= '---' . "\n";

    // created by crafts
    $q2 = $GLOBALS['db']->query('SELECT id, type, data FROM "craft" WHERE output = "' . trim($row['name'], ':') . '"');
    $rows = array();
    while ($row_c = $q2->fetchArray()) {
        $data_c = json_decode($row_c['data'], true);
        $rows[] = craft($data_c['options']['recipe'], $row_c['type']);
    }
    if ($rows) {
        $contents .= "\n\n" . '## Created by Crafts' . "\n\n";
        $contents .= implode("\n", $rows);
    }

    // used for crafts
    $q2 = $GLOBALS['db']->query('SELECT id, mod, type, output, quantity FROM "craft_to_itemname" LEFT JOIN "craft" ON "craft"."id"="craft_to_itemname"."craft_id" WHERE name = "' . trim($row['name'], ':') . '" GROUP BY craft.output ORDER BY output');
    $rows = array();
    while ($row_c = $q2->fetchArray()) {
        if ($row_c['type'] == 'fuel') {
            $rows[] = item('fire:basic_flame');
        } else {
            $rows[] = $row_c['output'] ? item($row_c['output'], $row_c['quantity']) : print_r($row_c, true);
        }
    }
    if ($rows) {
        $contents .= "\n\n" . '## Used for Crafts' . "\n\n";
        $contents .= '<ul class="list-items clearfix">' . "\n";
        foreach ($rows as $_row) {
            $contents .= "    <li>" . $_row . "</li>\n";
        }
        $contents .= '</ul>' . "\n";
    }

    // belongs to groups
    $sql = '
        SELECT "group"
        FROM "group_to_itemname"
        WHERE "name" = "' . trim($row['name'], ':') . '" OR "name" = ":' . trim($row['name'], ':') . '"
        GROUP BY "group"
        ORDER BY "group"
    ';
    $q2 = $GLOBALS['db']->query($sql);
    $groups = array();
    while ($row_g = $q2->fetchArray()) {
        $groups[] = "    <li>" . item('group:' . $row_g['group']) . "</li>\n";
    }
    if (!empty($groups)) {
        $contents .= "\n\n" . '## Belongs to Groups' . "\n\n";
        $contents .= '<ul class="list-items clearfix">' . "\n";
        $contents .= implode('', $groups);
        $contents .= '</ul>' . "\n";
    }

    file_put_contents($path . '/items/' . str_replace(array(':', '_'), '-', trim($row['name'], ':')) . '.md', $contents);
}

//
// Build items/fire-basic-flame.md
//
$contents = '---' . "\n";
$contents .= 'layout: default' . "\n";
$contents .= 'title: Fire' . "\n";
$contents .= 'heading: Fire' . "\n";
$contents .= 'description: "[node][fire:basic_flame]"' . "\n";
$contents .= 'permalink: /items/fire-basic-flame/' . "\n";
$contents .= 'icon: /items/itemcubes/fire_basic_flame.png' . "\n";
$contents .= '---' . "\n";

// created by crafts
$q2 = $GLOBALS['db']->query('SELECT id, type, data FROM "craft" WHERE type = "fuel"');
$rows = array();
$contents .= "\n\n" . '## Created by Fuels' . "\n\n";
$contents .= '<ul class="list-items clearfix">' . "\n";
while ($row_c = $q2->fetchArray()) {
    $data_c = json_decode($row_c['data'], true);
    $recipe = $data_c['options']['recipe'];
    $sql = '
        SELECT "id", "name", "image", "description", "type"
        FROM "item"
        WHERE "hidden" = 0
        AND ("name" = "' . $recipe . '" OR "name" = ":' . $recipe . '")
        ORDER BY "name"
    ';
    $q = $GLOBALS['db']->query($sql);
    $item = $q->fetchArray();
    if ($item) {
        $contents .= '<li>' . item($recipe) . '</li>' . "\n";
    } elseif (strpos($recipe, 'group:') !== false) {
        $contents .= '<li>' . item($recipe) . '</li>' . "\n";
    }
}
$contents .= '</ul>' . "\n";

file_put_contents($path . '/items/fire-basic-flame.md', $contents);


//
// Build items/group-*.md
//

$sql = '
    SELECT "group"
    FROM "group_to_itemname"
    GROUP BY "group"
    ORDER BY "group"
';
$q = $GLOBALS['db']->query($sql);
while ($row = $q->fetchArray()) {

    $contents = '---' . "\n";
    $contents .= 'layout: default' . "\n";
    $contents .= 'title: "Group: ' . ucwords(str_replace('_', ' ', $row['group'])) . '"' . "\n";
    $contents .= 'heading: "Group: ' . ucwords(str_replace('_', ' ', $row['group'])) . '"' . "\n";
    $contents .= 'description: "[group][' . $row['group'] . ']"' . "\n";
    $contents .= 'permalink: /items/group-' . str_replace('_', '-', $row['group']) . '/' . "\n";
    $contents .= '---' . "\n";

    // items in group
    $sql = '
        SELECT "group_to_itemname"."name"
        FROM "group_to_itemname"
        LEFT JOIN "item" ON "item"."name" = "group_to_itemname"."name"
        WHERE "group_to_itemname"."group" = "' . $row['group'] . '"
        AND "item"."hidden" = 0
        GROUP BY "group_to_itemname"."name"
        ORDER BY "group_to_itemname"."name"
    ';
    $q2 = $GLOBALS['db']->query($sql);
    $items = array();
    while ($row_g = $q2->fetchArray()) {
        $items[] = "    <li>" . item($row_g['name']) . "</li>\n";
    }
    if ($items) {
        $contents .= "\n\n" . '## Items in Group' . "\n\n";
        $contents .= '<ul class="list-items clearfix">' . "\n";
        $contents .= implode('', $items);
        $contents .= '</ul>' . "\n";
    }

    // used for crafts
    $q2 = $GLOBALS['db']->query('
        SELECT id, mod, type, output, quantity
        FROM "craft_to_itemname"
        LEFT JOIN "craft" ON "craft"."id"="craft_to_itemname"."craft_id"
        WHERE name = "group:' . $row['group'] . '"
        GROUP BY craft.output ORDER BY output
    ');
    $rows = array();
    while ($row_c = $q2->fetchArray()) {
        if ($row_c['type'] == 'fuel') {
            $rows[] = item('fire:basic_flame');
        } else {
            $rows[] = $row_c['output'] ? item($row_c['output'], $row_c['quantity']) : print_r($row_c, true);
        }
    }
    if ($rows) {
        $contents .= "\n\n" . '## Used for Crafts' . "\n\n";
        $contents .= '<ul class="list-items clearfix">' . "\n";
        foreach ($rows as $_row) {
            $contents .= "    <li>" . $_row . "</li>\n";
        }
        $contents .= '</ul>';
    }

    file_put_contents($path . '/items/group-' . str_replace('_', '-', $row['group']) . '.md', $contents);
}


//
// Build items/multi-group-*.md
//

$groups = array();
$sql = 'SELECT "data", "output" FROM "craft"';
$q = $GLOBALS['db']->query($sql);
while ($row = $q->fetchArray()) {
    $data_c = json_decode($row['data'], true);
    if (!isset($data_c['options']['recipe'])) {
        continue;
    }
    $recipe = $data_c['options']['recipe'];
    if (is_array($recipe)) {
        if (is_array($recipe[0])) {
            foreach ($recipe as $v) {
                foreach ($v as $vv) {
                    if (substr($vv, 0, 6) == 'group:' && strpos($vv, ',')) {
                        $groups[$vv][] = $row['output'];
                    }
                }
            }
        } else {
            foreach ($recipe as $v) {
                if (substr($v, 0, 6) == 'group:' && strpos($v, ',')) {
                    $groups[$v][] = $row['output'];
                }
            }
        }
    } else {
        $recipe = explode(' ', $recipe);
        foreach ($recipe as $v) {
            if (substr($v, 0, 6) == 'group:' && strpos($v, ',')) {
                $groups[$v][] = $row['output'];
            }
        }
    }
}
foreach ($groups as $group => $outputs) {
    $group = substr($group, 6);
    $_groups = explode(',', $group);

    $contents = '---' . "\n";
    $contents .= 'layout: default' . "\n";
    $contents .= 'title: "MultiGroup: ' . ucwords(str_replace('_', ' ', str_replace(',', ' + ', $group))) . '"' . "\n";
    $contents .= 'heading: "MultiGroup: ' . ucwords(str_replace('_', ' ', str_replace(',', ' + ', $group))) . '"' . "\n";
    $contents .= 'description: "[group][' . $group . ']"' . "\n";
    $contents .= 'permalink: /items/group-' . str_replace('_', '-', $group) . '/' . "\n";
    $contents .= '---' . "\n";

    // items in group
    $joins = array();
    $wheres = array();
    foreach ($_groups as $k => $_group) {
        $joins[] = 'LEFT JOIN "group_to_itemname" AS "gi_' . $k . '" ON "item"."name" = "gi_' . $k . '"."name"';
        $wheres[] = '"gi_' . $k . '"."group" = "' . $_group . '"';
    }
    $sql = '
        SELECT "item"."name"
        FROM "item"
        ' . implode(' ', $joins) . '
        WHERE "item"."hidden" = 0
        AND ' . implode(' AND ', $wheres) . '
    ';
    $q2 = $GLOBALS['db']->query($sql);
    $items = array();
    while ($row_g = $q2->fetchArray()) {
        $items[] = "    <li>" . item($row_g['name']) . "</li>\n";
    }
    if ($items) {
        $contents .= "\n\n" . '## Items in Group' . "\n\n";
        $contents .= '<ul class="list-items clearfix">' . "\n";
        $contents .= implode('', $items);
        $contents .= '</ul>' . "\n";
    }

    // used for crafts
    $contents .= "\n\n" . '## Used for Crafts' . "\n\n";
    $contents .= '<ul class="list-items clearfix">' . "\n";
    foreach ($outputs as $output) {
        $contents .= "    <li>" . item($output) . "</li>\n";
    }
    $contents .= '</ul>' . "\n";

    // belongs to groups
    $contents .= "\n\n" . '## Belongs to Groups' . "\n\n";
    $contents .= '<ul class="list-items clearfix">' . "\n";
    foreach ($_groups as $_group) {
        $contents .= "    <li>" . item('group:' . $_group) . "</li>\n";
    }
    $contents .= '</ul>' . "\n";

    file_put_contents($path . '/items/multi-group-' . str_replace('_', '-', $group) . '.md', $contents);

}