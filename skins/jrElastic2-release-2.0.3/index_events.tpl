{if jrCore_module_is_active('jrEvent')}
    <section class="dark calendar">
        <div class="overlay"></div>
        <div class="row">
            <div class="col12">
                <div class="head center">
                    <h1>{jrCore_lang skin="jrElastic2" id=10 default="upcoming events"}</h1>
                </div>
                <br>
                <div class="list">
                    {jrCore_list module="jrEvent" order_by="_created desc" limit="10" require_image="event_image" template="index_events_list.tpl"}
                </div>
            </div>
        </div>
    </section>
{/if}