Your site has sold an item:

item: {$item_name}
buyer: {$_buyer.user_name}

Details on the transaction can be found at the following URLs:
{jrCore_module_url module="jrFoxyCart" assign="murl"}
{$jamroom_url}/{$murl}/txn_details/{$txn_id}

