<div class="container">

    <div class="row">
        <div class="col12 last">
            <div id="top-users-box">
                <div>
                    {jrCore_module_url module="jrUser" assign="uurl"}
                    {if isset($unsub)}
                        <table class="page_table" style="margin-bottom:0">
                            <tr>
                                <th class="page_table_header" style="width:2%">&nbsp;</th>
                                <th class="page_table_header" style="width:40%">User</th>
                                <th class="page_table_header" style="width:40%">Email</th>
                                <th class="page_table_header" style="width:18%">When</th>
                            </tr>
                        </table>
                        <div id="cp-display-area">
                            <table class="page_table">
                                {foreach $unsub as $_u}
                                    {if ($_u@index % 2) === 0}
                                        <tr class="page_table_row_alt">
                                            {else}
                                        <tr class="page_table_row">
                                    {/if}
                                    <td class="page_table_cell" style="width:2%">
                                        {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$_u._user_id size="small" crop="portrait" width=40 height=40}
                                    </td>
                                    <td class="page_table_cell" style="width:40%">
                                        <a onclick="jrMailer_user_report('{$_u._user_id}', '{$campaign.c_id}')" title="View User Report"><u>{$_u.user_name}</u></a><br>
                                        <small><a href="{$jamroom_url}/{$_u.profile_url}">@{$_u.profile_url}</a></small>
                                    </td>
                                    <td class="page_table_cell" style="width:40%">{$_u.user_email}</td>
                                    <td class="page_table_cell center" style="width:18%">{$_u.u_time|jrCore_format_time:false:"relative"}</td>
                                    </tr>
                                {/foreach}
                            </table>
                        </div>
                    {else}
                        <div class="p20 center">No Users have Unsubscribed</div>
                    {/if}
                </div>
            </div>
        </div>

    </div>
</div>

<div id="top-users-holder" style="display:none"></div>
