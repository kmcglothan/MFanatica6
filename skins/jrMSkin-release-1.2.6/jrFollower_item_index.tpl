{* default index for profile *}
{jrCore_module_url module="jrFollower" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMSkin_breadcrumbs module="jrFollower" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrFollower" profile_id=$_profile_id}
    </div>
</div>


<div class="box">
    {jrMSkin_sort template="icons.tpl" nav_mode="jrFollower" profile_url=$profile_url}

    <div class="box_body">
        <div class="wrap">
            <div class="media" style="padding: 5px;">
                <div id="list">
                    {capture name="template" assign="ftpl"}
                    {literal}
                    {jrCore_lang module="jrFollower" id=5 default="pending" assign="pnd"}
                    {jrCore_lang module="jrFollower" id=30 default="approve" assign="apr"}
                    {jrCore_lang module="jrFollower" id=31 default="delete" assign="del"}
                    {jrCore_lang module="jrFollower" id=33 default="Are you sure you want to delete this follower?" assign="prompt"}
                    {jrCore_module_url module="jrFollower" assign="murl"}
                    {if isset($_items)}
                    {foreach $_items as $item}

                    {if $item@first || ($item@iteration % 4) == 1}
                    <div class="row">
                        {/if}

                        {if ($item@iteration % 4) == 0}
                        <div class="col3 last">
                            {else}
                            <div class="col3">
                                {/if}
                                {if $item.follow_active != 1}
                                <div class="p5 center field-hilight" style="position:relative">
                                    {assign var="txt" value="@`$item.profile_url` - `$pnd`"}
                                    {else}
                                    <div class="p5 center" style="position:relative">
                                        {assign var="txt" value="@`$item.profile_url`"}
                                        {/if}
                                        <div class="shadow">
                                            <a href="{$jamroom_url}/{$item.profile_url}">
                                                {jrCore_module_function
                                                function="jrImage_display"
                                                module="jrUser"
                                                type="user_image"
                                                item_id=$item._user_id size="large" crop="auto" class="img_scale" width=false height=false alt="{$txt|jrCore_entity_string}" title="{$txt|jrCore_entity_string}"}</a>
                                        </div>
                                        <br><a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url}</a><br>
                                        {if jrProfile_is_profile_owner($item.follow_profile_id)}
                                        <div style="position:absolute;bottom:28px;left:10px">
                                            {if $item.follow_active != 1}
                                            <input type="button" class="form_button" style="margin:0" value="{$apr|jrCore_entity_string}" onclick="jrCore_window_location('{$jamroom_url}/{$murl}/approve/{$item.follow_profile_id}/{$item._user_id}');">
                                            {/if}
                                            <input type="button" class="form_button" style="margin:0" value="{$del|jrCore_entity_string}" onclick="if(confirm('{$prompt|addslashes}')) { jrCore_window_location('{$jamroom_url}/{$murl}/delete/{$item.follow_profile_id}/{$item._user_id}'); }">
                                        </div>
                                        {/if}
                                    </div>
                                </div>
                                {if ($item@iteration % 4) == 0 || $item@last}
                            </div>
                            {/if}

                            {/foreach}
                            <div style="clear: both"></div>
                            {/if}
                            {/literal}
                            {/capture}

                            {if jrProfile_is_profile_owner($_profile_id)}
                                {jrCore_list module="jrFollower" search1="follow_profile_id = `$_profile_id`" order_by="_created desc" pagebreak=9 page=$_post.p template=$ftpl pager=true}
                            {else}
                                {jrCore_list module="jrFollower" search1="follow_profile_id = `$_profile_id`" search2="follow_active = 1" order_by="_created desc" pagebreak=9 page=$_post.p template=$ftpl pager=true}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
