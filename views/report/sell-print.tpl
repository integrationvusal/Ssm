<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SSM</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<div style="width: 1000px; margin: 0 auto;">

    <table class="table table-bordered" style="width:100%;">
        <tr>
            <td>#</td>
            <td>Operator</td>
            <td>Ad</td>
            <td>Kod</td>
            <td>Barkod</td>
            <td>Sayı</td>
            <td>Qaimə №</td>
            <td>Satış (USD)</td>
            <td>Satış (AZN)</td>
            <td>Cəmi satış (USD)</td>
            <td>Cəmi satış (AZN)</td>
            <td style="min-width: 90px">Tarix</td>
        </tr>
        {assign var=total_count value=0}
        {assign var=total_buy value=0}
        {assign var=total_sell value=0}
        {assign var=total_sell_azn value=0}
        {assign var="i" value=0}
        {foreach from=$invoices item=invoice}
            {assign var="i" value=$i+1}
            <tr>

                <td>{$i}</td>
                <td>{if empty($invoice.operator_name)}{$user.name}{else}{$invoice.operator_name}{/if}</td>
                <td>{$invoice.short_info}</td>
                <td>{$invoice.goods_code}</td>
                <td>{$invoice.barcode}</td>
                <td>{$invoice.count}</td>
                <td>{$invoice.serial}</td>
                <!--<td>{$invoice.buy_price}</td>-->
                <td>{if $invoice.currency}{$invoice.sell_price}{/if}</td>
                <td>{if !$invoice.currency}{$invoice.sell_price}{/if}</td>
                <!--<td>{$invoice.buy_price * $invoice.count}</td>-->
                <td>{if $invoice.currency}{$invoice.sell_price * $invoice.count}{/if}</td>
                <td>{if !$invoice.currency}{$invoice.sell_price * $invoice.count}{/if}</td>

                {assign var=total_count value=$total_count + $invoice.count}
                {assign var=total_buy value=$total_buy + ($invoice.buy_price * $invoice.count)}

                {if $invoice.currency}
                    {$total_sell=$total_sell + ($invoice.sell_price * $invoice.count)}
                {else}
                    {$total_sell_azn=$total_sell_azn + ($invoice.sell_price * $invoice.count)}
                {/if}

                <td>{$invoice.date|substr:0:10}</td>
            </tr>
        {/foreach}
        <tr>
            <td colspan="5">Göstərilənlərin cəmi:</td>
            <td>{$total_count}</td>
            <td></td>
            <td></td>
            <td></td>
            <!--<td>{$total_buy}</td>-->
            <td>{$total_sell}</td>
            <td>{$total_sell_azn}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="5">Cəmi:</td>
            <td>{$summary.count}</td>
            <td></td>
            <td></td>
            <td></td>
            <!--<td>{$summary.total_buy_price}</td>-->
            <td>{$summary.total_sell_price}</td>
            <td>{$summary.total_sell_price_azn}</td>
            <td></td>
        </tr>
    </table>

</div>
<script>
    window.print();
</script>
</body>
</html>
