<div class="box">
    {jrMaestro_sort template="icons.tpl" nav_mode="menu" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            <ul class="tm">
                <li>
                    <a href="{$jamroom_url}/{$profile_url}/{$murl}">
                        {if !isset($_post.profile_actions) ||  strlen($_post.profile_actions) == 0}
                            <strong>{jrCore_lang module="jrAction" id="4" default="Timeline"}</strong>
                        {else}
                            {jrCore_lang module="jrAction" id="4" default="Timeline"}
                        {/if}
                    </a>
                </li>
                <li>
                    <a href="{$jamroom_url}/{$profile_url}/{$murl}/mentions">
                        {if isset($_post.profile_actions) && $_post.profile_actions == 'mentions'}
                            <strong>{jrCore_lang module="jrAction" id="7" default="Mentions"}</strong>
                        {else}
                            {jrCore_lang module="jrAction" id="7" default="Mentions"}
                        {/if}
                    </a>
                </li>
                <li>
                    <a href="{$jamroom_url}/{$profile_url}/{$murl}/feedback">
                        {if isset($_post.profile_actions) && $_post.profile_actions == 'feedback'}
                            <strong>{jrCore_lang skin="jrMaestro" id=120 default="Feedback"}</strong>
                        {else}
                            {jrCore_lang skin="jrMaestro" id=120 default="Feedback"}
                        {/if}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>