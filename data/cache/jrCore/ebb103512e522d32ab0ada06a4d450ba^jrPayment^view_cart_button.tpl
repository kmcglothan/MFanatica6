<li>
<a onclick="jrPayment_view_cart()">
    {if $item_count > 0}
        <div id="payment-view-cart-button">{jrCore_lang module="jrPayment" id=6 default="cart"} <span>{$item_count}</span></div>
    {else}
        <div id="payment-view-cart-button">{jrCore_lang module="jrPayment" id=6 default="cart"} <span style="display:none"></span></div>
    {/if}
</a>
</li>
