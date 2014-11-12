<?php
require_once('lib/db.inc');

global $quickicon,$favoriteicon;
$quickicon = " <span class='glyphicon glyphicon-time' title='Ready in 30 minutes or less!'></span>";
$favoriteicon = " <span class='glyphicon glyphicon-heart' title='A Caroline favorite!'></span>";

function printHeader($title,$activeCat,$cssRelative = 0){
    global $quickicon,$favoriteicon;

$header = "<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name='description' content=''>
    <meta name='author' content=''>
    <link rel='icon' href='". str_repeat('../img/',$cssRelative) . "favicon.png'>

    <title>Eat Moore!";

    if($title !== 'Eat Moore!'){
        $header .= " | $title";
    }
    
    $header .= "</title>

        <!-- Latest compiled and minified CSS -->
        <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css'>
        <!-- Optional theme -->
        <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap-theme.min.css'>


    <!-- Custom styles for this template -->
    <link href='" . str_repeat('../',$cssRelative) . "theme.css' rel='stylesheet'>

        <link type='text/css' href='" . str_repeat('../',$cssRelative) . "style.css' rel='stylesheet'/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src='https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js'></script>
      <script src='https://oss.maxcdn.com/respond/1.4.2/respond.min.js'></script>
    <![endif]-->
  </head>

  <body role='document' class='$activeCat'>

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
          <a class='navbar-brand' href='".str_repeat('../',$cssRelative)."'>Eat Moore!</a>
            <form class='smallsearch hidden-lg hidden-md hidden-sm navbar-form' role='search'>
                <div class='form-group has-feedback'>
                    <input id='searchbox' type='text' placeholder='Search' class='form-control'>
                    <span id='searchicon' class='glyphicon glyphicon-search form-control-feedback'></span>
                </div>
            </form>
        </div>
        <div id='navbar' class='navbar-collapse collapse'>
          <ul class='nav navbar-nav'>";
            $header .= "<li class='".($activeCat == 'home' ? 'active' : '')."'><a href='". str_repeat('../',$cssRelative)."'>Home</a></li>";

            $catsMenu = "<ul class='dropdown-menu' role='menu'>";
            $catsClasses = Array();
            foreach(getCategories() as $cat){
                $catsMenu .= "<li class='".($activeCat == $cat['name'] ? 'active' : '')." {$cat['name']}'><a href='".str_repeat('../',$cssRelative)."category/{$cat['name']}'>" . htmlentities($cat['label']) . "</a></li>";
                $catsClasses[] = $cat['name'];
            }
            $catsMenu .= "</ul>";

            $header .= "<li class='dropdown ". (in_array($activeCat,$catsClasses) ? 'active' : '') . "'>";
            $header .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown'>Recipes<span class='caret'></span></a>" . $catsMenu . "</li>";

            // $header .= "<li class='".($activeCat == 'meal' ? 'active' : '')."'><a href='".str_repeat('../',$cssRelative)."meal/'>Meal?</a></li>";

            $header .= "<li class='dropdown ".(in_array($activeCat,Array('meal','quick','tips','ingredients')) ? 'active' : '')."'>
              <a href='#' class='dropdown-toggle' data-toggle='dropdown'>Tools<span class='caret'></span></a>
              <ul class='dropdown-menu' role='menu'>
                <li class='".($activeCat == 'meal' ? 'active' : '')."'><a href='".str_repeat('../',$cssRelative)."meal/'>Random Meal</a></li>
                <li class='".($activeCat == 'quick' ? 'active' : '')."'><a class='quick' href='".str_repeat('../',$cssRelative)."quick/'>Quick Dishes $quickicon</a></li>
                <li class='".($activeCat == 'favorite' ? 'active' : '')."'><a class='favorite' href='".str_repeat('../',$cssRelative)."favorites/'>Favorite Dishes $favoriteicon</a></li>
                <li class='".($activeCat == 'tips' ? 'active' : '')."'><a href='".str_repeat('../',$cssRelative)."tips/'>Secret Tips</a></li>
                <li class='".($activeCat == 'ingredients' ? 'active' : '')."'><a href='".str_repeat('../',$cssRelative)."ingredients/'>Ingredients</a></li>
              </ul>
            </li>
          </ul>
            <form class='hidden-xs navbar-form navbar-right' role='search'>
                <div class='form-group has-feedback'>
                    <input id='searchbox' type='text' placeholder='Search' class='form-control'>
                    <span id='searchicon' class='glyphicon glyphicon-search form-control-feedback'></span>
                </div>
            </form>
        </div><!--/.nav-collapse -->
      </div>
      </nav>

    <div class='container theme-showcase' role='main'>
      ";

    print $header;
}

function printFooter(){
    $footer = "

    </div> <!-- /container -->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>
    <!-- script src='../../assets/js/docs.min.js'></script -->
    <!-- Latest compiled and minified JavaScript -->
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js'></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!-- script src='../../assets/js/ie10-viewport-bug-workaround.js'></script-- >
  </body>
  </html>";
    print $footer;
}
