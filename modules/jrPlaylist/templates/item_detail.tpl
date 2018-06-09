{jrCore_module_url module="jrPlaylist" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_detail_buttons module="jrPlaylist" item=$item}
        </div>
        <h1>{$item.playlist_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrPlaylist" id="9" default="Playlist"}</a> &raquo; {$item.playlist_title}
        </div>
    </div>

    <div class="block_content">

        {if $item.playlist_count > 0}

            <div class="item">
                {assign var="ap" value="`$_conf.jrCore_active_skin`_auto_play"}
                {assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
                {assign var="player_type" value=$_conf.$skin_player_type}
                {assign var="player" value="jrPlaylist_`$player_type`"}
                {if isset($player_type) && strlen($player_type) > 0}
                    {jrCore_media_player type=$player module="jrPlaylist" item=$item autoplay=$_conf.$ap}
                {else}
                    {jrCore_media_player module="jrPlaylist" item=$item autoplay=$_conf.$ap}
                {/if}

            </div>

            <section>
                <ul class="sortable list" style="list-style:none outside none;padding-left:0;">
                    {foreach $item.playlist_items as $playlist_item}
                        <li data-id="{$playlist_item.playlist_module}-{$playlist_item._item_id}">
                            {jrCore_include template=$item.playlist_templates[$playlist_item.playlist_module] module=$playlist_item.playlist_module playlist_id=$item._item_id}
                        </li>
                    {/foreach}
                </ul>
            </section>

            {* We want to allow the item owner to re-order *}
            {if jrProfile_is_profile_owner($item._profile_id)}

                <style type="text/css">
                    .sortable{
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
                        height: 100px;
                        margin: 12px;
                    }
                </style>

                <script>
                    $(function() {
                        $('.sortable').sortable().bind('sortupdate', function(event,ui) {
                            //Triggered when the user stopped sorting and the DOM position has changed.
                            var o = $('ul.sortable li').map(function(){
                                return $(this).data("id");
                            }).get();
                            $.post(core_system_url + '/' + jrPlaylist_url + "/order_update/id={$item._item_id}/__ajax=1", {
                                playlist_order: o
                            });
                        });
                    });
                </script>

            {/if}

            {* bring in module features *}
            {jrCore_item_detail_features module="jrPlaylist" item=$item}

        {else}

            <div class="item">{jrCore_lang module="jrPlaylist" id="44" default="This playlist is empty and no longer exists"}</div>

        {/if}

    </div>

</div>
