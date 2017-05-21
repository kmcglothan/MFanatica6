{jrCore_module_url module="jrAudio" assign="murl"}
<div class="item rounded urlscan_card">
    <div class="row">

        <div class="col12 last">
            <div class="p10">

                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}"><h2>{$item.audio_title}</h2></a>

                <br><br>

                {assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
                {assign var="player_type" value=$_conf.$skin_player_type}
                {assign var="player" value="jrAudio_`$player_type`"}

                {if strlen($player_type) > 0}
                    {jrCore_media_player type=$player module="jrAudio" field="audio_file" item_id=$item._item_id autoplay=$autoplay}
                {else}
                    {jrCore_media_player module="jrAudio" field="audio_file" item_id=$item._item_id autoplay=$autoplay}
                {/if}

            </div>
        </div>

    </div>
</div>

