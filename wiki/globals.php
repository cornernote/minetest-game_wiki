<?php
require('config.php');

function debug($debug)
{
    echo '<pre>';
    print_r($debug);
    echo '</pre>';
}

function images($images, $options = array())
{
    $_images = array();
    foreach ($images as $image) {
        $_images[md5(serialize($image))] = $image;
    }
    $output = array();
    foreach ($_images as $image) {
        if (is_scalar($image)) {
            $output[] = image($image, $options);
        }
        else {
            $output[] = image($image->name, $options);
        }
    }
    return implode(' ', $output);
}

function image($image, $options = array())
{
    if (!$image) {
        return $image;
    }
    if (!is_scalar($image)) {
        //debug($image);
        return '';
    }
    if (substr($image, 0, 14) == '[inventorycube') {
        return item_image(array('image' => $image));
    }
    if (strpos($image, '^') !== false) {
        $images = explode('^', $image);
        $image = $images[1];
    }
    if (strpos($image, '&') !== false) {
        $images = explode('&', $image);
        $image = $images[1];
    }
    if (strpos($image, '{') !== false) {
        $images = explode('{', $image);
        $image = $images[0];
    }
    //if (!file_exists('textures/' . $image) && !empty($options['download'])) {
    //    $it = new RecursiveDirectoryIterator($GLOBALS['path']);
    //    foreach (new RecursiveIteratorIterator($it) as $file) {
    //        if ($image == substr($file, strlen($image) * -1)) {
    //            copy($file, 'textures/' . $image);
    //        }
    //    }
    //}
    if (!file_exists('textures/' . $image)) {
        debug('cant find: ' . $image);
        return '';
    }
    $width = $height = '';
    if (empty($options['fullsize'])) {
        $width = 'width="' . (isset($options['width']) ? $options['width'] : '32px') . '" ';
        $height = 'height="' . (isset($options['height']) ? $options['height'] : '32px') . '" ';
    }
    $class = isset($options['class']) ? 'class="' . $options['class'] . ' "' : '';
    return '<img src="textures/' . $image . '" ' . $width . $height . $class . '/>';
}

function item_image($item, $options = array())
{
    $file = false;
    if (!$file && !empty($item['name'])) {
        $file = 'itemcubes/' . str_replace(':', '-', $item['name']) . '.png';
        if (!file_exists($file))
            $file = false;
    }
    if (!$file && !empty($item['image'])) {
        if (substr($item['image'], 0, 14) == '[inventorycube') {
            $file = 'itemcubes/' . $item['image'] . '.png';
            if (!file_exists($file))
                return '';
        }
    }
    if ($file) {
        $width = $height = '';
        if (empty($options['fullsize'])) {
            $width = 'width="' . (!empty($options['width']) ? $options['width'] : '32px') . '" ';
            $height = 'height="' . (!empty($options['height']) ? $options['height'] : '32px') . '" ';
        }
        $class = !empty($options['class']) ? 'class="' . $options['class'] . ' "' : '';
        return '<img src="' . $file . '" ' . $width . $height . $class . '/>';
    }
    if (!empty($item['image'])) {
        return image($item['image']);
    }
    return false;
}

function item($name, $quantity = null)
{
    $output = '';
    $name = SQLite3::escapeString(item_name($name));
    if (!$name || in_array($name, array('air', 'default:air'))) {
        return $name;
    }

    // check for an alias
    $alias = $GLOBALS['db']->querySingle('SELECT itemname FROM alias WHERE name="' . $name . '"');
    if ($alias) {
        $name = $alias;
    }

    // load the item
    $q = $GLOBALS['db']->query('SELECT id, data, type, name, image, description FROM item WHERE name="' . $name . '"');
    if ($item = $q->fetchArray()) {
        $output .= '<a class="item" href="item.php?name=' . $item['name'] . '">';
        $output .= '<span class="image">' . item_image($item) . '</span>';
        $output .= '<span class="description">' . $item['description'] . '&nbsp;</span>';
        $output .= '<span class="name">[' . $item['type'] . '][' . $item['name'] . ']' . ($quantity ? ' x' . $quantity : '') . '</span>';
        $output .= '</a>';
    }

    // no item found
    elseif (substr($name, 0, 6) == 'group:') {
        $output .= '<a href="items.php?group=' . substr($name, 6) . '">' . $name . '</a>';
    }

    // no item found
    else {
        $output .= $name . ' (missing item)';
    }

    return $output;
}

function head_tags()
{
    ob_start();
    ?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<style>
    body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
    }
</style>
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<style>
    h2 {
        border: 1px solid #ddd;
        border-width: 1px 0;
        margin: 1.5em 0 0.5em 0;
        padding: 0 0.4em;
        background: #eee;
    }

    a.item {
        display: block;
        border: 1px solid #ccc;
        width: 345px;
        padding: 10px;
        background: #FFF;
    }

    a.item:hover {
        text-decoration: none;
        background: #FFC;
    }

    a.item .image {
        float: left;
        margin: 3px 4px 0 0;
    }

    a.item .description {
        font-size: 120%;
        font-weight: bold;
        color: #333;
        display: block;
    }

    img.image {
        border: 1px solid #ccc;
    }

    .home h2 {
        margin: 0;
    }

    .home h2 a {
        display: block;
    }

    .home ul {
        list-style: none;
        margin: 0;
    }

    .home li {
        margin: 5px;
        display: block;
    }

    .home li a:hover {
        text-decoration: none;
    }

    .itemgroup h3 {
        border: 1px solid #ddd;
        border-width: 1px 0;
        margin: 0 0 0.5em 0;
        padding: 0 0.2em;
        background: #eee;
    }

    .itemgroup ul {
        list-style: none;
        margin: 0;
    }

    .itemgroup li {
        margin: 10px 0;
    }

    table.crafting {
        border: 5px solid #666;
    }

    table.crafting td {
        width: 373px;
        border: 2px solid #666;
        height: 64px;
    }

    table.crafting td a.item {
        margin: 0 auto;
    }
