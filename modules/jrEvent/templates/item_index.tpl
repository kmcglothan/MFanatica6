{jrCore_module_url module="jrEvent" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrEvent" profile_id=$_profile_id}
        </div>
        <h1>{if isset($_post._1) && strlen($_post._1) > 0}{$_post._1}{else}{jrCore_lang module="jrEvent" id=31 default="Event"}{/if}</h1>

        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo;
            <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrEvent" id=31 default="Event"}</a>
        </div>
    </div>

    <div class="block_content">

    {if jrProfile_is_profile_owner($_profile_id)}
        {jrCore_list module="jrEvent" profile_id=$_profile_id search="event_date > 0" order_by="event_date desc" pagebreak=6 page=$_post.p pager=true}
    {else}
        {jrCore_list module="jrEvent" profile_id=$_profile_id order_by="event_date desc" pagebreak=6 page=$_post.p pager=true}
    {/if}

    </div>

</div>
