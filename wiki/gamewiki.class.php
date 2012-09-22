<?php
/**
 * GameWiki for Minetest
 *
 * Copyright (c) 2012 cornernote, Brett O'Donnell <cornernote@gmail.com>
 *
 * Source Code: https://github.com/cornernote/minetest-gamewiki
 * License: GPLv3
 */
class gamewiki
{

    /**
     * @var array
     */
    static $paste_items = array();

    /**
     * Return an image from an array of image files
     *
     * @param $images
     * @param array $options
     * @return string
     */
    static function images($images, $options = array())
    {
        $_images = array();
        foreach ($images as $image) {
            $_images[md5(serialize($image))] = $image;
        }
        $output = array();
        foreach ($_images as $image) {
            if (is_scalar($image)) {
                $output[] = self::image($image, $options);
            }
            else {
                $output[] = self::image($image->name, $options);
            }
        }
        return implode(' ', $output);
    }

    /**
     * Return an image from a image file
     *
     * @param $image
     * @param array $options
     * @return bool|string
     */
    static function image($image, $options = array())
    {
        if (!$image) {
            return $image;
        }
        if (!is_scalar($image)) {
            //debug($image);
            return '';
        }
        if (substr($image, 0, 14) == '[inventorycube') {
            $output = self::item_image(array('image' => $image));
            if ($output)
                return $output;
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
        if ($image == '[inventorycube') {
            return '';
        }
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

    /**
     * Return an image from the inventorycube or fallback to use tile image
     *
     * @param $item
     * @param array $options
     * @return bool|string
     */
    static function item_image($item, $options = array())
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
                    $file = false;
            }
        }
        if (!$file && !empty($item['image'])) {
            $file = 'textures/' . $item['image'];
            if (!file_exists($file))
                $file = false;
        }
        if (!$file && !empty($item['data'])) {
            $data = json_decode($item['data']);
            if (!empty($data->options->tiles)) {
                $file = 'textures/' . $data->options->tiles[0];
                if (!file_exists($file))
                    $file = false;
            }
            if (!empty($data->options->tile_images)) {
                $file = 'textures/' . $data->options->tile_images[0];
                if (!file_exists($file))
                    $file = false;
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
        return false;
    }

    /**
     * Return the HTML for an Item
     *
     * @param $name
     * @param null $quantity
     * @param bool $small
     * @return string
     */
    static function item($name, $quantity = null, $small = false)
    {
        global $db;
        $output = '';
        $name = SQLite3::escapeString(self::item_name($name));
        if (!$name || in_array($name, array('air', 'default:air'))) {
            return $name;
        }

        // check for an alias
        $alias = $db->querySingle('SELECT itemname FROM alias WHERE name="' . $name . '"');
        if ($alias) {
            $name = $alias;
        }

        // load the item
        $q = $db->query('SELECT id, data, type, name, image, description FROM item WHERE name="' . $name . '"');
        if ($item = $q->fetchArray()) {
            $attr = 'class="item"';
            if ($small) {
                $attr = 'rel="tooltip" title="' . $item['description'] . ' [' . $item['type'] . '][' . $item['name'] . ']' . ($quantity ? ' x' . $quantity : '') . '"';
            }
            $output .= '<a href="item.php?name=' . $item['name'] . '" ' . $attr . '>';
            $output .= '<span class="image">' . self::item_image($item) . '</span>';
            if (!$small) {
                $output .= '<span class="description">' . $item['description'] . '&nbsp;</span>';
                $output .= '<span class="name">[' . $item['type'] . '][' . $item['name'] . ']' . ($quantity ? ' x' . $quantity : '') . '</span>';
            }
            $output .= '</a>';
        }

        // no item found
        elseif (substr($name, 0, 6) == 'group:') {
            $group = substr($name, 6);
            $name = str_replace(',', ' ', $group);
            if ($small) {
                $output .= '<p style="font-size:75%;text-align:center;line-height:1.1em;margin:0;padding:0;"><a href="items.php?group=' . $group . '" rel="tooltip" title="' . $name . '">view item group</a></p>';
            }
            else {
                $output .= '<p><a href="items.php?group=' . $group . '">' . $name . '</a></p>';
            }
        }

        // no item found
        else {
            $output .= $name . ' (missing item)';
        }

        return $output;
    }


    /**
     * Returns a unique list of items when given an item list such as a recipe input
     *
     * @param $items
     * @return array
     */
    static function item_names($items)
    {
        $_items = array();
        if (is_array($items)) {
            foreach ($items as $name) {
                if (is_array($name)) {
                    $_items = array_merge($_items, self::item_names($name));
                }
                else {
                    $_items[] = $name;
                }
            }
        }
        else {
            $_items[] = self::item_name($items);
        }
        foreach ($_items as $k => $v) if (!$v) unset($_items[$k]);
        return array_unique($_items);
    }

    /**
     * Returns an item name with annoying words and characters removed
     *
     * @param $name
     * @return mixed
     */
    static function item_name($name)
    {
        $name = str_ireplace(array('tool ', 'node ', 'craft ', 'toolitem ', 'nodeitem ', 'craftitem ', '"'), '', $name);
        $name = explode(' ', $name);
        $name = $name[0];
        return $name;
    }

    /**
     * Returns an item quantity
     *
     * @param $name
     * @return mixed
     */
    static function item_quantity($name)
    {
        $name = str_ireplace(array('tool ', 'node ', 'craft ', 'toolitem ', 'nodeitem ', 'craftitem ', '"'), '', $name);
        $name = explode(' ', $name);
        return isset($name[1]) ? $name[1] : 1;
    }

    /**
     * Returns a HTML table containing the recipe for a craft
     *
     * @param $recipe
     * @param $type
     * @param bool $small
     * @return string
     */
    static function craft_recipe($recipe, $type, $small = false)
    {
        $class = $small ? 'crafting-small' : 'crafting';
        $output = '<table class="' . $class . '">';
        if (in_array($type, array('fuel', 'cooking'))) {
            $output .= '<tr>';
            $output .= '<td>' . ($type == 'cooking' ? self::item($recipe, null, $small) : '') . '</td>';
            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td>' . ($type == 'fuel' ? self::item('default:furnace', null, $small) : self::item('default:furnace_active', null, $small)) . '</td>';
            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td>' . ($type == 'fuel' ? self::item($recipe, null, $small) : '') . '</td>';
            $output .= '</tr>';
        }
        else {
            if (is_array($recipe)) {
                if (is_array($recipe[0])) {
                    $output .= '<tr>';
                    $output .= '<td>' . (isset($recipe[0][0]) ? self::item($recipe[0][0], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[0][1]) ? self::item($recipe[0][1], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[0][2]) ? self::item($recipe[0][2], null, $small) : '') . '</td>';
                    $output .= '</tr>';
                    $output .= '<tr>';
                    $output .= '<td>' . (isset($recipe[1][0]) ? self::item($recipe[1][0], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[1][1]) ? self::item($recipe[1][1], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[1][2]) ? self::item($recipe[1][2], null, $small) : '') . '</td>';
                    $output .= '</tr>';
                    $output .= '<tr>';
                    $output .= '<td>' . (isset($recipe[2][0]) ? self::item($recipe[2][0], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[2][1]) ? self::item($recipe[2][1], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[2][2]) ? self::item($recipe[2][2], null, $small) : '') . '</td>';
                    $output .= '</tr>';
                }
                else {
                    $output .= '<tr>';
                    $output .= '<td>' . (isset($recipe[0]) ? self::item($recipe[0], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[1]) ? self::item($recipe[1], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[2]) ? self::item($recipe[2], null, $small) : '') . '</td>';
                    $output .= '</tr>';
                    $output .= '<tr>';
                    $output .= '<td>' . (isset($recipe[3]) ? self::item($recipe[3], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[4]) ? self::item($recipe[4], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[5]) ? self::item($recipe[5], null, $small) : '') . '</td>';
                    $output .= '</tr>';
                    $output .= '<tr>';
                    $output .= '<td>' . (isset($recipe[6]) ? self::item($recipe[6], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[7]) ? self::item($recipe[7], null, $small) : '') . '</td>';
                    $output .= '<td>' . (isset($recipe[8]) ? self::item($recipe[8], null, $small) : '') . '</td>';
                    $output .= '</tr>';
                }
            }
            else {
                $recipe = explode(' ', $recipe);
                $output .= '<tr>';
                $output .= '<td>' . (isset($recipe[0]) ? self::item($recipe[0], null, $small) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[1]) ? self::item($recipe[1], null, $small) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[2]) ? self::item($recipe[2], null, $small) : '') . '</td>';
                $output .= '</tr>';
                $output .= '<tr>';
                $output .= '<td>' . (isset($recipe[3]) ? self::item($recipe[3], null, $small) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[4]) ? self::item($recipe[4], null, $small) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[5]) ? self::item($recipe[5], null, $small) : '') . '</td>';
                $output .= '</tr>';
                $output .= '<tr>';
                $output .= '<td>' . (isset($recipe[6]) ? self::item($recipe[6], null, $small) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[7]) ? self::item($recipe[7], null, $small) : '') . '</td>';
                $output .= '<td>' . (isset($recipe[8]) ? self::item($recipe[8], null, $small) : '') . '</td>';
                $output .= '</tr>';
            }
        }
        $output .= '</table>';
        return $output;
    }

    /**
     * Returns a text table containing the recipe for a craft
     *
     * @param $output
     * @param $recipe
     * @param $type
     * @return string
     */
    static function craft_recipe_paste($output, $recipe, $type)
    {
        $output = explode(' ', $output);
        $quantity = isset($output[1]) ? $output[1] : '1';
        $quantity = ($quantity <= 1) ? '' : ' x' . $quantity;
        $output = $output[0] . $quantity;
        $return = '';
        self::$paste_items = array('empty');
        if (in_array($type, array('fuel', 'cooking'))) {
            $return .= ($type == 'cooking' ? $recipe . ' -> cooking -> ' . $output : '');
            $return .= ($type == 'fuel' ? $recipe . ' -> fuel' : '');
        }
        else {
            if (is_array($recipe)) {
                if (is_array($recipe[0])) {
                    $return .= (!empty($recipe[0][0]) ? self::item_paste($recipe[0][0]) : '-');
                    $return .= (!empty($recipe[0][1]) ? self::item_paste($recipe[0][1]) : '-');
                    $return .= (!empty($recipe[0][2]) ? self::item_paste($recipe[0][2]) : '-');
                    $return .= "\n";
                    $return .= (!empty($recipe[1][0]) ? self::item_paste($recipe[1][0]) : '-');
                    $return .= (!empty($recipe[1][1]) ? self::item_paste($recipe[1][1]) : '-');
                    $return .= (!empty($recipe[1][2]) ? self::item_paste($recipe[1][2]) : '-');
                    $return .= "\n";
                    $return .= (!empty($recipe[2][0]) ? self::item_paste($recipe[2][0]) : '-');
                    $return .= (!empty($recipe[2][1]) ? self::item_paste($recipe[2][1]) : '-');
                    $return .= (!empty($recipe[2][2]) ? self::item_paste($recipe[2][2]) : '-');
                    $return .= "\n";
                }
                else {
                    $return .= (!empty($recipe[0]) ? self::item_paste($recipe[0]) : '-');
                    $return .= (!empty($recipe[1]) ? self::item_paste($recipe[1]) : '-');
                    $return .= (!empty($recipe[2]) ? self::item_paste($recipe[2]) : '-');
                    $return .= "\n";
                    $return .= (!empty($recipe[3]) ? self::item_paste($recipe[3]) : '-');
                    $return .= (!empty($recipe[4]) ? self::item_paste($recipe[4]) : '-');
                    $return .= (!empty($recipe[5]) ? self::item_paste($recipe[5]) : '-');
                    $return .= "\n";
                    $return .= (!empty($recipe[6]) ? self::item_paste($recipe[6]) : '-');
                    $return .= (!empty($recipe[7]) ? self::item_paste($recipe[7]) : '-');
                    $return .= (!empty($recipe[8]) ? self::item_paste($recipe[8]) : '-');
                    $return .= "\n";
                }
            }
            else {
                $recipe = explode(' ', $recipe);
                $return .= (!empty($recipe[0]) ? self::item_paste($recipe[0]) : '-');
                $return .= (!empty($recipe[1]) ? self::item_paste($recipe[1]) : '-');
                $return .= (!empty($recipe[2]) ? self::item_paste($recipe[2]) : '-');
                $return .= "\n";
                $return .= (!empty($recipe[3]) ? self::item_paste($recipe[3]) : '-');
                $return .= (!empty($recipe[4]) ? self::item_paste($recipe[4]) : '-');
                $return .= (!empty($recipe[5]) ? self::item_paste($recipe[5]) : '-');
                $return .= "\n";
                $return .= (!empty($recipe[6]) ? self::item_paste($recipe[6]) : '-');
                $return .= (!empty($recipe[7]) ? self::item_paste($recipe[7]) : '-');
                $return .= (!empty($recipe[8]) ? self::item_paste($recipe[8]) : '-');
                $return .= "\n";
            }
            $return = explode("\n", $return);
            $return[0] .= '   -> ' . $output;
            unset(self::$paste_items[0]);
            foreach (self::$paste_items as $k => $paste_item) {
                if (!isset($return[$k + 1])) {
                    $return[$k] = '   ';
                }
                $return[$k] .= '   ' . $k . ' = ' . $paste_item;
            }
            $return = implode("\n", $return);
        }
        return trim($return);
    }

    static function item_paste($item)
    {
        if (!in_array($item, self::$paste_items)) {
            self::$paste_items[] = $item;
        }
        return array_search($item, self::$paste_items);
    }

    /**
     * @return array
     */
    static function get_mods()
    {
        global $db;
        $mods = array();
        $q = $db->query('SELECT mod FROM "item" WHERE mod!="unknown" AND mod!="__builtin" AND mod!="" GROUP BY mod ORDER BY mod');
        while ($row = $q->fetchArray()) {
            $mods[] = $row['mod'];
        }
        return $mods;
    }

}
