Dynamo - dynamic avatar MOD for phpBB
============================

**Why is it called _Dynamo_?**

Because I couldn't think of a better name and I needed a short name for database tables and config field names.

**What will this repo contain?**

This repo will contain all the modified/new files (only) that are required for this MOD to work. Once it is ready for release, it will contain the MODX file as well, and the modified files will be removed from the repo.

Will add ACP and UCP modules.

**What's the status of this MOD?**

I had some code (mainly for the ACP and a bit of skeleton code for the UCP module) already written from when I first started writing this MOD, but I decided to restructure it a bit and thus start from scratch. So it's  under heavy development right now, and is definitely not ready for public consumption just yet. That said, most of the code left to write is fairly trivial, so it's just a matter of implementation.

Permissions
-----------

`acl_a_dynamo` - the only one lol (for full admins only), might add more later if necessary

Database structure and SQL
--------------------------

`phpbb_config` - add the following configuration-related fields:

*   `dynamo_enabled`
*   `dynamo_use_points` - if set to true, it will look for a points MOD (by seeing if `points_enable` in `phpbb_config` is set to true). if it can find one, then the points-related options will be available.
*   `dynamo_change_base` - if set to true, users can change their base after they've created it. if set to false, users cannot change their base (i.e. the items in all layers designated 'base').
*   `dynamo_mandatory` - if set to true, then users cannot have a regular avatar, and must set a dynamic avatar in order for an avatar to show up at all. if set to false, then users can choose between a regular avatar and a dynmic one 

`phpbb_dynamo_layers` - new table to hold the information associated with each layer, with the following fields:

*   `dynamo_layer_id` - should be immutable as each layer ID is linked to its items
*   `dynamo_layer_mandatory` - whether it's required or not. can only be set to true if there is at least one item associated with it.
*   `dynamo_layer_default` - the default item id for the layer. can choose between all the items associated with that layer.
*   `dynamo_layer_name`
*   `dynamo_layer_desc` - description (optional)
*   `dynamo_layer_position` - from 1 to the number of layers. Or, allow it to accept any sort of ordering? For more graceful behaviour in case of manual change or something?

`phpbb_dynamo_items` - new table to hold the information associated with each item, with the following fields:

*   `dynamo_item_id` - image files will be saved with their item id as a filename
*   `dynamo_item_layer` - the ID of the layer it is associated with. Deleting a layer should delete all of its items.
*   `dynamo_item_name`
*   `dynamo_item_desc` - description (optional)

`phpbb_dynamo_users` - new table to hold each user's avatar information. no keys here as each user can and probably will show up multiple times.

*   `dynamo_user_id`
*   `dynamo_user_layer`
*   `dynamo_user_item` - item ID for that layer
*   Index: `user_layer_index` to ensure that there is only one user_id/layer_id entry for each combination if that makes sense
