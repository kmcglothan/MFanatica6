{if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
    <li class="left">
        <a onclick="jrPayment_view_cart()">
            {if $item_count > 0}
                <div id="payment-view-cart-button">{jrCore_lang module="jrPayment" id=6 default="cart"} <span>({$item_count})</span></div>
            {else}
                <div id="payment-view-cart-button">{jrCore_lang module="jrPayment" id=6 default="cart"} <span></span></div>
            {/if}
        </a>
    </li>
{else}
    <li class="desk right">
        <a onclick="jrPayment_view_cart()">
            {if $item_count > 0}
                <div id="payment-view-cart-button">{jrCore_lang module="jrPayment" id=6 default="cart" assign="ct"}{jrCore_image image="cart44.png" alt=$ct} <span>({$item_count})</span></div>
            {else}
                <div id="payment-view-cart-button">{jrCore_lang module="jrPayment" id=6 default="cart" assign="ct"}{jrCore_image image="cart44.png" alt=$ct} <span></span></div>
            {/if}
        </a>
    </li>
{/if}