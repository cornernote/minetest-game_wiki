JSON = (loadfile "JSON.lua")() -- one-time load of the routines

-- register_craft
wiki_register_craft_count = 0
local wiki_register_craft = minetest.register_craft
minetest.register_craft = function (options) 
	wiki_register_craft(options) 
	wiki_register_craft_count = wiki_register_craft_count+1
	wikiSaveJson("register_craft", wiki_register_craft_count,{name=name,options=options})
end

-- register_abm
wiki_register_abm_count = 0
local wiki_register_abm = minetest.register_abm
minetest.register_abm = function (options) 
	wiki_register_abm(options) 
	wiki_register_abm_count = wiki_register_abm_count+1
	wikiSaveJson("register_abm", wiki_register_abm_count,{name=name,options=options})
end

-- register_entity
wiki_register_entity_count = 0;
local wiki_register_entity = minetest.register_entity
minetest.register_entity = function (name, options) 
	wiki_register_entity(name, options) 
	wiki_register_entity_count = wiki_register_entity_count+1
	wikiSaveJson("register_entity", wiki_register_entity_count,{name=name,options=options})
end

-- register_item
wiki_register_item_count = 0;
local wiki_register_item = minetest.register_item
minetest.register_item = function (name, options) 
	wiki_register_item(name, options) 
	wiki_register_item_count = wiki_register_item_count+1
	wikiSaveJson("register_item", wiki_register_item_count,{name=name,options=options})
end

-- register_alias
wiki_register_alias_count = 0;
local wiki_register_alias = minetest.register_alias
minetest.register_alias = function (name, options) 
	wiki_register_alias(name, options) 
	wiki_register_alias_count = wiki_register_alias_count+1
	wikiSaveJson("register_alias", wiki_register_alias_count,{name=name,options=options})
end

-- save json data to file
wikiSaveJson = function(name, counter, data)
	local modname = minetest.get_current_modname();
	if modname==nil then modname="" end
	local json = JSON:encode(data)
	io.output(io.open(minetest.get_modpath("__builtin").."/wikidata/"..name.."."..modname.."."..counter..".json","w"))
	io.write(json)
	io.close()
end
