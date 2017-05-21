{jrCore_module_url module="jrFoxyCart" assign="murl"}
{foreach $subscription->transaction_template->transaction_details->transaction_detail as $transaction_detail}
    <div class="details p5" style="max-width: 500px;">
        <table>
            <tr>
                <td>{jrCore_lang module="jrFoxyCart" id=99 default="Product Name"}</td>
                <td>{$transaction_detail->product_name}</td>
            </tr>
            <tr>
                <td>{jrCore_lang module="jrFoxyCart" id=100 default="Product Price"}</td>
                <td>{$transaction_detail->product_price}</td>
            </tr>
            <tr>
                <td>{jrCore_lang module="jrFoxyCart" id=91 default="Length"}</td>
                <td>{$subscription->frequency}</td>
            </tr>
        </table>
    </div>
{/foreach}