Thank you for your {$_conf.jrCore_system_name} purchase!

You will find your downloadable items have been loaded into the "Your Purchases" section:
{jrCore_module_url module="jrPayment" assign="murl"}
{$jamroom_url}/{$murl}/purchases

{if $ship_notice === true}
You will be receiving a separate email with shipping instructions that also contain details on the progress of your order and how to contact the seller.
{/if}

Thank you for your purchase!
