{if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
    <li>
        <a onclick="jrPayment_view_cart()">
            {if $item_count > 0}
                {jrCore_lang skin=$_conf.jrCore_active_skin id="99" default="Items In Your Cart" assign="iic"}
                <div id="payment-view-cart-button" title="{$iic} - {$item_count}">{jrCore_lang module="jrPayment" id=6 default="cart"} <span title="{$iic} - {$item_count}">({$item_count})</span></div>
            {else}
                <div id="payment-view-cart-button"title="{$iic} - {$item_count}">{jrCore_lang module="jrPayment" id=6 default="cart"} <span></span></div>
            {/if}
        </a>
    </li>
{else}
    <li>
        <a onclick="jrPayment_view_cart()">
            {if $item_count > 0}
                {jrCore_lang skin=$_conf.jrCore_active_skin id="99" default="Items In Your Cart" assign="iic"}
                <div id="payment-view-cart-button">{jrCore_lang module="jrPayment" id=6 default="cart" assign="ct"}{jrCore_image image="cart.png" alt=$ct title="{$iic} - {$item_count}"} <span title="{$iic} - {$item_count}">({$item_count})</span></div>
            {else}
                <div id="payment-view-cart-button">{jrCore_lang module="jrPayment" id=6 default="cart" assign="ct"}{jrCore_image image="cart.png" alt=$ct} <span></span></div>
            {/if}
        </a>
    </li>
{/if}
