{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMaestro_breadcrumbs module="jrFollower" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrFollower" profile_id=$_post._profile_id}
    </div>
</div>

<div class="box">
    {jrMaestro_sort template="icons.tpl" nav_mode="jrFollower" profile_url=$profile_url}

    <div class="box_body">
        <div class="wrap">
            <div class="media" style="padding: 5px;">
                <div id="list">
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
            </div>
        </div>
    </div>
</div>
