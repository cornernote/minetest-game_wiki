----------------------------------
GameWiki GUI for Minetest
----------------------------------

Copyright (c) 2012 cornernote, Brett O'Donnell <cornernote@gmail.com>
Source Code: https://github.com/cornernote/minetest-gamewiki
Home Page: https://sites.google.com/site/cornernote/minetest/game-wiki

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.


-----------------
Description
-----------------

Extracts all ingame items which can then be viewed on a website.

Server admins may want to make this website public so that players can learn more about the world.


It works as follows:
- install
- load your game, which dumps all the items to JSON encoded strings inside wikidata/
- load import.php in a browser to import the JSON data into SQLite3
- copy your textures
- thats all, now you can browse your own MineTest GameWiki!



-----------------
Included Files
-----------------

MINETEST (mod to generate data):

/minetest/                      -- this folder needs to be copied into your minetest folder
  bin/
    JSON.lua                    -- this must be in the same folder as minetest.exe (site:http://regex.info/blog/lua/json) (license:unknown)
    itemcubes/                  -- the item cube pngs will be saves here (build from source required)
  src/                          -- this is needed if you want to generate itemcube images (build from source required required)
    itemdef.for-wiki.cpp
    title.for-wiki.cpp
  builtin/
    misc_register.lua.for-wiki  -- !!! IMPORTANT !!! - you need to rename to misc_register.lua - see install instructions below
    wiki.lua                    -- this file saves the minetest.register_* calls to json files
    wikidata/                   -- the json data will be saved here


GAMEWIKI (website to display wiki):

/wiki/                          -- this folder needs to be uploaded to your php5/sqlite3 website
  bootstrap/                    -- twitter bootstrap (site:http://twitter.github.com/bootstrap/) (license:http://www.apache.org/licenses/LICENSE-2.0)
  data/                         -- sqlite data
    wikidata/                   -- import json files go here before running import.php
    wiki.db                     -- sqlite3 database containing default mods
  textures/                     -- put your textures here
  config.php                    -- !!! IMPORTANT !!! - you need to edit the path to minetest if generating your own database
  import.php                    -- !!! IMPORTANT !!! - do not upload this to your public website
  *.php                         -- these are the files that output the html for the wiki



-----------------
INSTALL (using default mods)
-----------------

Upload /wiki/ to your website

Thats all  =)


-----------------
INSTALL (generate your own mods database)
-----------------

1) Copy /minetest/* to your minetest folder - nothing should be replaced

2) We need to add a line to /minetest/builtin/builtin.lua: (just under "misc_register.lua"):
dofile(minetest.get_modpath("__builtin").."/wiki.lua")

3) Open minetest  
- This will create the JSON data
- You should see a lot of files being created in /minetest/builtin/wikidata/
- Once done, move the files to /wiki/data/wikidata/

4) Edit config.php and set the path to minetest - there is no need to change the database name

5) Open import.php in a web browser  
- This will import the JSON data into an SQLite3 database
- You should notice the files being removed from /wiki/data/wikidata/

6) Copy your textures to /wiki/textures/



-----------------
INSTALL (generate itemcube images - build from source required)
-----------------

1) Open the itemdef.for-wiki.cpp and tile.for-wiki.cpp files in the src/ folder.  Search for "WIKI IMAGE EXTRACT"

2) Copy and paste this to your itemdef.cpp and tile.cpp files 

3) Build the sources. (building from source is out of the scope of this package)

4) Run the game, you will see /minetest/bin/itemcubes/ being populated

5) Copy to /wiki/itemcubes/


