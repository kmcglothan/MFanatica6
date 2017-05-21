{jrCore_module_url module="jrSoundCloud" assign="murl"}
<div class="p5">
    <span class="action_item_title">

    {if $item.action_mode == 'create'}

        {jrCore_lang module="jrSoundCloud" id=1 default="Posted a new SoundCloud Track"}:<br>
        <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.soundcloud_title_url}" title="{$item.action_data.soundcloud_title|jrCore_entity_string}">{$item.action_data.soundcloud_title}</a><br>
        {jrSoundCloud_embed item_id=$item.action_item_id auto_play=false}

    {elseif $item.action_mode == 'search'}

        {jrCore_lang module="jrSoundCloud" id="62" default="Posted new SoundCloud tracks"}:
        {math equation="x + 4" x=$item._created assign="x"}
        {jrCore_list module="jrSoundCloud" search1="_created >= `$item._created`" search2="_created <= `$x`" search3="_profile_id = `$item._profile_id`" template='null' order_by="_item_id asc" limit=5 assign="preview"}
        {if isset($preview[0]) && is_array($preview[0])}
            {foreach $preview as $_i}
                <br>&bull;&nbsp;<a href="{$jamroom_url}/{$_i.profile_url}/{$murl}/{$_i._item_id}/{$_i.soundcloud_title_url}">{$_i.soundcloud_title|truncate:60:"..."}</a>
            {/foreach}
        {else}
            <br><a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{$jamroom_url}/{$item.profile_url}/{$murl}</a>
        {/if}

    {else}

        {jrCore_lang module="jrSoundCloud" id=59 default="Updated a SoundCloud Track"}:<br>
        <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.soundcloud_title_url}" title="{$item.action_data.soundcloud_title|jrCore_entity_string}">{$item.action_data.soundcloud_title}</a><br>
        {jrSoundCloud_embed item_id=$item.action_item_id auto_play=false}

    {/if}

    </span>
</div>
