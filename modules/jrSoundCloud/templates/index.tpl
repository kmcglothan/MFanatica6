{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="soundcloud_title,soundcloud_description"}
        <h1>{jrCore_lang module="jrSoundCloud" id=53 default="SoundCloud"}</h1>
    </div>

    <div class="block_content">

        {jrCore_list module="jrSoundCloud" order_by="_created numerical_desc" pagebreak=10 page=$_post.p pager=true}

    </div>

</div>

{jrCore_include template="footer.tpl"}
