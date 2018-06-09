{$page_javascript}
<div class="block">
    <div class="title">
        <h1>{jrCore_lang module="jrSubscribe" id="28" default="subscriptions"}</h1>
    </div>
    <div class="block_content">
        <div class="item">
            <div class="container">
                {if isset($_plans)}
                    {$sym = jrPayment_get_currency_code()}
                    {foreach $_plans as $k => $item}
                        {if $item@first || ($item@iteration % 3) == 1}
                            <div class="row">
                        {/if}
                        <div class="col4{if $item@last || ($item@iteration % 3) == 0} last{/if}">
                            <div class="sub-plan-box">
                                <div class="sub-header">
                                    <h2>{$item.sub_title}</h2>
                                </div>
                                {if isset($item.sub_features) && strlen($item.sub_features) > 0}
                                    <div class="sub-features">
                                        {$item.sub_features}
                                    </div>
                                {/if}
                                <div class="sub-price">
                                    <h4>
                                        {$sym}{$item.sub_item_price} / {jrSubscribe_get_sub_duration_string($item.sub_duration)}
                                        {if isset($item.sub_trial) && $item.sub_trial > 0}
                                            &nbsp;-&nbsp;
                                            {jrSubscribe_convert_interval_to_days($item.sub_trial)} {jrCore_lang module="jrSubscribe" id="3" default="Day"} {jrCore_lang module="jrSubscribe" id="48" default="Free"} {jrCore_lang module="jrSubscribe" id="23" default="Trial"}
                                        {/if}
                                    </h4>
                                </div>
                                <div class="p10 center">
                                    {jrSubscribe_get_subscription_button plan_id=$item._item_id}
                                </div>
                            </div>
                        </div>
                        {if $item@last || ($item@iteration % 3) == 0}
                            </div>
                        {/if}
                    {/foreach}
                {/if}
            </div>
        </div>
    </div>
</div>
