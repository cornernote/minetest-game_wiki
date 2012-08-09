<?php
$resume = empty($_GET['resume']) ? false : true;
if(!count(glob('data/wikidata/*.json'))) $resume = true;

// remove existing database
if (!$resume) {
    if (file_exists('data/wiki.db')) {
        if (file_exists('data/wiki.bak.db')) {
            unlink('data/wiki.bak.db');
        }
        rename('data/wiki.db', 'data/wiki.bak.db');
    }
}
// set to 24 hours (really long)
set_time_limit(60 * 60 * 24);
// include globals
require('globals.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MTGW</title>
    <?php echo head_tags(); ?>
</head>

<body>
<?php echo menu(); ?>
<div class="container">
    <h1>Import JSON Data</h1>

    <p>If the import stops, <a href="import.php?resume=1" class="btn">click here</a> to resume.

    <p>
        <?php
        // create database
        if (!$resume) {

            $GLOBALS['db']->query('CREATE TABLE "abm" ("id" INTEGER PRIMARY KEY, "mod" TEXT, "data" TEXT)');
            $GLOBALS['db']->query('CREATE INDEX "main"."abm.mod" ON "abm" ("mod")');

            $GLOBALS['db']->query('CREATE TABLE "abm_to_itemname" ("abm_id" INTEGER, "name" TEXT)');
            $GLOBALS['db']->query('CREATE INDEX "main"."abm_to_itemname.name" ON "abm_to_itemname" ("name")');

            $GLOBALS['db']->query('CREATE TABLE "alias" ("id" INTEGER PRIMARY KEY, "mod" TEXT, "data" TEXT, "name" TEXT, "itemname" TEXT)');
            $GLOBALS['db']->query('CREATE INDEX "main"."alias.mod" ON "alias" ("mod")');
            $GLOBALS['db']->query('CREATE INDEX "main"."alias.name" ON "alias" ("name")');
            $GLOBALS['db']->query('CREATE INDEX "main"."alias.itemname" ON "alias" ("itemname")');

            $GLOBALS['db']->query('CREATE TABLE "craft" ("id" INTEGER PRIMARY KEY, "mod" TEXT, "data" TEXT, "type" TEXT, "output" TEXT, "quantity" INTEGER)');
            $GLOBALS['db']->query('CREATE INDEX "main"."craft.mod" ON "craft" ("mod")');
            $GLOBALS['db']->query('CREATE INDEX "main"."craft.type" ON "craft" ("type")');
            $GLOBALS['db']->query('CREATE INDEX "main"."craft.output" ON "craft" ("output")');

            $GLOBALS['db']->query('CREATE TABLE "craft_to_itemname" ("craft_id" INTEGER, "name" TEXT)');
            $GLOBALS['db']->query('CREATE INDEX "main"."craft_to_itemname.name" ON "craft_to_itemname" ("name")');

            $GLOBALS['db']->query('CREATE TABLE "entity" ("id" INTEGER PRIMARY KEY, "mod" TEXT, "data" TEXT)');
            $GLOBALS['db']->query('CREATE INDEX "main"."entity.mod" ON "entity" ("mod")');

            $GLOBALS['db']->query('CREATE TABLE "item" ("id" INTEGER PRIMARY KEY, "mod" TEXT, "data" TEXT, "type" TEXT, "name" TEXT, "description" TEXT, "image" TEXT)');
            $GLOBALS['db']->query('CREATE INDEX "main"."item.mod" ON "item" ("mod")');
            $GLOBALS['db']->query('CREATE INDEX "main"."item.type" ON "item" ("type")');
            $GLOBALS['db']->query('CREATE INDEX "main"."item.name" ON "item" ("name")');

            $GLOBALS['db']->query('CREATE TABLE "group_to_itemname" ("group" TEXT, "value" TEXT, "name" TEXT)');
            $GLOBALS['db']->query('CREATE INDEX "main"."group_to_itemname.name" ON "group_to_itemname" ("name")');

        }

        // register_abm
        echo '<h2>ABM</h2>';
        foreach (glob('data/wikidata/register_abm.*.*.json') as $filename) {
            echo 'importing: <b>' . $filename . '</b><br/>';
            $pathinfo = pathinfo($filename);
            list($table, $mod, $id) = explode('.', $pathinfo['filename']);
            $contents = file_get_contents($filename);
            $json = json_decode($contents);
            $data = SQLite3::escapeString($contents);
            if ($GLOBALS['db']->query("INSERT INTO abm (id, mod, data) VALUES ('" . $id . "','" . $mod . "','" . $data . "')")) {
                $abm_id = $GLOBALS['db']->lastInsertRowID();
                if (isset($json->options->nodenames)) foreach (item_names($json->options->nodenames) as $name)
                    $GLOBALS['db']->query("INSERT INTO abm_to_itemname (abm_id, name) VALUES ('" . $abm_id . "','" . $name . "')");
                if (isset($json->options->neighbors)) foreach (item_names($json->options->neighbors) as $name)
                    $GLOBALS['db']->query("INSERT INTO abm_to_itemname (abm_id, name) VALUES ('" . $abm_id . "','" . $name . "')");
                unlink($filename);
            }
        }

        // register_alias
        echo '<h2>Alias</h2>';
        foreach (glob('data/wikidata/register_alias.*.*.json') as $filename) {
            echo 'importing: <b>' . $filename . '</b><br/>';
            $pathinfo = pathinfo($filename);
            list($table, $mod, $id) = explode('.', $pathinfo['filename']);
            $contents = file_get_contents($filename);
            $json = json_decode($contents);
            $data = SQLite3::escapeString($contents);
            $name = SQLite3::escapeString($json->name);
            $itemname = SQLite3::escapeString($json->options);
            if ($GLOBALS['db']->query("INSERT INTO alias (id, mod, data, name, itemname) VALUES ('" . $id . "','" . $mod . "','" . $data . "','" . $name . "','" . $itemname . "')")) {
                unlink($filename);
            }
        }

        // register_craft
        echo '<h2>Craft</h2>';
        foreach (glob('data/wikidata/register_craft.*.*.json') as $filename) {
            echo 'importing: <b>' . $filename . '</b><br/>';
            $pathinfo = pathinfo($filename);
            list($table, $mod, $id) = explode('.', $pathinfo['filename']);
            $contents = file_get_contents($filename);
            $json = json_decode($contents);
            $data = SQLite3::escapeString($contents);
            $type = isset($json->options->type) ? $json->options->type : 'crafting';
            $_output = isset($json->options->output) ? item_name($json->options->output) : '';
            $_output = explode(' ', $_output);
            $output = SQLite3::escapeString($_output[0]);
            $quantity = SQLite3::escapeString(isset($_output[1]) ? $_output[1] : 1);
            if ($GLOBALS['db']->query("INSERT INTO craft (id, mod, data, type, output, quantity) VALUES ('" . $id . "','" . $mod . "','" . $data . "','" . $type . "','" . $output . "','" . $quantity . "')")) {
                $craft_id = $GLOBALS['db']->lastInsertRowID();
                if (isset($json->options) && isset($json->options->recipe)) foreach (item_names($json->options->recipe) as $name)
                    $GLOBALS['db']->query("INSERT INTO craft_to_itemname (craft_id, name) VALUES ('" . $craft_id . "','" . $name . "')");
                unlink($filename);
            }
        }

        // register_entity
        echo '<h2>Entity</h2>';
        foreach (glob('data/wikidata/register_entity.*.*.json') as $filename) {
            echo 'importing: <b>' . $filename . '</b><br/>';
            $pathinfo = pathinfo($filename);
            list($table, $mod, $id) = explode('.', $pathinfo['filename']);
            $data = SQLite3::escapeString(file_get_contents($filename));
            if ($GLOBALS['db']->query("INSERT INTO entity (id, mod, data) VALUES ('" . $id . "','" . $mod . "','" . $data . "')")) {
                unlink($filename);
            }
        }

        // register_item
        echo '<h2>Item</h2>';
        foreach (glob('data/wikidata/register_item.*.*.json') as $filename) {
            echo 'importing: <b>' . $filename . '</b><br/>';
            $pathinfo = pathinfo($filename);
            list($table, $mod, $id) = explode('.', $pathinfo['filename']);
            $contents = file_get_contents($filename);
            $data = SQLite3::escapeString($contents);
            $json = json_decode($contents);
            $type = SQLite3::escapeString(isset($json->options) && !empty($json->options->type) ? $json->options->type : 'unknown');
            $name = SQLite3::escapeString(item_name($json->name));
            $name = explode(' ', $name);
            $name = isset($name[1]) ? $name[1] : $name[0];
            $description = SQLite3::escapeString((isset($json->options->description) ? $json->options->description : ''));
            $images = array_merge(
                isset($json->options->inventory_image) ? is_array($json->options->inventory_image) ? $json->options->inventory_image : array($json->options->inventory_image) : array(),
                isset($json->options->tiles) ? (array)$json->options->tiles : array(),
                isset($json->options->tile_images) ? (array)$json->options->tile_images : array()
            );
            if (isset($json->options->inventory_image)) {
                if (is_array($json->options->inventory_image)) {
                    $image = $json->options->inventory_image[0];
                }
                else {
                    $image = $json->options->inventory_image;
                }
            }
            else {
                if (isset($json->options->tiles) && isset($json->options->tiles[5])) {
                    $image = SQLite3::escapeString($json->options->tiles[5]);
                }
                elseif (isset($json->options->tile_images) && isset($json->options->tile_images[5])) {
                    $image = SQLite3::escapeString($json->options->tile_images[5]);
                }
                else {
                    $image = SQLite3::escapeString(isset($images[0]) ? $images[0] : '');
                }
            }
            if ($GLOBALS['db']->query("INSERT INTO item (id, mod, data, type, name, description, image) VALUES ('" . $id . "','" . $mod . "','" . $data . "','" . $type . "','" . $name . "','" . $description . "','" . $image . "')")) {
                if (isset($json->options) && isset($json->options->groups)) foreach ($json->options->groups as $group => $value)
                    $GLOBALS['db']->query("INSERT INTO group_to_itemname ('group', value, name) VALUES ('" . $group . "','" . $value . "','" . $name . "')");
                //echo 'fetching images: <b>' . print_r($images, true) . '</b><br/>';
                //images(array_merge(array($image), $images), array('download' => true));
                unlink($filename);
            }
        }

        echo '<h2>Done!</h2>';

        ?>


</div>
</body>
</html>