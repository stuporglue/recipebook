Recipe Book
==========

A PHP / Postgres / Bootstrap recipe book for my wife's recipes.

Feel free to use this code to jump-start your own recipe website. The code does include some 
hard coded content (index.php, tips.php, meal.php) so you'll want to edit those to suit your
site. 

You can check out the live site here: [Demo / EatMoore.com](EatMoore.com)

Features 
--------

* LibreOffice Base front-end for easily loading in data 
* Support for sub-recipes (eg. for sauces or whatever)
* Uses the popular Bootstrap 3 framework
* Instructions and About sections support Markdown
* Fractions look like fractions
* Search / Autocomplete box
* Random meal generator
* Support for marking recipes as favorite or quick (or both)
* Browse recipes by ingredient or by category

Quirks
------

These aren't bugs, these are design decisions to make things easier

* Ingredient quantities are entered as decimals. Use 0.33 for 1/3 and 0.66 for 2/3
* All ingredients must have a unit. Use count for things like eggs or whole fruits which shouldn't display a unit
* All ingredients must have a quantity. Use 0 for things which shouldn't display a quantity
* Search uses Postgres' ILIKE "%searchTerm%" against a view. It works pretty well for searching for words and phrases but isn't a propper full-text search. 
* Inkscape crashed right after I exported the background image, so there's no easy way to modify or update it. All the images in the background image came from [OpenClipart.org](http://openclipart.org/).

Setup
-----

You will need Postgres, PHP and Apache with .htaccess / mod_rewrite enabled. 

1. Download the code to your web host.
2. Use the included db.sql file to set up your database. 
3. Copy lib/config.php.example to lib/config.php and edit the database connection values

The website should be set up at this point. You should have some categories and a working site
but no recipes or ingredients. 

You can use [LibreOffice Base](http://www.libreoffice.org/) and the included file, RecipeBook.odb, 
to add recipes to your site. LibreOffice Base is a little slow to start up, but once it has loaded
the forms it works pretty well. 

Note: Once a LibreOffice form is opened, dropdowns and linked field options are not re-populated. 
This means that if you forget to add an ingredient before adding a recipe, you'll need to close the 
recipe form, add the ingredient then re-open the recipe form to be able to select it in the list. 

License
-------

Licensed under the [MIT License](LICENSE.txt). Free for commercial or non-commercial use. 
