{jrCore_lang module="jrAudio" id="41" default="Audio" assign="page_title"}
{jrCore_module_url module="jrAudio" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="audio_index">
    <div class="wrap">
        <div class="row">
            <div class="col6">
                <h1>{jrCore_lang module="jrAudio" id=41 default="Audios"}</h1>
            </div>

            <div class="col6">
                {jrSearch_module_form fields="audio_title,audio_text,audio_genrey,audio_album"} {jrCore_list module="jrAudio" group_by="audio_genre" order_by="audio_genre asc" limit=100 template="select_genre.tpl"}
            </div>
        </div>
        <div class="row">
            {if strlen($_post.genre) > 0}
                {$s1 = "audio_genre = `$_post.genre`"}
            {/if}
            {jrCore_list module="jrAudio" search=$s1 order_by="_created numerical_desc" pagebreak=32 page=$_post.p pager=true template="index_item_1.tpl" require_image="audio_image"}
        </div>
        <br>
    </div>
</div>

{jrCore_include template="footer.tpl"}

