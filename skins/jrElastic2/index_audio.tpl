{if jrCore_module_is_active('jrAudio')}
    <section class="dark audio">
        <div class="overlay"></div>
        <div class="row">
            <div class="col12">
                <div class="head center">
                    <h1>{jrCore_lang skin="jrElastic2" id=4 default="Top 10 Tracks"}</h1>
                </div>
                <br>
                <div class="list">
                    {jrCore_list module="jrAudio" chart_days=30 chart_field="audio_file_stream_count" limit="10" require_image="audio_image" template="index_audio_list.tpl"}
                </div>
            </div>
        </div>
    </section>
{/if}