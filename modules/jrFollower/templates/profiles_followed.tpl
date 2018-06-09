<div class="title">
    <h1>{jrCore_lang module="jrFollower" id=2 default="following"}</h1>
    <div class="breadcrumbs">
        <a href="{$jamroom_url}/{$profile_url}">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrFollower" id=2 default="following"}</a>
    </div>
</div>

<div class="block_content">

    {jrCore_module_url module="jrFollower" assign="murl"}
    {if isset($_items)}
    {foreach $_items as $item}

    {if $item@first || ($item@iteration % 4) == 1}
    <div class="row">
    {/if}

        {if ($item@iteration % 4) === 0}
        <div class="col3 last">
        {else}
        <div class="col3">
        {/if}

            <div class="p5 center" style="position:relative">
                <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="large" crop="auto" class="img_scale" width=false height=false alt="{$txt|jrCore_entity_string}" title="{$txt|jrCore_entity_string}"}</a>
                <br>
                <a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url}</a>
                <br>
                {if $item._followers && count($item._followers) > 1}
                    {jrCore_lang module="jrFollower" id="40" default="Followed by:"}<br>
                    {foreach $item._followers as $follower}
                        {$follower.user_name}<br>
                    {/foreach}
                {/if}
            </div>
        </div>

        {if ($item@iteration % 4) === 0 || $item@last}
        <div style="clear:both"></div>

    </div>
    {/if}
    {/foreach}
    {/if}

</div>
