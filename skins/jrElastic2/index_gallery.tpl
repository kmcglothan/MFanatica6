{if jrCore_module_is_active('jrGallery')}
    <section class="white">
        <div class="row">
            <div class="col12">
                <div class="head center">
                    <h1>{jrCore_lang skin="jrElastic2" id=11 default="Latest Gallery Images"}</h1>
                </div>
                <br>
                <div class="list">
                    {$limit = 24}
                    {if jrCore_is_mobile_device()}
                        {$limit = 8}
                    {elseif jrCore_is_tablet_device()}
                        {$limit = 16}
                    {/if}
                    {jrCore_list module="jrGallery" order_by="_created desc" limit=$limit template="index_gallery_list.tpl"}
                </div>
            </div>
        </div>
    </section>
{/if}
