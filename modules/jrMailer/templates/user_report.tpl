{jrCore_module_url module="jrUser" assign="uurl"}
<div class="container">

    <div class="row" style="margin-bottom: 6px">
        <div class="col12 last">
            <div>
                <table class="page_banner">
                    <tr>
                        <td class="page_banner_left">
                            <a onclick="jrMailer_top_users()">{jrCore_icon icon="previous"}</a> &nbsp;{$user_name}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col12 last">

            <table class="page_table" style="margin-bottom:6px">
                <tr class="page_table_row">
                    <td class="page_table_cell p10" style="width:15%;vertical-align:top">
                        {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$_user_id size="larger" crop="portrait" width=232 height=232}
                    </td>
                    <td class="page_table_cell p10" style="width:35%;vertical-align:top">
                        <div style="position:relative">
                            <div style="position:absolute;top:0;right:0">
                                <input type="button" value="view account" class="form_button" style="margin-left:0" onclick="window.open('{$jamroom_url}/{$uurl}/account/profile_id={$_profile_id}/user_id={$_user_id}')">
                            </div>
                            <a href="{$jamroom_url}/{$profile_url}"><b>@{$profile_url}</b></a><br>
                            Joined: {$_created|jrCore_format_time:false:'relative'}<br>
                            Last Login: {$user_last_login|jrCore_format_time:false:'relative'}<br>
                            Location: {$latest_campaign_location|default:"unknown"}<br>
                            Latitude: {$user_latitude}<br>
                            Longitude: {$user_longitude}
                            {if $user_platform != "unknown"}
                                <br>Platform: {$user_platform}
                            {/if}
                            {if $user_browser != "unknown"}
                                <br>Browser: {$user_browser} ({$user_version})
                            {/if}
                        </div>
                    </td>
                    <td class="page_table_cell center" style="width:50%;padding:0">
                        {if strlen($user_city) > 0 && isset($user_latitude) && $user_latitude != '0'}
                            <iframe style="width:100%;height:250px" frameborder="0" scrolling="yes" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?&amp;q={$user_latitude},{$user_longitude}&amp;z=13&amp;iwloc=near&amp;output=embed"></iframe>
                        {elseif strlen($user_city) > 0 && isset($latest_campaign_location) && strlen($latest_campaign_location) > 0}
                            <iframe style="width:100%;height:250px" frameborder="0" scrolling="yes" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?&amp;q={$latest_campaign_location|urlencode}&amp;z=11&amp;iwloc=near&amp;output=embed"></iframe>
                        {elseif isset($latest_campaign_location) && strlen($latest_campaign_location) > 0}
                            <iframe style="width:100%;height:250px" frameborder="0" scrolling="yes" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?&amp;q={$latest_campaign_location|urlencode}&amp;z=3&amp;iwloc=near&amp;output=embed"></iframe>
                        {else}
                            Unable to determine this user's location at this time.
                            <br>
                            Try again later.
                        {/if}
                    </td>
                </tr>
            </table>

            <table class="page_table">
                <tr class="page_table_row">
                    <th class="page_table_header" style="width:77%">Clicked URL</th>
                    <th class="page_table_header" style="width:8%">Clicks</th>
                </tr>
                {if count($_urls) > 0}
                    {foreach $_urls as $url => $cnt}
                        {if ($cnt@index % 2) === 0}
                            <tr class="page_table_row_alt">
                                {else}
                            <tr class="page_table_row">
                        {/if}
                        <td class="page_table_cell p10"><a href="{$jamroom_url}{$url}">{$url}</a></td>
                        <td class="page_table_cell p10 center">{$cnt|jrCore_number_format}</td>
                        </tr>
                    {/foreach}
                {/if}
            </table>

        </div>
    </div>
</div>
