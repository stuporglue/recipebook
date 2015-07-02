<?php

require_once('../lib/template.php');

session_start();

if(isset($_POST['username']) && isset($_POST['password'])){
    if($config['admin'] == $_POST['username'] && $config['adminpw'] == $_POST['password']){
        $_SESSION['loggedin'] = TRUE;
    }
}

if($_SESSION['loggedin'] === TRUE){
    header("Location:home.php");
    exit();
}

printHeader("Chef Login Area");
?>
<div class="jubotron">
<h1>Chef Area</h1>
Welcome to the chef area
<form method="post">
    <label for="username">Chef Name</label><input name="username"/>
    <label for="password">Chef Password</label><input type="password" name="password"/>
    <input type='submit' value="Log In"/>
</form>
</div>
<?php
printFooter();
