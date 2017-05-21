{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="playlist_title"}
        <h1>{jrCore_lang module="jrPlaylist" id="9" default="Playlist"}</h1>
    </div>

    <div class="block_content">
        {jrCore_list module="jrPlaylist" order_by="_created NUMERICAL_DESC" pagebreak="10" page=$_post.p pager=true}
    </div>

</div>

{jrCore_include template="footer.tpl"}

