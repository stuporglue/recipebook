-- For PostgreSQL
-- TODO: Add foreign key constraints
-- TODO: Pre-load table with common units of measure

CREATE TABLE categories (
    id serial,
    name character varying NOT NULL
);

CREATE TABLE ingredients (
    id serial,
    name character varying NOT NULL,
    plural character varying
);

CREATE TABLE recipe_ingredient (
    id serial,
    recipe_id integer NOT NULL,
    quantity double precision,
    unit_id integer NOT NULL,
    premodifier character varying,
    ingredient_id integer NOT NULL,
    postmodifier character varying
);

CREATE TABLE units (
    id serial,
    name character varying NOT NULL,
    abbreviation character varying,
    plural character varying
);

CREATE VIEW pretty_ingredients AS
    SELECT 
        ri.recipe_id, 
        ri.quantity, 
        CASE 
            WHEN ((u.name)::text = 'count'::text) THEN ''::character varying 
            WHEN (ri.quantity = (1)::double precision) THEN u.name 
            ELSE u.plural 
        END AS unit, 
        CASE 
            WHEN ((u.name)::text = 'count'::text) THEN ''::character varying 
            ELSE u.abbreviation 
        END AS abbreviation, 
        COALESCE(
            ri.premodifier, 
            CASE 
                WHEN (ri.quantity = (1)::double precision) THEN i.name 
                ELSE COALESCE(i.plural, i.name) END, 
                ri.postmodifier
        ) AS ingredient 
    FROM 
        recipe_ingredient ri, 
        ingredients i, 
        units u 
    WHERE 
        ri.ingredient_id = i.id AND 
        ri.unit_id = u.id;

CREATE TABLE recipes (
    id serial,
    name character varying NOT NULL,
    about text,
    instructions text,
    category integer NOT NULL,
    quick boolean,
    display_name character varying,
    hide boolean
);


CREATE TABLE recpie_recipe (
    id serial,
    parent integer NOT NULL,
    child integer NOT NULL,
    childname character varying NOT NULL
);
