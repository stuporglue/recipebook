<!DOCTYPE HTML>
<html>
    <head>
        <title>As long as you make this food...Everything is going to be OK</title>
        <link type='text/css' href='style.css' rel='stylesheet'/>
    </head>
    <body>
        <h1>As long as you make this food...Everything is going to be OK</h1>
        <p>Quite possibly the only cookbook you'll ever need!</p>
        <p>By Caroline</p>
        <p>Keep calm and follow instructions. It's that easy! Now let's do this!</p>
        <div class='tips'>
            <h2>A few tips</h2>
            <p>
            <dl>
                <dt>To boil eggs</dt><dd>Place eggs in water in pan. Bring to a boil, then turn off heat and cover with lid. Leave covered 15 to 20 minutes. All done!</dd>
                <dt>To cut a kiwi</dt><dd>Cut the ends off. Slip a spoon right under the skin, then rotate the spoon around the kiwi, separating the fruit from the skin all the way around. It will change your life!</dd>
                <dt>Measuring</dt><dd>There are 4 tablespoons in 1/4 cup. There are 3 teaspoons in a tablespoon. So if any recipes call for 4 tablespoons or 3 teaspoons of something, save yourself some work!</dd>
                <dt>Shreading Meat</dt><dd>If you have a stand mixer, use it to shread cooked meat with the dough hook. It saves like an hour</dd>
                <dt>Real Ingredients</dt><dd>Pretty much everything tastes better when you use real butter, real ginger, real garlic, real vanilla or real herbs. But if you're feeding people under the age of about 15, they won't care! So save your $ for the big guns.</dd>
                <dt>Ginger</dt><dd>Peel ginger with a spoon, not a knife. Just scrape the spoon against the skin</dd>
                <dt>Avocados</dt><dd>Slice avocadods inside the skin! Just cut it in half, remove the pit by whacking it with the heel of a knife and twisting it out. Then use a paring knife to slice each half while they are still in the skin. Scoop slices out with a spoon!</dd>
                <dt>Eggshells</dt><dd>If you drop little pieces of eggshell in a dish you are making, use the large portion of the eggshell to scoop out the little bits in your food. They're like magnets!</dd>
            </dl>
            </p>
        </div>
        <?php
        require_once('lib/db.inc');

        $res = getAll();
        $cat = NULL;
        while($row = pg_fetch_assoc($res)){
        if($row['category'] !== $cat){
        $cat = $row['category'];
        print "<h2>" . ucfirst($cat) . "</h2>";
        }
        print "<a href='recipe/{$row['id']}/" . urlencode($row['name']) . "'>{$row['name']}</a><br>";
        }
        ?>
    </body>
</html>
