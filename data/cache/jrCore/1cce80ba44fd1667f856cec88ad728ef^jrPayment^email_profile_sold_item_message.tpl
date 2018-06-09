Congratulations - you have sold items!

Items:
{foreach $_items as $item}
{$item.cart_module_url} - {$item.item_name}
{/foreach}
Buyer:
{$_buyer.user_name} (@{$_buyer.profile_name})

You can view the transaction online for more details:

{jrCore_module_url module="jrPayment" assign="murl"}
{$jamroom_url}/{$_items[0].profile_url}/{$murl}/payments
