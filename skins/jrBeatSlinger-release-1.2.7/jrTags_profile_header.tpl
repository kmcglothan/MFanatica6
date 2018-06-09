{jrCore_module_url module="jrTags" assign="murl"}
<div class="page_nav">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrTags" profile_url=$profile_url page="index"}
    </div>
</div>

<div class="box">
    {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrTag" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            <div id="list">