<?php require('globals.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MTGW</title>
    <?php echo head_tags(); ?>
</head>

<body>
<?php echo menu(); ?>
<div class="container home">

    <div class="hero-unit">
        <h1>MineTest GameWiki</h1>

        <p>This site contains a list of all ingame items and crafts.</p>
    </div>

    <div class="well">
        <ul>
            <li>
                <h2><a href="items.php">Items
                    <small>tools, nodes, crafts</small>
                </a></h2>
            </li>
            <li>
                <h2><a href="crafts.php">Crafts
                    <small>learn to craft items</small>
                </a></h2>
            </li>
            <li>
                <h2><a href="abms.php">ABMs
                    <small>active block modifiers</small>
                </a></h2>
            </li>
            <li>
                <h2><a href="aliases.php">Aliases
                    <small>list of available item aliases</small>
                </a></h2>
            </li>
        </ul>
    </div>

</div>
</body>
</html>