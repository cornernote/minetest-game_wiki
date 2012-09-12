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

    table.crafting-small td {
        border: 1px solid #ccc;
        margin: 1px;
        padding: 1px;
        width: 32px;
        height: 32px;
    }

    #footer {
        clear: both;
        height: 50px;
    }

</style>
<script type="text/javascript" src="bootstrap/js/jquery.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
<script type="text/javascript">
    $(function () {
        $('a[rel=tooltip]').tooltip();
    });
</script>

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->