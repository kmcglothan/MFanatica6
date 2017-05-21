
{jrCore_lang skin="jrMSkin" id=113 default="Store" assign="page_title"}
{jrCore_module_url module="jrStore" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="fs">
    <div class="row">
        <div class="box">
            {if strlen($_post.cat) > 0}
                <div class="head">{jrMSkin_cat_title cat=$_post.cat}</div>
            {else}
                <div class="head">{jrCore_lang skin="jrMSkin" id="104" default="All Products"}</div>
            {/if}
            {jrSearch_module_form fields="product_title,product_body,product_category"}
            <input type="hidden" id="murl" value="{$murl}"/>
            <input type="hidden" id="target" value="#list"/>
            <input type="hidden" id="pagebreak" value="12"/>
            <input type="hidden" id="mod" value="jrAudio"/>
            <input type="hidden" id="profile_id" value="{$_profile_id}"/>
            <div class="box_body">
                <div class="wrap" style="padding: 0.5em">
                    <div id="list" class="clearfix animatedParent animateOnce">
                        {if strlen($_post.cat) > 0}
                            {jrCore_list
                            module="jrStore"
                            search="product_category_url = `$_post.cat`"
                            order_by="_item_id numerical_desc"
                            template="index_item_5.tpl"
                            pagebreak=20
                            page=$_post.p
                            pager=true}
                        {else}
                            {jrCore_list
                            module="jrStore"
                            order_by="_item_id numerical_desc"
                            template="index_item_5.tpl"
                            pagebreak=20
                            page=$_post.p
                            pager=true}
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{jrCore_include template="footer.tpl"}
