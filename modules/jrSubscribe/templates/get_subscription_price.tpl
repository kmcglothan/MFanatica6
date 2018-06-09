<div id="sub-price-modal" class="item" style="display:none">
    <div id="sub-price-box">

        <span id="sub-price-message">{jrCore_lang module="jrSubscribe" id=50 default="Enter the amount you would like to pay for this subscription:"}</span>
        <br>
        <div id="sub-price-error" style="display:none"></div>

        {jrCore_lang module="jrSubscribe" id=51 default="Save Price" assign="save"}
        <input id="sub-price-text" type="text" class="form_text" placeholder="" onkeypress="if(event && event.keyCode == 13) { jrSubscribe_save_price(); }">&nbsp;<input type="button" value="{$save|jrCore_entity_string}" class="form_button sub-price-button" onclick="jrSubscribe_save_price();">
        <input id="sub-currency" type="hidden" value="0">
        <input id="sub-plan-id" type="hidden" value="0">

        <div class="clear"></div>
        <div class="simplemodal-close" style="position:absolute;right:10px;bottom:20px">{jrCore_icon icon="close" size="16"}</div>

    </div>
</div>

