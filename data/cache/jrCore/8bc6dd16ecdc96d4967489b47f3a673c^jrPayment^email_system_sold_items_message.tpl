Your site has sold items:

Items:
{foreach $_items as $item}
@{$item.profile_url} - {$item.cart_module_url} - {$item.item_name}
{/foreach}
Buyer:
{$_buyer.user_name} (@{$_buyer.profile_name})

Details on the transaction can be found at the following URL:
{jrCore_module_url module="jrPayment" assign="murl"}
{$jamroom_url}/{$murl}/txn_detail/{$_cart.txn_id}

