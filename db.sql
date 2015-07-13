CREATE TABLE categories (
    id serial PRIMARY KEY,
    name character varying NOT NULL UNIQUE,
    label character varying 
);

INSERT INTO categories (name,label) VALUES 
('breads', 'Breads'),
('desserts', 'Desserts'),
('maindish', 'Main Dishes'),
('other', 'Other'),
('soupsalad', 'Soup & Salad');


CREATE TABLE ingredients (
    id serial PRIMARY KEY,
    name character varying NOT NULL UNIQUE,
    plural character varying UNIQUE
);

CREATE TABLE units (
    id serial PRIMARY KEY,
    name character varying NOT NULL UNIQUE,
    abbreviation character varying UNIQUE,
    plural character varying UNIQUE
);

INSERT INTO units (name,abbreviation,plural) VALUES 
('bag', 'bag', 'bags'),
('box', 'box', 'boxes'),
('bunch', 'bunch', 'bunch'),
('can', 'can', 'cans'),
('count', 'count', 'count'),
('cup', 'c', 'cups'),
('dash', 'dash', 'dash'),
('drop', 'drop', 'drops'),
('gallon', 'gal', 'gallons'),
('head', 'head', 'heads'),
('inch', 'in', 'inches'),
('milligram', 'mg', 'milligrams'),
('milliliter', 'ml', 'milliliters'),
('ounce', 'oz', 'ounces'),
('package', 'pkg', 'packges'),
('pinch', 'pinch', 'pinches'),
('pint', 'pt', 'pints'),
('pound', 'lb', 'pounds'),
('quart', 'qt', 'quarts'),
('slice', 'slice', 'slices'),
('square', 'square', 'squares'),
('stick', 'stick', 'sticks'),
('tablespoon', 'T', 'tablespoons'),
('teaspoon', 't', 'teaspoons'),
('to taste', 'to taste', 'to taste');

CREATE TABLE recipes (
    id serial PRIMARY KEY,
    name character varying NOT NULL UNIQUE,
    about text,
    instructions text,
    category integer NOT NULL REFERENCES categories(id),
    quick boolean,
    display_name character varying,
    hide boolean,
    date_added date DEFAULT now(),
    favorite boolean
);

CREATE TABLE recipe_recipe (
    id serial PRIMARY KEY,
    parent integer NOT NULL REFERENCES recipes(id),
    child integer NOT NULL REFERENCES recipes(id),
    childname character varying NOT NULL
);

CREATE TABLE recipe_ingredient (
    id serial PRIMARY KEY,
    recipe_id integer REFERENCES recipes(id),
    quantity double precision,
    unit_id integer REFERENCES units(id),
    premodifier character varying,
    ingredient_id integer REFERENCES ingredients(id),
    postmodifier character varying
);


CREATE VIEW pretty_ingredients AS
    (
        SELECT 
            ri.id,
            ri.recipe_id, 
            ri.ingredient_id, 
            ri.unit_id,
            COALESCE(ri.premodifier, '') AS premodifier, 
            COALESCE(i.plural, i.name) AS name, 
            COALESCE(ri.postmodifier, '') AS postmodifier, 
            ri.quantity, 
            CASE 
                WHEN ((u.name)::text='count') THEN ''
                WHEN (ri.quantity=1) THEN u.name 
                ELSE u.plural 
            END AS unit, 
            CASE 
                WHEN ((u.name)::text = 'count') THEN ''
                ELSE u.abbreviation 
            END AS abbreviation, 
            (
                COALESCE(ri.premodifier, '') || 
                ' ' ||
                CASE 
                    WHEN ri.quantity=1 THEN i.name 
                    ELSE COALESCE(i.plural, i.name) 
                END || 
                ' ' || 
                COALESCE(ri.postmodifier, '')
            ) AS ingredient
        FROM 
            recipe_ingredient ri, 
            ingredients i, 
            units u 
        WHERE 
            ri.ingredient_id=i.id AND 
            ri.unit_id = u.id
    );

CREATE VIEW search AS
    ( 
        SELECT 
            *
            FROM (
                    SELECT 
                        1 AS ord, 
                        'category' AS kind, 
                        categories.label, 
                        'category/' AS urlpre, 
                        categories.name AS urlpost, 
                        (COALESCE(categories.label,'') || ' ' || COALESCE(categories.name,'')) AS search 
                    FROM 
                        categories 
                    
                    UNION 
                    
                    SELECT 
                        2 AS ord, 
                        'recipe' AS kind, 
                        recipes.name AS label, 
                        'recipe/' AS urlpre, 
                        recipes.name AS urlpost, 
                        (
                            '<remove>' || 
                            recipes.name || 
                            '</remove>' || 
                            COALESCE(recipes.about,'') || 
                            ' ' || 
                            COALESCE(recipes.instructions,'') || 
                            ' ' || 
                            COALESCE(recipes.display_name, '')
                        ) AS search 
                    FROM 
                        recipes 
                    WHERE 
                        recipes.hide = false OR 
                        recipes.hide IS NULL
                
                
                    UNION 
                    
                    SELECT 
                        3 AS ord, 
                        'ingredient' AS kind, 
                        initcap(COALESCE(ingredients.plural, ingredients.name)) AS label, 
                        'ingredient/' AS urlpre, 
                        ingredients.name AS urlpost, 
                        (
                            COALESCE(ingredients.name,'') || 
                            ' ' || 
                            COALESCE(ingredients.plural,'')
                        ) AS search 
                    FROM 
                    ingredients
                ) u 
            ORDER BY u.ord
        );

