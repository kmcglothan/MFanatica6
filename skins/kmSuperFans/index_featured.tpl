{if $_conf.kmSuperFans_list_1_active != 'off'}
    <section class="featured">
        <div class="row">
            <div class="col12">
                <div class="center">
                    <h1>{jrCore_lang skin="kmSuperFans" id=56 default="We have the world's best music from the world's coolest community"}</h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col12">
                <div class="head">
                    {jrCore_icon icon="audio" size="20" color="ff5500"} <span>{jrCore_lang skin="kmSuperFans" id=48 default="On Sale Now"}</span>
                </div>
                <div class="list">
                    {if $_conf.kmSuperFans_require_price_1 == 'on'}
                        {$s1 = "audio_file_item_price > 0"}
                    {/if}
                    {if strlen($_conf.kmSuperFans_list_1_ids) > 0}
                        {jrCore_list module="jrAudio" search="_item_id in `$_conf.kmSuperFans_list_1_ids`" limit="8" template="index_item_1.tpl"}
                    {elseif jrCore_module_is_active('jrCombinedAudio') && $_conf.kmSuperFans_list_1_soundcloud == 'on'}
                        {jrCombinedAudio_get_active_modules assign="mods"}
                        {if strlen($mods) > 0}
                            {jrSeamless_list modules=$mods  search=$s1 order_by="_created desc" limit="8" template="index_item_1.tpl"}
                        {elseif jrUser_is_admin()}
                            No active audio modules found!
                        {/if}
                    {else}
                        {jrCore_list module="jrAudio" search=$s1 limit="8" template="index_item_1.tpl" require_image="audio_image"}
                    {/if}
                </div>
            </div>
        </div>
    </section>
{/if}

{if $_conf.kmSuperFans_list_2_active != 'off'}
    <section class="featured dark">
        <div class="row">
            <div class="col12">
                <div class="head">
                    {jrCore_icon icon="star" size="20" color="ff5500"} <span>{jrCore_lang skin="kmSuperFans" id=49 default="Featured Artists"}</span>
                </div>
                <div class="col12">
                    {if strlen($_conf.kmSuperFans_list_2_ids) > 0}
                        {jrCore_list module="jrProfile" search="_item_id in `$_conf.kmSuperFans_list_2_ids`" limit="7" template="index_item_2.tpl"}
                    {else}
                        {jrCore_list module="jrProfile" order_by="profile_jrAudio_item_count numerical_desc" limit="7" template="index_item_2.tpl"}
                    {/if}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col12">
                <div class="center register">
                    <br>
                    {jrCore_lang skin="kmSuperFans" id=52 default="Join us today and start creating."} <button class="form_button" onclick="jrCore_window_location('{$jamroom_url}/{jrCore_module_url module="jrUser"}/signup')">Register</button>
                </div>
            </div>
        </div>
    </section>
{/if}


{if $_conf.kmSuperFans_list_3_active != 'off'}

    <section class="featured">
        <div class="row">
            <div class="col12">
                <div class="head">
                    {jrCore_icon icon="stats" size="20" color="ff5500"} <span> {$_conf.kmSuperFans_chart_days} {jrCore_lang skin="kmSuperFans" id=50 default="Day Charts"}</span>
                </div>
                <div class="index_chart">
                    <div class="list_item">
                        <div class="table center">
                            <div class="table-row">
                                <div class="table-cell" style="width: 50px;">
                                    #
                                </div>
                                <div class="table-cell" style="width: 50px;">
                                    dir
                                </div>
                                <div class="table-cell desk" style="width: 50px;">
                                    last
                                </div>
                                <div class="table-cell desk" style="width: 50px">
                                    &nbsp;
                                </div>
                                <div class="table-cell desk" style="width: 42px;">
                                    &nbsp;
                                </div>
                                <div class="table-cell">
                                    {jrCore_lang skin="kmSuperFans" id="30" default="Title"}
                                </div>
                                <div class="table-cell desk" style="width: 220px">
                                    {jrCore_lang skin="kmSuperFans" id="31" default="Artist"}
                                </div>

                                <div class="table-cell desk" style="width: 120px;">
                                    Genre
                                </div>
                                <div class="table-cell desk" style="width: 80px;">
                                    {jrCore_lang skin="kmSuperFans" id="58" default="Plays"}
                                </div>
                                <div class="table-cell chart_buttons" style="width:150px; text-align: right; position: relative;">
                                    {jrCore_module_url module="jrImage" assign="iurl"}
                                    <div id="chartLoader" class="p10" style="display:none"><img src="{$jamroom_url}/skins/kmSuperFans/img/ajax-loader.gif" alt="{$working|jrCore_entity_string}"></div>
                                    <select class="form_select" id="chart_days" onchange="kmSuperFans_chart_days(this.value)">
                                        <option {if $_conf.kmSuperFans_chart_days == '1'}selected="selected"{/if} value="1">1 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                        <option {if $_conf.kmSuperFans_chart_days == '7'}selected="selected"{/if} value="7">7 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                        <option {if $_conf.kmSuperFans_chart_days == '14'}selected="selected"{/if} value="14">14 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                        <option {if $_conf.kmSuperFans_chart_days == '30'}selected="selected"{/if} value="30">30 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                        <option {if $_conf.kmSuperFans_chart_days == '90'}selected="selected"{/if} value="90">90 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                        <option {if $_conf.kmSuperFans_chart_days == '365'}selected="selected"{/if} value="365">365 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list" id="chart">
                        {if $_conf.kmSuperFans_require_price_3 == 'on'}
                            {$s2 = "audio_file_item_price > 0"}
                        {/if}
                        {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" search=$s2 chart_days=$_conf.kmSuperFans_chart_days limit="17" template="index_chart_item.tpl"}
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}