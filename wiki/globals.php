<?php
require('config.php');

function debug($debug)
{
    echo '<pre>';
    print_r($debug);
    echo '</pre>';
}

function item_image_file($item)
{
    if (!empty($item['name'])) {
        $file = 'itemcubes/' . str_replace(':', '_', trim($item['name'], ':')) . '.png';
        if (file_exists($file))
            return $file;
    }
    if (!empty($item['image'])) {
        if (substr($item['image'], 0, 14) == '[inventorycube') {
            $file = 'itemcubes/' . $item['image'] . '.png';
            if (file_exists($file))
                return $file;
        }
    }
    if (empty($item['image'])) {
        return '';
    }
    $file = $item['image'];
    if (strpos($file, '^') !== false) {
        $images = explode('^', $file);
        $file = $images[0];
    }
    if (strpos($file, '&') !== false) {
        $images = explode('&', $file);
        $file = $images[1];
    }
    if (strpos($file, '{') !== false) {
        $images = explode('{', $file);
        $file = $images[0];
    }
    return 'textures/' . $file;
}

function item_image($item, $tooltip = null)
{
    $file = item_image_file($item);
    if ($file) {
        if ($tooltip === null) {
            $tooltip = ' data-toggle="tooltip" title="' . $item['description'] . ' [' . $item['type'] . '][' . trim($item['name'], ':') . ']"';
        }
        return '<img src="{{site.baseurl}}/assets/img/items/' . $file . '"' . $tooltip . '>';
    }
    return false;
}

function group_image($group)
{
    $sql = '
        SELECT "group_to_itemname"."name", "item"."image"
        FROM "group_to_itemname"
        LEFT JOIN "item" ON "item"."name" = "group_to_itemname"."name"
        WHERE "group_to_itemname"."group" = "' . substr($group, 6) . '"
        AND "item"."hidden" = 0
        GROUP BY "group_to_itemname"."name"
    ';
    $q = $GLOBALS['db']->query($sql);
    $tooltip = ' data-toggle="tooltip" title="Group: ' . ucwords(str_replace('_', ' ', substr($group, 6))) . ' [group][' . substr($group, 6) . ']"';
    $images = array();
    while ($item = $q->fetchArray()) {
        $images[] = item_image($item, false);
    }
    while (count($images) < 3) {
        $images[] = '<img src="{{site.baseurl}}/assets/img/transparent.png' . '">';
    }
    if ($images) {
        return '<span class="item-group"' . $tooltip . '>' . implode('', array_slice($images, 0, 4)) . '</span>';
    }
    return '<img src="{{site.baseurl}}/assets/img/items/group.png' . '"' . $tooltip . '>';
}

function item($name, $quantity = null)
{
    $output = '';
    $name = SQLite3::escapeString(item_name($name));
    if (!$name || in_array($name, array('air', 'default:air'))) {
        return $name;
    }

    // check for an alias
    //$alias = $GLOBALS['db']->querySingle('SELECT itemname FROM alias WHERE name="' . $name . '"');
    //if ($alias) {
    //    $name = $alias;
    //}

    // load the item
    $sql = 'SELECT id, data, type, name, image, description FROM item WHERE name="' . $name . '" OR name=":' . $name . '"';
    $q = $GLOBALS['db']->query($sql);
    if ($item = $q->fetchArray()) {
        $output .= '<a href="{{site.baseurl}}/items/' . str_replace(array(':', '_'), '-', trim($item['name'], ':')) . '/">';
        $output .= item_image($item);
        $output .= '</a>';
    } elseif (substr($name, 0, 6) == 'group:') {
        $group = substr($name, 6);
        $output .= '<a href="{{site.baseurl}}/items/group-' . str_replace(array('_', ' '), '-', $group) . '/">';
        if (strpos($group, ',')) {
            $tooltip = ' data-toggle="tooltip" title="MultiGroup: ' . ucwords(str_replace('_', ' ', str_replace(',', ' + ', $group))) . ' [group][' . $group . ']"';
            $output .= '<img src="{{site.baseurl}}/assets/img/items/group.png' . '"' . $tooltip . '>';
            $output .= '</a>';
        } else {
            $output .= group_image($name);
        }
        $output .= '</a>';
    } else {
        $output .= $name . ' (missing item)';
    }

    return $output;
}

function item_name($name)
{
    $name = str_ireplace(array('tool ', 'node ', 'craft ', 'toolitem ', 'nodeitem ', 'craftitem ', '"'), '', $name);
    $name = explode(' ', $name);
    $name = $name[0];
    $name = trim($name, ':');
    return $name;
}

function craft($recipe, $type)
{
    $output = '<div class="craft">' . "\n";
    if (in_array($type, array('fuel', 'cooking'))) {
        $output .= craft_furnace($recipe, $type);
    } else {
        if (is_array($recipe)) {
            if (is_array($recipe[0])) {
                $output .= craft_shape($recipe);
            } else {
                $output .= craft_shapeless($recipe);
            }
        } else {
            $recipe = explode(' ', $recipe);
            $output .= craft_shapeless($recipe);
        }
    }
    $output .= '</div>' . "\n";
    return $output;
}

function craft_shape($recipe)
{
    $output = "    " . '<div>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[0][0]) ? item($recipe[0][0]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[0][1]) ? item($recipe[0][1]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[0][2]) ? item($recipe[0][2]) : '') . '</span>' . "\n";
    $output .= "    " . '</div>' . "\n";
    $output .= "    " . '<div>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[1][0]) ? item($recipe[1][0]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[1][1]) ? item($recipe[1][1]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[1][2]) ? item($recipe[1][2]) : '') . '</span>' . "\n";
    $output .= "    " . '</div>' . "\n";
    $output .= "    " . '<div>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[2][0]) ? item($recipe[2][0]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[2][1]) ? item($recipe[2][1]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[2][2]) ? item($recipe[2][2]) : '') . '</span>' . "\n";
    $output .= "    " . '</div>' . "\n";
    return $output;
}

function craft_shapeless($recipe)
{
    $output = "    " . '<div>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[0]) ? item($recipe[0]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[1]) ? item($recipe[1]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[2]) ? item($recipe[2]) : '') . '</span>' . "\n";
    $output .= "    " . '</div>' . "\n";
    $output .= "    " . '<div>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[3]) ? item($recipe[3]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[4]) ? item($recipe[4]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[5]) ? item($recipe[5]) : '') . '</span>' . "\n";
    $output .= "    " . '</div>' . "\n";
    $output .= "    " . '<div>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[6]) ? item($recipe[6]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[7]) ? item($recipe[7]) : '') . '</span>' . "\n";
    $output .= "        " . '<span>' . (isset($recipe[8]) ? item($recipe[8]) : '') . '</span>' . "\n";
    $output .= "    " . '</div>' . "\n";
    return $output;
}

function craft_furnace($recipe, $type)
{
    $output = "    " . '<div>' . "\n";
    $output .= "        " . '<span>' . ($type == 'cooking' ? item($recipe) : '') . '</span>' . "\n";
    $output .= "    " . '</div>' . "\n";
    $output .= "    " . '<div>' . "\n";
    $output .= "        " . '<span>' . ($type == 'fuel' ? item('default:furnace') : item('default:furnace')) . '</span>' . "\n";
    $output .= "    " . '</div>' . "\n";
    $output .= "    " . '<div>' . "\n";
    $output .= "        " . '<span>' . ($type == 'fuel' ? item($recipe) : '') . '</span>' . "\n";
    $output .= "    " . '</div>' . "\n";
    return $output;
}