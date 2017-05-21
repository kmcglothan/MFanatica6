{* PROFILE SPOTLIGHT ROW *}
{if $_params.module == 'jrProfile'}

    {if isset($_items)}
        {foreach from=$_items item="item"}
            {if $item@first || ($item@iteration % 4) == 1}
                <div class="row">
            {/if}
            <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if} center">
                <div class="center m0 p8">
                    <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="medium" crop="auto" alt=$item.profile_name title=$item.profile_name class="iloutline img_shadow"}</a>
                    <br>
                    <div class="media_title" style="padding-top:5px;padding-bottom:0">
                        <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a>
                    </div>
                </div>
            </div>
            {if $item@last || ($item@iteration % 4) == 0}
                </div>
            {/if}
        {/foreach}
    {/if}

    {* AUDIO SPOTLIGHT ROW *}
{elseif $_params.module == 'jrAudio'}

    {if isset($_items)}
        <div class="container">
            {foreach from=$_items item="item"}
                {if $item@first || ($item@iteration % 4) == 1}
                    <div class="row">
                {/if}
                <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                    <div class="p5" style="text-align:center;">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="medium" crop="auto" alt=$item.audio_title title=$item.audio_title class="iloutline img_shadow"}</a><br>
                        <div style="width:196px;margin:0 auto;">
                            <table>
                                <tr>
                                    <td class="media_title capital" style="text-align:center;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.audio_title_url}" title="{$item.audio_title}">{$item.audio_title|truncate:20:"...":false}</a><br>
                                        <span class="normal"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">{$item.profile_name|truncate:20:"...":false}</a></span>
                                    </td>
                                    <td style="text-align:right;">
                                        {if $item.audio_file_extension == 'mp3'}
                                            {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item image="small_button"}
                                        {/if}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                {if $item@last || ($item@iteration % 4) == 0}
                    </div>
                {/if}
            {/foreach}
        </div>
    {/if}

    {* SOUNDCLOUD SPOTLIGHT ROW *}
{elseif $_params.module == 'jrSoundCloud'}

    {if isset($_items)}
        <div class="container">
            {foreach from=$_items item="item"}
                {if $item@first || ($item@iteration % 4) == 1}
                    <div class="row">
                {/if}
                <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                    <div class="p5" style="text-align:center;">
                        {if isset($item.soundcloud_artwork_url) && strlen($item.soundcloud_artwork_url) > 0}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.soundcloud_title_url}"><img src="{$item.soundcloud_artwork_url}" alt="{$item.soundcloud_title_url|jrCore_entity_string}" class="iloutline img_shadow" width="196" height="196"></a><br>
                        {/if}
                        <div style="width:196px;margin:0 auto;">
                            <table>
                                <tr>
                                    <td class="media_title capital" style="text-align:center;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.soundcloud_title_url}" title="{$item.soundcloud_title}">{$item.soundcloud_title|truncate:20:"...":false}</a><br>
                                        <span class="normal"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">{$item.profile_name|truncate:20:"...":false}</a></span>
                                    </td>
                                    <td style="text-align:right;">
                                        {jrSoundCloud_player params=$item}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                {if $item@last || ($item@iteration % 4) == 0}
                    </div>
                {/if}
            {/foreach}
        </div>
    {/if}


    {* VIDEO SPOTLIGHT ROW *}
{elseif $_params.module == 'jrVideo'}

    {if isset($_items)}
        <div class="container">
            {foreach from=$_items item="item"}
                {if $item@first || ($item@iteration % 4) == 1}
                    <div class="row">
                {/if}
                <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                    <div class="p5" style="text-align:center;">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.video_title_url}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="medium" crop="auto" alt=$item.video_title title=$item.video_title class="iloutline img_shadow"}</a><br>
                        <div style="width:196px;margin:0 auto;">
                            <table>
                                <tr>
                                    <td class="media_title capital" style="text-align:center;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.video_title_url}" title="{$item.video_title}">{$item.video_title|truncate:25:"...":false}</a><br>
                                        <span class="normal"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">{$item.profile_name|truncate:20:"...":false}</a></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                {if $item@last || ($item@iteration % 4) == 0}
                    </div>
                {/if}
            {/foreach}
        </div>
    {/if}

    {* YOUTUBE SPOTLIGHT ROW *}
{elseif $_params.module == 'jrYouTube'}

    {if isset($_items)}
        <div class="container">
            {foreach from=$_items item="item"}
                {if $item@first || ($item@iteration % 4) == 1}
                    <div class="row">
                {/if}
                <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                    <div class="p5" style="text-align:center;">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.youtube_title_url}"><img src="{$item.youtube_artwork_url}" alt="{$item.youtube_title|jrCore_entity_string}" class="iloutline img_shadow" width="196" height="196"></a><br>
                        <div style="width:196px;margin:0 auto;">
                            <table>
                                <tr>
                                    <td class="media_title capital" style="text-align:center;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.youtube_title_url}" title="{$item.youtube_title}">{$item.youtube_title|truncate:25:"...":false}</a><br>
                                        <span class="normal"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">{$item.profile_name|truncate:20:"...":false}</a></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                {if $item@last || ($item@iteration % 4) == 0}
                    </div>
                {/if}
            {/foreach}
        </div>
    {/if}


    {* VIMEO SPOTLIGHT ROW *}
{elseif $_params.module == 'jrVimeo'}

    {if isset($_items)}
        <div class="container">
            {foreach from=$_items item="item"}
                {if $item@first || ($item@iteration % 4) == 1}
                    <div class="row">
                {/if}
                <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                    <div class="p5" style="text-align:center;">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.vimeo_title_url}"><img src="{$item.vimeo_artwork_url}" class="iloutline img_shadow" width="196" height="196"></a><br>
                        <div style="width:196px;margin:0 auto;">
                            <table>
                                <tr>
                                    <td class="media_title capital" style="text-align:center;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.viemo_title_url}" title="{$item.vimeo_title}">{$item.vimeo_title|truncate:25:"...":false}</a><br>
                                        <span class="normal"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">{$item.profile_name|truncate:20:"...":false}</a></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                {if $item@last || ($item@iteration % 4) == 0}
                    </div>
                {/if}
            {/foreach}
        </div>
    {/if}


    {* EVENT SPOTLIGHT ROW *}
{elseif $_params.module == 'jrEvent'}

    {if isset($_items)}
        <div class="container">
            {foreach from=$_items item="item"}
                {if $item@first || ($item@iteration % 4) == 1}
                    <div class="row">
                {/if}
                <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                    <div class="p5" style="text-align:center;">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.event_title_url}">{jrCore_module_function function="jrImage_display" module="jrEvent" type="event_image" item_id=$item._item_id size="medium" crop="auto" alt=$item.event_title class="iloutline img_shadow"}</a><br>
                        <div style="width:196px;margin:0 auto;">
                            <table>
                                <tr>
                                    <td class="media_title capital" style="text-align:center;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/calendar/month={$item.event_date|jrCore_date_format:"%-m"}/year={$item.event_date|jrCore_date_format:"%Y"}">{jrCore_image module="jrEvent" image="calendar_icon.png"}</a>&nbsp;
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.event_title_url}" title="{$item.event_title}">{$item.event_title|truncate:20:"...":false}</a><br>
                                        <span class="normal"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">{$item.profile_name|truncate:20:"...":false}</a></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                {if $item@last || ($item@iteration % 4) == 0}
                    </div>
                {/if}
            {/foreach}
        </div>
    {/if}


    {* BLOG SPOTLIGHT ROW *}
{elseif $_params.module == 'jrBlog'}

    {if isset($_items)}
        <div class="container">
            {foreach from=$_items item="item"}
                {if $item@first || ($item@iteration % 4) == 1}
                    <div class="row">
                {/if}
                <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                    <div class="p5" style="text-align:center;">
                        <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="medium" crop="auto" alt=$item.profile_name title=$item.profile_name class="iloutline img_shadow"}</a><br>
                        <div style="width:196px;margin:0 auto;">
                            <table>
                                <tr>
                                    <td class="media_title capital" style="text-align:center;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.blog_title_url}" title="{$item.blog_title}">{$item.blog_title|truncate:25:"...":false}</a><br>
                                        <span class="normal"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">{$item.profile_name|truncate:20:"...":false}</a></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                {if $item@last || ($item@iteration % 4) == 0}
                    </div>
                {/if}
            {/foreach}
        </div>
    {/if}


{/if}
