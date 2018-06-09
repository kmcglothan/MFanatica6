{jrCore_module_url module="jrAction" assign="murl"}
{jrCore_include template="header.tpl"}
{jrUser_home_profile_key key="_profile_id" assign="_profile_id"}
{jrUser_home_profile_key key="profile_name" assign="profile_name"}
{jrUser_home_profile_key key="profile_url" assign="profile_url"}

<section class="fs">
    <div class="row">
        {jrCore_include template="timeline_left.tpl"}
        <div class="col6">
            <div id="timeline" style="padding: 0 1em">
                {jrCore_include template="action_input.tpl"}

                {if strlen($_post.ss) > 0}
                    {jrCore_list module="jrAction" search="action_text like %`$_post.ss`%" order_by="_item_id numerical_desc" pagebreak=12 page=$_post.p pager=true}
                {else}
                    {jrCore_list module="jrAction" order_by="_item_id desc" simplepagebreak=12 include_followed=true page=$page_num pager=true pager_template="timeline_pager.tpl"}
                {/if}
                <div id="timeline_pagination_url" style="display:none">{$jamroom_url}/timeline_pagination/{$_profile_id}</div>
            </div>
        </div>
        <div class="col3">
            <div class="box">

                {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrSearch" profile_url=$profile_url single=true}

                <div class="box_body">
                    <div class="wrap">
                        <div class="media">
                            <div class="wrap clearfix">
                                <div id="site_search">
                                    {jrSearch_module_form fields="action_data"}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {if $_profile_id > 0}
                <div class="box">

                    {jrBeatSlinger_sort template="icons.tpl" nav_mode="stats" profile_url=$profile_url}

                    <div class="box_body">
                        <div class="wrap">
                            <div class="media">
                                <div class="wrap clearfix">
                                    {capture name="template" assign="stats_tpl"}
                                    {literal}
                                        {foreach $_stats as $title => $_stat}
                                        {jrCore_module_url module=$_stat.module assign="murl"}
                                        <div class="stat_entry_box">
                                            <a href="{$jamroom_url}/{$profile_url}/{$murl}"><span class="stat_entry_title">{$title}:</span> <span class="stat_entry_count">{$_stat.count|default:0}</span></a>
                                        </div>
                                        {/foreach}
                                    {/literal}
                                    {/capture}
                                    {jrProfile_stats profile_id=$_profile_id template=$stats_tpl}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</section>


{jrCore_include template="footer.tpl"}
