{jrCore_module_url module="jrGuestBook" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrISkin_breadcrumbs module="jrGuestBook" profile_url=$profile_url profile_name=$profile_name page="detail"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrGuestBook" profile_id=$_profile_id}
    </div>
</div>


<div class="box">
    {jrISkin_sort template="icons.tpl" nav_mode="jrGuestBook" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            <div class="block_content">

                <a id="guestbook_section" name="guestbook_section"></a>

                <div id="guestbook_success" class="item success" style="display:none; margin: 0 0 1em">
                    {jrCore_lang module="jrGuestBook" id="14" default="The guestbook entry was successfully created!"}
                </div>

                <div class="media" style="margin: 0 0 1em;">
                    <div class="wrap">
                        {if jrUser_is_logged_in()}

                            <div id="guestbook_notice" style="display:none;"><!-- any guestbook errors load here --></div>

                            <form id="gform" method="POST" onsubmit="jrGuestBook_post_entry('{$_profile_id}','#gform','#guestbooks');return false">
                                <input type="hidden" id="profile_id" name="profile_id" value="{$_profile_id}">
                                <textarea style="background: #f5fafa none repeat scroll 0 0;" name="guestbook_text" cols="40" rows="5" class="form_textarea" placeholder="Say something..."></textarea>
                                <br>
                                <div style="vertical-align:middle">
                                    <img id="form_submit_indicator" src="{$jamroom_url}/skins/jrISkin/img/submit.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}" style="margin:8px 8px 0px 8px;"><input id="guestbook_submit" type="submit" value="{jrCore_lang module="jrGuestBook" id="19" default="sign guest book"}" class="form_button" style="margin-top:8px;">
                                </div>
                            </form>

                        {else}

                            {jrCore_lang module="jrGuestBook" id="22" default="You must be logged in to post to this guestbook!"}

                        {/if}
                    </div>
                </div>

                <div id="guestbooks">
                    {jrCore_list module="jrGuestBook" search1="guestbook_owner_id = `$_profile_id`" order_by="_created numerical_desc" limit="250"}
                </div>

            </div>
        </div>
    </div>
</div>

