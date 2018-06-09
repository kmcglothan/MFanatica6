<section class="index">
    <div class="overlay">

        <div class="top_stories">
            <div id="slides" class="page_1">
                <div class="slide_box">
                    {if $_conf.jrMogul_slide_1_active != 'off'}
                        <div class="slide" id="slide_1">
                            <h1>{$_conf.jrMogul_slide_1_headline}</h1>
                            <div class="buttons">
                                {jrCore_module_url module="jrUser" assign="uurl"}
                                <button class="login" onclick="jrCore_window_location('/{$uurl}/login')">{jrCore_lang skin="jrMogul" id=104 default="Start Listening"}</button>
                                <button class="signup" onclick="jrCore_window_location('/{$uurl}/signup')">{jrCore_lang skin="jrMogul" id=103 default="Start Creating"}</button>
                            </div>
                            <p>{$_conf.jrMogul_slide_1_text}</p>
                        </div>
                    {/if}
                    {if $_conf.jrMogul_slide_2_active != 'off'}
                        <div class="slide" id="slide_2">
                            <h1>{$_conf.jrMogul_slide_2_headline}</h1>
                            {jrCore_image image="people.png" width="486" height="238"}
                            <p>{$_conf.jrMogul_slide_2_text}</p>
                        </div>
                    {/if}
                    {if $_conf.jrMogul_slide_3_active != 'off'}
                        <div class="slide" id="slide_3">
                            <h1>{$_conf.jrMogul_slide_3_headline}</h1>
                            {jrCore_image image="rockstar.png" width="306" height="238"}
                            <p>{$_conf.jrMogul_slide_3_text}</p>
                        </div>
                    {/if}
                    {if $_conf.jrMogul_slide_4_active != 'off'}
                        <div class="slide" id="slide_4">
                            <h1>{$_conf.jrMogul_slide_4_headline}</h1>
                            {jrCore_image image="smartphone.png" width="340" height="238"}
                            <p>{$_conf.jrMogul_slide_4_text}</p>
                        </div>
                    {/if}
                    {if $_conf.jrMogul_slide_4_active != 'off'}
                        <div class="slide" id="slide_5">
                            <h1>{$_conf.jrMogul_slide_5_headline}</h1>
                            {jrCore_image image="headphones.png" width="238" height="238"}
                            <p>{$_conf.jrMogul_slide_5_text}</p>
                        </div>
                    {/if}
                </div>
                <span class="prev"><a href="#"></a></span>
                <span class="next"><a href="#"></a></span>
                <ul class="nav">
                    {if $_conf.jrMogul_slide_1_active != 'off'}
                        <li id="tab_1"><a href="#"></a></li>
                    {/if}
                    {if $_conf.jrMogul_slide_2_active != 'off'}
                        <li id="tab_2"><a href="#"></a></li>
                    {/if}
                    {if $_conf.jrMogul_slide_3_active != 'off'}
                        <li id="tab_3"><a href="#"></a></li>
                    {/if}
                    {if $_conf.jrMogul_slide_4_active != 'off'}
                        <li id="tab_4"><a href="#"></a></li>
                    {/if}
                    {if $_conf.jrMogul_slide_4_active != 'off'}
                        <li id="tab_5"><a href="#"></a></li>
                    {/if}
                </ul>
            </div>
        </div>

    </div>
    <div class="down">
        <a href="#"></a>
    </div>
</section>