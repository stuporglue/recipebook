<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function handler($errno,$errstr,$errfile,$errline,$errcontext){
    error_log($errstr . ' -- ' . $errfile . ":" . $errline);
}

set_error_handler('handler');

require_once(__DIR__ . '/db.inc');

global $quickicon,$favoriteicon;
$quickicon = " <span class='glyphicon glyphicon-time' title='Ready in 30 minutes or less!'></span>";
$favoriteicon = " <span class='glyphicon glyphicon-heart' title='A Caroline favorite!'></span>";

function printHeader($title="EatMoore",$activeCat=NULL){
    global $quickicon,$favoriteicon;

    $print_admin_css = (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE);
    $admin_area = strpos($_SERVER['REQUEST_URI'],'/chef/') === 0;

    $relpath = '//' . $_SERVER['HTTP_HOST'] . '/';

$header = "<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name='description' content=''>
    <meta name='author' content=''>
    <link rel='icon' href='{$relpath}img/favicon.png'>

    <title>Eat Moore!";

    if($title !== 'Eat Moore!'){
        $header .= " | $title";
    }
    
    $header .= "</title>

        <!-- Latest compiled and minified CSS -->
        <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css' media='all'>
        <!-- Optional theme -->
        <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap-theme.min.css' media='screen'>


    <!-- Custom styles for this template -->
    <link href='{$relpath}css/theme.css' rel='stylesheet' media='screen'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
    ";

    if($print_admin_css){
        $header .= "<link type='text/css' href='{$relpath}chef/bootgrid/jquery.bootgrid.min.css' rel='stylesheet' media='screen'/>";
        $header .= "<link type='text/css' href='{$relpath}css/admin.css' rel='stylesheet' media='screen'/>";
    }

    $header .= "
    <link type='text/css' href='{$relpath}css/style.css' rel='stylesheet' media='screen'/>
    <link type='text/css' href='{$relpath}css/print.css' rel='stylesheet' media='print'/>
    <link type='text/css' href='{$relpath}css/tt.css' rel='stylesheet' media='screen'/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src='https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js'></script>
      <script src='https://oss.maxcdn.com/respond/1.4.2/respond.min.js'></script>
    <![endif]-->
  </head>

  <body role='document' class='$activeCat" . ($admin_area ? ' admin' : '') . "'>

    <!-- Fixed navbar -->
    <nav class='navbar navbar-inverse navbar-fixed-top' role='navigation'>
      <div class='container'>
        <div class='navbar-header'>
          <button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#navbar' aria-expanded='false' aria-controls='navbar'>
            <span class='sr-only'>Toggle navigation</span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
          </button>
          <a class='navbar-brand' href='{$relpath}'>Eat Moore!</a>
        </div>
        <div id='navbar' class='navbar-collapse collapse'>
          <ul class='nav navbar-nav'>";
            $header .= "<li class='".($activeCat == 'home' ? 'active' : '')."'><a href='$relpath'>Home</a></li>";

            $catsMenu = "<ul class='dropdown-menu' role='menu'>";
            $catsClasses = Array();
            foreach(getCategories() as $cat){
                $catsMenu .= "<li class='".($activeCat == $cat['name'] ? 'active' : '')." {$cat['name']}'><a href='{$relpath}category/{$cat['name']}'>" . htmlentities($cat['label']) . "</a></li>";
                $catsClasses[] = $cat['name'];
            }
            $catsMenu .= "</ul>";

            $header .= "<li class='dropdown ". (in_array($activeCat,$catsClasses) ? 'active' : '') . "'>";
            $header .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown'>Recipes<span class='caret'></span></a>" . $catsMenu . "</li>";

            $header .= "<li class='dropdown ".(in_array($activeCat,Array('meal','quick','tips','ingredients')) ? 'active' : '')."'>
              <a href='#' class='dropdown-toggle' data-toggle='dropdown'>Tools<span class='caret'></span></a>
              <ul class='dropdown-menu' role='menu'>
                <li class='".($activeCat == 'meal' ? 'active' : '')."'><a href='{$relpath}meal'>Random Meal</a></li>
                <li class='".($activeCat == 'quick' ? 'active' : '')."'><a href='{$relpath}quick'>Quick Dishes $quickicon</a></li>
                <li class='".($activeCat == 'favorite' ? 'active' : '')."'><a class='favorite' href='{$relpath}favorites'>Favorite Dishes $favoriteicon</a></li>
                <li class='".($activeCat == 'tips' ? 'active' : '')."'><a href='{$relpath}tips'>Secret Tips</a></li>
                <li class='".($activeCat == 'ingredients' ? 'active' : '')."'><a href='{$relpath}ingredients'>Ingredients</a></li>
              </ul>
              </li>";

            if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === TRUE){
                $header .= "<li class='dropdown'>
                    <a href='#' class='dropdown-toggle' data-toggle='dropdown'>Chef's Area<span class='caret'></span></a>
                    <ul class='dropdown-menu' role='menu'>
                        <li><a href='{$relpath}chef/categories.php'>Categories</a></li>
                        <li><a href='{$relpath}chef/ingredients.php'>Ingredients</a></li>
                        <li><a href='{$relpath}chef/recipes.php'>Recipes</a></li>
                        <li><a href='{$relpath}chef/units.php'>Units</a></li>
                    </ul>
                    </li>";
            }

            $header .= "</ul>
            <form class='sitesearch hidden-xs navbar-form navbar-right' role='search' method='post' action='{$relpath}search'>
                <div class='form-group has-feedback'>
                    <input type='text' placeholder='Search' class='typeahead form-control searchbox' name='searchval'>
                    <span class='glyphicon glyphicon-search form-control-feedback searchicon'></span>
                    <input style='position:absolute;top:-1000px;left:-1000px;' name='dosearch' type='submit'/>
                </div>
            </form>
        </div><!--/.nav-collapse -->
      </div>
      </nav>

    <div class='container theme-showcase' role='main'>
      ";

    // $header .=  "<span class='print'>" . ($_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] .  $_SERVER['REQUEST_URI'] . "</span>";
    print $header;
}