</style>

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php
    return ob_get_clean();
}

function menu()
{
    $page = basename($_SERVER['SCRIPT_NAME']);
    ob_start();
    ?>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="./">MineTest GameWiki</a>

            <div class="nav-collapse">
                <ul class="nav">
                    <li <?php echo in_array($page, array('items.php', 'item.php')) ? 'class="active"' : '' ?>>
                        <a href="items.php">Items</a></li>
                    <li <?php echo in_array($page, array('crafts.php', 'craft.php')) ? 'class="active"' : '' ?>>
                        <a href="crafts.php">Crafts</a></li>
                    <li <?php echo in_array($page, array('abms.php', 'abm.php')) ? 'class="active"' : '' ?>>
                        <a href="abms.php">ABMs</a></li>
                    <li <?php echo in_array($page, array('aliases.php')) ? 'class="active"' : '' ?>>
                        <a href="aliases.php">Aliases</a></li>
                    </li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>
<?php
    return ob_get_clean();
}

function item_names($items)
{
    $_items = array();
    if (is_array($items)) {
        foreach ($items as $name) {
            if (is_array($name)) {
                $_items = array_merge($_items, item_names($name));
            }
            else {
                $_items[] = $name;
            }
        }
    }
    else {
        $_items[] = item_name($items);
    }
    foreach ($_items as $k => $v) if (!$v) unset($_items[$k]);
    return array_unique($_items);
}

function item_name($name)
{
    $name = str_ireplace(array('tool ', 'node ', 'craft ', 'toolitem ', 'nodeitem ', 'craftitem ', '"'), '', $name);
    $name = explode(' ', $name);
    $name = $name[0];
    return $name;
}

function craft_recipe($recipe, $type)
{
    $output = '<table class="crafting">';
    if (in_array($type, array('fuel', 'cooking'))) {
        $output .= '<tr>';
        $output .= '<td>' . ($type == 'cooking' ? item($recipe) : '') . '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<td>' . ($type == 'fuel' ? item('default:furnace') : item('default:furnace_active')) . '</td>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<td>' . ($type == 'fuel' ? item($recipe) : '') . '</td>';
        $output .= '</tr>';
    }
    else {
        if (is_array($recipe)) {
            if (is_array($recipe[0])) {
                $output .= '<tr>';
                $output .= '<td>' . (isset($recipe[0][0]) ? item($recipe[0][0]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[0][1]) ? item($recipe[0][1]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[0][2]) ? item($recipe[0][2]) : '') . '</td>';
                $output .= '</tr>';
                $output .= '<tr>';
                $output .= '<td>' . (isset($recipe[1][0]) ? item($recipe[1][0]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[1][1]) ? item($recipe[1][1]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[1][2]) ? item($recipe[1][2]) : '') . '</td>';
                $output .= '</tr>';
                $output .= '<tr>';
                $output .= '<td>' . (isset($recipe[2][0]) ? item($recipe[2][0]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[2][1]) ? item($recipe[2][1]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[2][2]) ? item($recipe[2][2]) : '') . '</td>';
                $output .= '</tr>';
            }
            else {
                $output .= '<tr>';
                $output .= '<td>' . (isset($recipe[0]) ? item($recipe[0]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[1]) ? item($recipe[1]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[2]) ? item($recipe[2]) : '') . '</td>';
                $output .= '</tr>';
                $output .= '<tr>';
                $output .= '<td>' . (isset($recipe[3]) ? item($recipe[3]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[4]) ? item($recipe[4]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[5]) ? item($recipe[5]) : '') . '</td>';
                $output .= '</tr>';
                $output .= '<tr>';
                $output .= '<td>' . (isset($recipe[6]) ? item($recipe[6]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[7]) ? item($recipe[7]) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[8]) ? item($recipe[8]) : '') . '</td>';
                $output .= '</tr>';
            }
        }
        else {
            $recipe = explode(' ', $recipe);
            $output .= '<tr>';
            $output .= '<td>' . (isset($recipe[0]) ? item($recipe[0]) : '') . '</td>';
            $output .= '<td>' . (isset($recipe[1]) ? item($recipe[1]) : '') . '</td>';
            $output .= '<td>' . (isset($recipe[2]) ? item($recipe[2]) : '') . '</td>';
            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td>' . (isset($recipe[3]) ? item($recipe[3]) : '') . '</td>';
            $output .= '<td>' . (isset($recipe[4]) ? item($recipe[4]) : '') . '</td>';
            $output .= '<td>' . (isset($recipe[5]) ? item($recipe[5]) : '') . '</td>';
            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td>' . (isset($recipe[6]) ? item($recipe[6]) : '') . '</td>';
            $output .= '<td>' . (isset($recipe[7]) ? item($recipe[7]) : '') . '</td>';
            $output .= '<td>' . (isset($recipe[8]) ? item($recipe[8]) : '') . '</td>';
            $output .= '</tr>';
        }
    }
    $output .= '</table>';
    return $output;
}

?>