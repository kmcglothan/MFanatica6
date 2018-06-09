{* ROW TEMPLATE *}
{capture name="row_tempalte" assign="fav_song_row"}
    {literal}
    {jrCore_module_url module="jrAudio" assign="murl"}
    {if isset($_items)}
    <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="136" default="Our Favorite Song"}</h2><br>
    <br>
    {foreach from=$_items item="item"}
    <div class="container">

        <div class="row">

            <div class="col5">
                <div class="center">
                    <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a></h2>
                    <br>
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="large" crop="auto" class="iloutline img_shadow img_scale" alt=$item.audio_title title=$item.audio_title style="max-width:256px;max-height:256px;"}</a><br>
                    <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="112" default="By"}: <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></h3>
                    <br>
                </div>
            </div>
            <div class="col7 last">
                <div class="left p20">
                    <br>
                    <h3>{jrCore_lang module="jrAudio" id="12" default="genre"}: <span class="highlight-txt bold">{$item.audio_genre}</span></h3>
                    <br>
                    <br>
                    {if isset($item.audio_album) && strlen($item.audio_album) > 0}
                    <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="64" default="album"}: <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a></h3>
                    <br>
                    <br>
                    {/if}
                    {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0 }
                    <br>
                    <br>
                    {if $item.audio_file_extension == 'mp3'}
                        {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item image="button_player"}
                    {else}
                        {jrCore_lang skin=$_conf.jrCore_active_skin id="156" default="Download" assign="alttitle"}
                        <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_image image="download.png" alt=$alttitle title=$alttitle} </a>
                    {/if}
                </div>
            </div>
            <div class="row">
                <div class="col12 last">
                    <div style="float:right; padding-top:10px; margin-top: 10px;">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}" title="More"><div class="button-more">&nbsp;</div></a>
                    </div>
                    <div class="clear">&nbsp;</div>
                </div>
            </div>

        </div>

    </div>

    {/foreach}
    {/if}
    {/literal}
{/capture}

{if isset($_conf.jrProJam_favorite_song) && strlen($_conf.jrProJam_favorite_song) > 0}
    {jrCore_list module="jrAudio" limit="1" search1="_item_id in `$_conf.jrProJam_favorite_song`" template=$fav_song_row}
{else}
    {if isset($_conf.jrProJam_require_images) && $_conf.jrProJam_require_images == 'on'}
        {jrCore_list module="jrAudio" order_by="audio_title random" limit="1" template=$fav_song_row require_image="audio_image"}
    {else}
        {jrCore_list module="jrAudio" order_by="audio_title random" limit="1" template=$fav_song_row}
    {/if}
{/if}
