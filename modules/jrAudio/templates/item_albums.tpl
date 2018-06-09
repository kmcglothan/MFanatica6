{jrCore_module_url module="jrAudio" assign="murl"}
{if !isset($_post._2)}
    {jrCore_page_title title="`$profile_name` - {jrCore_lang module="jrAudio" id="41" default="Audio"}"}

    {* We're showing a list of existing albums *}
    <div class="block">

        <div class="title">
            <div class="block_config">

                {jrCore_bundle_index_buttons module="jrAudio" profile_id=$_profile_id create_action="`$murl`/create_album" create_alt=35}

            </div>
            <h1>{jrCore_lang module="jrAudio" id=34 default="Albums"}</h1>
            <div class="breadcrumbs">
                <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo;
                {if jrCore_module_is_active('jrCombinedAudio')}
                    <a href="{$jamroom_url}/{$profile_url}/{jrCore_module_url module="jrCombinedAudio"}">{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}</a>
                {else}
                    <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrAudio" id="41" default="Audio"}</a>
                {/if}
                &raquo; {jrCore_lang module="jrAudio" id="34" default="Albums"}
            </div>

        </div>

        {capture name="row_template" assign="template"}
            {literal}
                {if isset($_items) && is_array($_items)}
                {jrCore_module_url module="jrAudio" assign="murl"}
                {foreach from=$_items item="item"}
                <div class="item">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="icon" crop="auto" alt=$item.audio_title title=$item.audio_title class="iloutline" width=false height=false}</a>
                    &nbsp;&nbsp;<a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}"><h1>{$item.audio_album}</h1></a>
                    <div class="block_config">

                        {jrCore_bundle_list_buttons module="jrAudio" profile_id=$item._profile_id bundle_name=$item.audio_album create_action="`$murl`/create_album" create_alt=35 update_action="`$murl`/update_album/`$item.audio_album_url`" update_alt=60 delete_action="`$murl`/delete_album/`$item.audio_album_url`" delete_alt=56}

                    </div>
                </div>
                {/foreach}
                {/if}
            {/literal}
        {/capture}

        <div class="block_content">

            {jrCore_list module="jrAudio" profile_id=$_profile_id order_by="_created desc" group_by="audio_album_url" pagebreak="6" page=$_post.p template=$template pager=true}

        </div>

    </div>

