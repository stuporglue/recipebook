<?php

session_start();
if($_SESSION['loggedin'] !== TRUE){
    header("Location:index.php");
    exit();
}

require_once('../lib/template.php');

printHeader("Chef Area");
?>
<div class="jumbotron">
<h1>Chef Area</h1>
Welcome to the chef area
</div>
<div>
<h2>Manage Lists</h2>
<ul>
<li><a href='categories.php'>Categories</a></li>
<li><a href='ingredients.php'>Ingredients</a></li>
<li><a href='units.php'>Units</a></li>
</ul>
</div>
<?php
printFooter();
