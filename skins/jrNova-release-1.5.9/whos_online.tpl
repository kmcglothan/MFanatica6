<div class="container">
{if isset($master)}
    <div class="row">
        <div class="col12 last">
            <span class="media_title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="56" default="Master"} {jrCore_lang  skin=$_conf.jrCore_active_skin id="57" default="Admins"}</span>
            <hr>
        </div>
    </div>
    <div class="row">
        {foreach from=$master item="m_admin"}
            <div class="col3{if $m_admin@last} last{/if}">
                <div class="center capital p5">
                    <a href="{$jamroom_url}/{$m_admin.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$m_admin.session_user_id size="xsmall" crop="auto" class="iloutline" alt=$m_admin.session_user_name title=$m_admin.session_user_name}</a>
                </div>
            </div>
        {/foreach}
    </div>
{/if}
{if isset($admin)}
    <div class="row">
        <div class="col12 last">
            <hr>
            <span class="media_title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="8" default="Site"} {jrCore_lang  skin=$_conf.jrCore_active_skin id="57" default="Admins"}</span>
            <hr>
        </div>
    </div>
    <div class="row">
        {foreach from=$admin item="s_admin"}
            <div class="col3{if $s_admin@last} last{/if}">
                <div class="center capital p5">
                    <a href="{$jamroom_url}/{$s_admin.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$s_admin.session_user_id size="xsmall" crop="auto" class="iloutline" alt=$s_admin.session_user_name title=$s_admin.session_user_name}</a>
                </div>
            </div>
        {/foreach}
    </div>
{/if}
{if isset($user)}
    <div class="row">
        <div class="col12 last">
            <hr>
            <span class="media_title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="58" default="Members"}</span>
            <hr>
        </div>
    </div>
    <div class="row">
        {foreach from=$user item="member"}
            <div class="col3{if $member@last} last{/if}">
                <div class="center">
                    <a href="{$jamroom_url}/{$member.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$member.session_user_id size="xsmall" crop="auto" class="iloutline" alt=$member.session_user_name title=$member.session_user_name}</a>
                </div>
            </div>
        {/foreach}
    </div>
{/if}
</div>

<hr>

<div class="p5" style="width:90%;display:table;margin:0 auto;">
    <div style="display:table-row;">
        <div class="media_title" style="display:table-cell;">
            {jrCore_lang  skin=$_conf.jrCore_active_skin id="56" default="Master"} {jrCore_lang  skin=$_conf.jrCore_active_skin id="57" default="Admins"}:
        </div>
        <div style="width:5%;display:table-cell;text-align:right;">
            {$master_count}
        </div>
    </div>
    <div style="display:table-row;">
        <div class="media_title" style="display:table-cell;">
            {jrCore_lang  skin=$_conf.jrCore_active_skin id="8" default="Site"} {jrCore_lang  skin=$_conf.jrCore_active_skin id="57" default="Admins"}:
        </div>
        <div style="width:5%;display:table-cell;text-align:right;">
            {$admin_count}
        </div>
    </div>
    <div style="display:table-row;">
        <div class="media_title" style="display:table-cell;">
            {jrCore_lang  skin=$_conf.jrCore_active_skin id="58" default="Members"}:
        </div>
        <div style="width:5%;display:table-cell;text-align:right;">
            {$user_count}
        </div>
    </div>
    <div style="display:table-row;">
        <div class="media_title" style="display:table-cell;">
            {jrCore_lang  skin=$_conf.jrCore_active_skin id="59" default="Visitors"}:
        </div>
        <div style="width:5%;display:table-cell;text-align:right;">
            {$visitor_count}
        </div>
    </div>

</div>

<hr>

<div class="p5" style="width:90%;display:table;margin:0 auto;">

    <div style="display:table-row">

        <div class="media_title" style="display:table-cell;">
        {jrCore_lang  skin=$_conf.jrCore_active_skin id="60" default="Total"}:
        </div>
        <div style="width:5%;display:table-cell;text-align:right;">
        {$all_count}
        </div>

    </div>

</div>