{else}
    {* Show our audio items in this album *}

    {capture name="row_template" assign="template"}
    {literal}

    {if isset($_items) && is_array($_items)}
    {jrCore_page_title title="`$_items[0]['audio_album']` - `$_items[0]['profile_name']` inside"}
    {jrCore_module_url module="jrAudio" assign="murl"}
    <div class="block">

        <div class="title">

            <div class="block_config">

                {jrCore_bundle_detail_buttons module="jrAudio" profile_id=$_items[0]._profile_id bundle_name=$_items[0].audio_album create_action="`$murl`/create_album" update_action="`$murl`/update_album/`$_items.0.audio_album_url`" delete_action="`$murl`/delete_album/`$_items.0.audio_album_url`"}

            </div>
            <h1>{$_items.0.audio_album}</h1>
            <div class="breadcrumbs">
                <a href="{$jamroom_url}/{$_items.0.profile_url}/">{$_items.0.profile_name}</a> &raquo;
                {if jrCore_module_is_active('jrCombinedAudio') && $_items.0.quota_jrCombinedAudio_allowed == 'on'}
                    <a href="{$jamroom_url}/{$_items.0.profile_url}/{jrCore_module_url module='jrCombinedAudio'}">{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}</a>
                {else}
                    <a href="{$jamroom_url}/{$_items.0.profile_url}/{$murl}">{jrCore_lang module="jrAudio" id="41" default="Audio"}</a>
                {/if}
                &raquo; <a href="{$jamroom_url}/{$_items.0.profile_url}/{$murl}/albums">{jrCore_lang module="jrAudio" id="34" default="Albums"}</a> &raquo; {$_items.0.audio_album}
            </div>

        </div>

        <div class="block_content">

            <div class="item">
                {if $_items.0.audio_image_size > 0}
                <div class="center" style="padding:0;margin:0 auto;">
                    {jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$_items.0._item_id size="xxxlarge" crop="805:500" class="img_scale" alt=$_items.0.audio_title style="max-width:805px;max-height:500px;margin:0 auto;border-radius:0;"}
                </div>
                <br>
                {/if}
                {assign var="ap" value="`$_conf.jrCore_active_skin`_auto_play"}
                {assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
                {assign var="player_type" value=$_conf.$skin_player_type}
                {assign var="player" value="jrAudio_`$player_type`"}
                {if isset($player_type) && strlen($player_type) > 0}
                    {jrCore_media_player type=$player module="jrAudio" field="audio_file" search1="_profile_id = `$_items.0._profile_id`" search2="audio_album = `$_items.0.audio_album`" order_by="audio_file_track numerical_asc" limit="50" autoplay=$_conf.$ap}
                {else}
                    {jrCore_media_player module="jrAudio" field="audio_file" search1="_profile_id = `$_items.0._profile_id`" search2="audio_album = `$_items.0.audio_album`" order_by="audio_file_track numerical_asc" limit="50" autoplay=$_conf.$ap}
                {/if}
            </div>

            <section>
                <ul class="sortable list" style="list-style:none outside none;padding-left:0;">
                    {foreach from=$_items item="item" name="loop"}
                    <li data-id="{$item._item_id}">
                        <div class="item">
                            <div class="container">
                                <div class="row">
                                    <div class="col1">
                                        <div class="p5">
                                            {$item@iteration}
                                        </div>
                                    </div>
                                    <div class="col7">
                                        <div class="p5" style="overflow-wrap:break-word">
                                            <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a></h2><br>
                                            {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_number|default:0 }
                                        </div>
                                    </div>
                                    <div class="col4 last">
                                        <div class="p5">
                                            <div class="block_config">
                                                {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrAudio" field="audio_file" item=$item}
                                                {jrCore_module_function function="jrFoxyCartBundle_button" module="jrAudio" field="audio_file" item=$item}
                                                {jrCore_module_function function="jrPlaylist_button" playlist_for="jrAudio" item_id=$item._item_id title="Add To Playlist"}
                                                {jrCore_item_update_button module="jrAudio" profile_id=$item._profile_id item_id=$item._item_id style="width:100px"}
                                                {jrCore_item_delete_button module="jrAudio" profile_id=$item._profile_id item_id=$item._item_id style="width:100px;margin:6px 0"}
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    {/foreach}
                </ul>
            </section>
            {/if}

        </div>

    </div>

    {/literal}
    {/capture}

    {$album_url = jrCore_url_string($_post._2)}
    {if strlen($album_url) > 0}
        {jrCore_list module="jrAudio" profile_id=$_profile_id search2="audio_album_url = `$album_url`" order_by="audio_file_track numerical_asc" limit="50" template=$template}
    {else}
        {jrCore_lang module="jrAudio" id="69" default="Album not found"}
    {/if}

    {* We want to allow the item owner to re-order *}
    {if jrProfile_is_profile_owner($_profile_id)}

        <style type="text/css">
        .sortable {
            margin: auto;
            padding: 0;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .sortable li {
            list-style: none;
            cursor: move;
        }
        li.sortable-placeholder {
            border: 1px dashed #BBB;
            background: none;
            height: 60px;
            margin: 12px;
        }
        </style>

        <script type="text/javascript">
            $(function() {
                $('.sortable').sortable().bind('sortupdate', function(e,u) {
                    var o = $('ul.sortable li').map(function(){
                        return $(this).data("id");
                    }).get();
                    $.post(core_system_url + '/' + jrAudio_url + "/order_update/__ajax=1", {
                        audio_file_track: o
                    });
                });
            });
        </script>

    {/if}

{/if}
