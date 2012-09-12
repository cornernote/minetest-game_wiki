<?php
$page = basename($_SERVER['SCRIPT_NAME']);
?>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="<?php echo $GLOBALS['brand_url']; ?>"><?php echo $GLOBALS['name']; ?></a>

            <div class="nav-collapse">
                <ul class="nav">
                    <li <?php echo in_array($page, array('index.php')) ? 'class="active"' : '' ?>>
                        <a href="./">Home</a></li>
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
        </div>
    </div>
</div>
