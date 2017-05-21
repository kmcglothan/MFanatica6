{jrCore_module_url module="jrOneAll" assign="murl"}
{jrCore_module_url module="jrImage" assign="iurl"}
<span id="oneall_timeline_networks">
    <input type="hidden" name="oneall_share_active" value="off">
    <input type="checkbox" name="oneall_share_active" class="form_checkbox share_checkbox" checked="checked">
    {jrCore_lang module="jrOneAll" id=14 default="share activity with" assign="label"}
    <span>&nbsp;{$label}&nbsp;</span>
    <a href="{$jamroom_url}/{$murl}/networks">
        {foreach $_networks as $provider}
            <img src="{$jamroom_url}/{$iurl}/img/module/jrOneAll/{$provider}.png" width="24" height="24" alt="{$provider}" title="{$label} {$provider}">
        {/foreach}
    </a>
</span>
