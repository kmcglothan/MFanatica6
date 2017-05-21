{* this is for use in the  'remote template url' field of the checkout templates here https://admin.foxycart.com/admin.php
put the url http://yoursite.com/foxycart/remote_templates/checkout to import this checkout template. *}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$_conf.jrCore_system_name} Checkout</title>
    <link rel="stylesheet" href="https://^^store_domain^^/themes/standard/styles.css" type="text/css" media="screen" charset="utf-8"/>
</head>

<body id="checkout">
<div id="pageContainer" style="width:760px; margin:0 auto;">
    ^^cart^^
    ^^checkout^^
</div>

{* shipping calculation for jrStore *}
<script type="text/javascript" charset="utf-8">
    //<![CDATA[
    jQuery(document).ready(function () {
        calculate_shipping();
        $("select").change(function () {
            calculate_shipping();
        });
    });


    function calculate_shipping() {
        var canShip = true;
        var shipping = 0;
        var country_code = (jQuery("#use_different_addresses").is(":checked") ? $("#shipping_country").val() : $("#customer_country").val());

        //onchange bring back in the shipping options
        jQuery('.fc_cart_item_international_shipping').fadeIn();
        jQuery('.fc_cart_item_domestic_shipping').fadeIn();

        //loop through the products in the cart
        for (var p in fc_json.products) {
            if (fc_json.products[p].options["ships from"] == country_code) {
                shipping += (fc_json.products[0].options["domestic shipping"] * fc_json.products[p].quantity);
                //hide international shipping price
                jQuery('#product_' + fc_json.products[p].id + ' .fc_cart_item_international_shipping').fadeOut();
            } else {
                shipping += (fc_json.products[0].options["international shipping"] * fc_json.products[p].quantity);
                //hide domestic shipping price
                jQuery('#product_' + fc_json.products[p].id + ' .fc_cart_item_domestic_shipping').fadeOut();
            }
        }

        if (canShip) {
            // Perform tasks here when shipping is possible
            FC.checkout.config.orderFlatRateShipping = shipping;
            FC.checkout.updateShipping(-1);
        }
    }

    //]]>
</script>

</body>
</html>
