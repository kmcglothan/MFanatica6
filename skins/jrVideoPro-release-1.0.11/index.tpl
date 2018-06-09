{* /////////// DO NOT REMOVE //////////  *}
{assign var="page_template" value="index"}
{* /////////// DO NOT REMOVE //////////  *}

{jrCore_include template="header.tpl"}
<div class="index">
    {jrCore_include template="index_slides.tpl"}
    {jrCore_module_url module="jrVideo" assign="murl"}
    <div class="pad">
        <br>
        <div class="row">
            <div class="head">
                <span><a href="{$jamroom_url}/{$murl}/new-releases">{jrCore_lang skin="jrVideoPro" id=50 default="New Releases"}</a> </span>
            </div>
        </div>
        <section>
            <div class="row">
                <div class="index_list clearfix page_1">
                    <div>
                        {jrCore_list
                        module="jrVideo"
                        order_by="_item_id numerical_desc"
                        limit="18"
                        template="index_item_1.tpl"
                        require_image="video_image"
                        }
                    </div>
                </div>
            </div>
            <a class="list_nav previous"></a>
            <a class="list_nav next"></a>
        </section>
        <div class="row">
            <div class="head">
                <span><a href="{$jamroom_url}/{$murl}/staff-picks">{jrCore_lang skin="jrVideoPro" id=49 default="Staff Picks"}</a> </span>
            </div>
        </div>
        <section>
            <div class="row">
                <div class="index_list clearfix c27 page_1">
                    <div>
                        {if strlen($_conf.jrVideoPro_staff_picks) > 0}
                            {jrCore_list
                            module="jrVideo"
                            limit="27"
                            search="_item_id in `$_conf.jrVideoPro_staff_picks`"
                            template="index_item_2.tpl"
                            require_image="video_image"
                            }
                        {else}
                            {jrCore_list
                            module="jrVideo"
                            limit="27"
                            order_by="video_file_stream_count numerical_desc"
                            template="index_item_2.tpl"
                            require_image="video_image"
                            }
                        {/if}
                    </div>
                </div>
            </div>
            <a class="list_nav previous"></a>
            <a class="list_nav next"></a>
        </section>
        <div class="row">
            <div class="head">
                <span><a href="{$jamroom_url}/{$murl}/top-rated">{jrCore_lang skin="jrVideoPro" id=51 default="Top Rated"}</a> </span>
            </div>
        </div>
        <section>
            <div class="row">
                <div class="index_list clearfix page_1">
                    <div>{jrCore_list
                        module="jrVideo"
                        limit="18"
                        order_by="video_rating_overall_average_count numerical_desc"
                        template="index_item_4.tpl"
                        require_image="video_image"
                        }
                    </div>
                </div>
            </div>
            <a class="list_nav previous"></a>
            <a class="list_nav next"></a>
        </section>
        <div class="row">
            <div class="head">
                <span><a href="{$jamroom_url}/{$murl}/most-watched">{jrCore_lang skin="jrVideoPro" id=52 default="Most Watched"}</a></span>
            </div>
        </div>
        <section>
            <div class="row">
                <div class="index_list clearfix c21 page_1">
                    <div>{jrCore_list
                        module="jrVideo"
                        chart_days=$_conf.jrVideoPro_watched_days
                        chart_field="video_file_stream_count"
                        limit="21"
                        template="index_item_3.tpl"
                        require_image="video_image"
                        }
                    </div>
                </div>
            </div>
            <a class="list_nav previous"></a>
            <a class="list_nav next"></a>
        </section>
        <div class="row">
            <div class="head">
                <span><a href="{$jamroom_url}/{$murl}/new-series">{jrCore_lang skin="jrVideoPro" id=53 default="New Series"}</a></span>
            </div>
        </div>
        <section>
            <div class="row">
                {if $_conf.jrVideoPro_list_1_active != 'off'}
                    <div class="index_list clearfix c27 page_1">
                        <div>{jrCore_list
                            module="jrVideo"
                            order_by="_item_id numerical_desc"
                            limit="27"
                            group_by="video_album"
                            template="index_item_2.tpl"
                            require_image="video_image"
                            }
                        </div>
                    </div>
                {/if}
            </div>
            <a class="list_nav previous"></a>
            <a class="list_nav next"></a>
        </section>
        <div class="row">
            <div class="head">
                <span><a href="{$jamroom_url}/{$murl}/category=comedy">{jrCore_lang skin="jrVideoPro" id=48 default="Comedy"}</a></span>
            </div>
        </div>
        <section>
            <div class="row">
                <div class="index_list clearfix page_1" style="height: 225px">
                    <div>{jrCore_list
                        module="jrVideo"
                        order_by="_item_id numerical_desc"
                        limit="18"
                        search="video_category = comedy"
                        template="index_item_4.tpl"
                        require_image="video_image"
                        }
                    </div>
                </div>
            </div>
            <a class="list_nav previous"></a>
            <a class="list_nav next"></a>
        </section>
        <div class="row">
            <div class="head">
                <span><a href="{$jamroom_url}/{$murl}/category=drama">{jrCore_lang skin="jrVideoPro" id=54 default="Drama"}</a></span>
            </div>
        </div>
        <section>
            <div class="row">
                <div class="index_list clearfix page_1">
                    <div>{jrCore_list
                        module="jrVideo"
                        order_by="_item_id numerical_desc"
                        limit="18"
                        search="video_category = drama"
                        template="index_item_4.tpl"
                        require_image="video_image"
                        }
                    </div>
                </div>
            </div>
            <a class="list_nav previous"></a>
            <a class="list_nav next"></a>
        </section>
    </div>
</div>

{jrCore_include template="footer.tpl"}

