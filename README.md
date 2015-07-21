# GameWiki GUI for Minetest

Copyright (c) 2012 cornernote, Brett O'Donnell <cornernote@gmail.com>

Source Code: https://github.com/cornernote/minetest-gamewiki

Home Page: https://sites.google.com/site/cornernote/minetest/game-wiki


## Description

Extracts all ingame items which can then be viewed on a website.

Server admins may want to make this website public so that players can learn more about the world.

It works as follows:

- install
- load your game, which dumps all the items to JSON encoded strings inside wikidata/
- load import.php in a browser to import the JSON data into SQLite3
- copy your textures
- thats all, now you can browse your own MineTest GameWiki!


## Included Files

MINETEST (mod to generate data):

```
/minetest/                      -- this folder needs to be copied into your minetest folder
  bin/
    JSON.lua                    -- this must be in the same folder as minetest.exe (site:http://regex.info/blog/lua/json) (license:unknown)
    itemcubes/                  -- the item cube images will be saved here
  src/
    itemdef.cpp                 -- !!! IMPORTANT !!! - overwrites existing - used to extract item cube images - see install instructions below
    client/
      title.for-wiki.cpp        -- !!! IMPORTANT !!! - overwrites existing - used to extract item cube images - see install instructions below
  builtin/
    game/
      init.lua                  -- !!! IMPORTANT !!! - overwrites existing - extracts item definitions - see install instructions below
      wiki.lua                  -- this file saves the minetest.register_* calls to json files
      wikidata/                 -- the json data will be saved here
```

GAMEWIKI (website to display wiki):

```
/wiki/                          -- this folder needs to be uploaded to your php5/sqlite3 website
  data/                         -- sqlite data
    wikidata/                   -- import json files go here before running import.php
  itemcubes/                    -- put item cube images here
  textures/                     -- put texture images here
  build.php                     -- builds the output html/md files
  config.php                    -- configuration settings
  globals.php                   -- global functions
  import.php                    -- imports data from json files to sqlite
```


## Install

Copy /minetest/* to your minetest folder - some files will be replaced.


## Extract Cube Images

1) Build the sources. (building from source is out of the scope of this package)

2) Run the game:
- This will create the cube images.
- You should see `/minetest/bin/itemcubes/` being populated.  
- **Note**, you have to be in the `/minetest/bin/` folder when you start the game.

3) Copy to cube images to `/wiki/itemcubes/`.


## Extract Minetest Data

1) Run the game:
- This will create the JSON data
- You should see a lot of files being created in `/minetest/builtin/game/wikidata/`

2) Once done, move the JSON files to `/wiki/data/wikidata/`.

3) Open `import.php` in a web browser:
- This will import the JSON data into an SQLite3 database.
- You should notice the files being removed from `/wiki/data/wikidata/`.

4) Copy additional textures to `/wiki/textures/`.

5) Open `build.php` in a web browser:
- This will create html/md files to use on your website.


