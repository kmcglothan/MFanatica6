{* default index for profile *}
{jrCore_module_url module="jrPlaylist" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMaestro_breadcrumbs module="jrPlaylist" profile_url=$profile_url profile_name=$profile_name page="detail"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrPlaylist" profile_id=$_profile_id}
    </div>
</div>


<div class="col8">
    <div class="box">
        {jrMaestro_sort template="icons.tpl" nav_mode="jrPlaylist" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap">
                <div id="list">
                    {jrCore_list module="jrPlaylist" profile_id=$_profile_id order_by="playlist_display_order numerical_asc" pagebreak="10" page=$_post.p pager=true}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col4 last">
    <div class="box">
        <ul id="actions_tab">
            <li class="solo" id="album_tab">
                <a href="#"></a>
            </li>
        </ul>
        <span>{jrCore_lang skin="jrMaestro" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrAudio"
                    order_by='_created RANDOM'
                    pagebreak=8
                    template="chart_playlist.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>