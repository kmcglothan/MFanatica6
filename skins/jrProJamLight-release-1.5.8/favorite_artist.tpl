{capture name="row_template" assign="fav_artist_row"}
    {literal}
    {if isset($_items)}
    <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="123" default="Our Favorite Artist"}</h2><br>
    <br>
    <div class="container">
        {foreach from=$_items item="row"}
        <div class="row">
            <div class="col6">
                <div class="center">
                    <h2><a href="{$jamroom_url}/{$row.profile_url}">{$row.profile_name}</a></h2>
                    <br>
                    <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="large" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline img_shadow img_scale" style="max-width:256px;max-height:256px;"}</a><br>
                    <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="Songs"}: <span class="highlight-txt bold">{$row.profile_jrAudio_item_count}</span></h4>&nbsp;
                    <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="50" default="Views"}: <span class="highlight-txt bold">{$row.profile_view_count}</span></h4>
                    <br>
                </div>
            </div>
            <div class="col6 last">
                <div class="left p5">
                    {if strlen($row.profile_bio) > 0}
                    <br>
                    <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="About"}</h4>:
                    <div class="normal" style="color: #FFF;">{$row.profile_bio|truncate:500:"...":false|jrCore_format_string:$row.profile_quota_id}</div>
                    {/if}
                </div>
            </div>
            <div class="row">
                <div class="col12 last">
                    <div style="float:right; padding-top:10px; margin-top: 10px;">
                        <a href="{$jamroom_url}/{$row.profile_url}" title="More"><div class="button-more">&nbsp;</div></a>
                    </div>
                    <div class="clear">&nbsp;</div>
                </div>
            </div>
        </div>
        {/foreach}
    </div>
    {/if}
    {/literal}
{/capture}

{if isset($_conf.jrProJamLight_favorite_artist) && strlen($_conf.jrProJamLight_favorite_artist) > 0}
    {jrCore_list module="jrProfile" limit="1" profile_id=$_conf.jrProJamLight_favorite_artist template=$fav_artist_row}
{else}
    {if isset($_conf.jrProJamLight_require_images) && $_conf.jrProJamLight_require_images == 'on'}
        {jrCore_list module="jrProfile" order_by="profile_name random" quota_id=$_conf.jrProJamLight_artist_quota limit="1" template=$fav_artist_row require_image="profile_image"}
    {else}
        {jrCore_list module="jrProfile" order_by="profile_name random" quota_id=$_conf.jrProJamLight_artist_quota limit="1" template=$fav_artist_row}
    {/if}
{/if}
