{if jrCore_module_is_active('jrVideo')}
    <section class="white videos">
        <div class="row">
            <div class="col12">
                <div class="head center">
                    <h1>{jrCore_lang skin="jrElastic2" id=26 default="Hot Videos"}</h1>
                </div>
                <br>
                <div class="list">
                    {$limit = 24}
                    {if jrCore_is_mobile_device()}
                        {$limit = 8}
                    {elseif jrCore_is_tablet_device()}
                        {$limit = 16}
                    {/if}
                    {jrCore_list module="jrVideo" chart_days="10" chart_field="video_file_strem_count" limit=$limit template="index_video_list.tpl"}
                </div>
            </div>
        </div>
    </section>
{/if}