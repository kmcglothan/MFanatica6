<div style="display: inline-block; width:100%">
    {assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
    {assign var="player_type" value=$_conf.$skin_player_type}
    {assign var="player" value="jrAudio_`$player_type`"}
    {if isset($item) && is_array($item)}
        {if isset($player_type) && strlen($player_type) > 0}
            {jrCore_media_player type=$player module="jrAudio" field="audio_file" item=$item}
        {else}
            {jrCore_media_player module="jrAudio" field="audio_file" item=$item}
        {/if}
    {elseif isset($_items) && is_array($_items)}
        {if isset($player_type) && strlen($player_type) > 0}
            {jrCore_media_player type=$player module="jrAudio" field="audio_file" items=$_items}
        {else}
            {jrCore_media_player module="jrAudio" field="audio_file" items=$_items}
        {/if}
    {/if}
</div>