function printFooter(){
    $relpath = '//' . $_SERVER['HTTP_HOST'] . '/';
    $print_admin_css = (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE);
    $admin_area = strpos($_SERVER['REQUEST_URI'],'/chef/') === 0;

    $footer = "
    </div>
    <form class='sitesearch smallsearch hidden-lg hidden-md hidden-sm navbar-form' role='search' method='post' action='{$relpath}search'>
        <div class='form-group has-feedback'>
            <input type='text' placeholder='Search' class='typeahead form-control searchbox' name='searchval'>
            <span class='glyphicon glyphicon-search form-control-feedback searchicon'></span>
            <input style='margin-left:-10000px;' name='dosearch' type='submit'/>
        </div>
    </form>

    <!-- Modal -->
    <div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
    <div class='modal-dialog' role='document'>
        <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
            <h4 class='modal-title' id='myModalLabel'>Modal title</h4>
        </div>
        <div class='modal-body'>
        </div>
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            <button type='button' class='btn btn-primary' id='savebutton'>Save changes</button>
        </div>
        </div>
    </div>
    </div>

    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js'></script>
<script src='{$relpath}js/handlebars-v2.0.0.js'></script>
    <script src='{$relpath}js/typeahead.bundle.js'></script>
    ";

    if($print_admin_css){
        $footer .= "
            <script src='{$relpath}chef/bootgrid/jquery.bootgrid.js'></script>
            <script src='{$relpath}chef/bootgrid/jquery.bootgrid.fa.js'></script>
            <script src='{$relpath}js/admin.js'></script>
            ";
    }

    $footer .= "
    <script>
        var relpath = '$relpath';
    </script>
    <script src='{$relpath}js/js.js'></script>
";

    if(file_exists(__DIR__ . '/../js/ga.js')){
        $footer .= "<script src='{$relpath}js/ga.js'></script>";
    }

    $footer .= "

  </body>
  </html>";
    print $footer;
}

function editLink($type,$id){
    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === TRUE){
        print "<div><a href='#' data-type='$type' data-row-id='$id' onclick='editRecord(this);'>(Edit)</a></div>";
    }
}
