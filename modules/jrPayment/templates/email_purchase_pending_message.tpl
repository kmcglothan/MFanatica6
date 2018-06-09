Thank you for your {$_conf.jrCore_system_name} purchase!

Currently your payment is "pending" - once payment clears you will receive notification that your items are available in the "Your Purchases" section:
{jrCore_module_url module="jrPayment" assign="murl"}
{$jamroom_url}/{$murl}/purchases

{if $ship_notice === true}
You will be receiving a separate email with shipping instructions that also contain details on the progress of your order and how to contact the seller.
{/if}

Thank you again for your purchase!
