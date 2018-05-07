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
            <th>#</th>
            <th>Obyekt</th>
            <th>Operator</th>
            <th>Xərcin adı</th>
            <th>Qaimə №</th>
            <th>Sayı</th>
            <th>Məbləğ (USD)</th>
            <th>Məbləğ (AZN)</th>
            <th>Tarix</th>
        </tr>
        {assign var=total_count value=0}
        {assign var=total_sell value=0}
        {assign var=total_sell_azn value=0}
        {assign var="i" value=0}
        {foreach from=$invoices item=invoice}
            {assign var="i" value=$i+1}
            <tr>

                <td>{$i}</td>
                <td>{$invoice.subject_name}</td>
                <td>{if empty($invoice.name)}{$user.name}{else}{$invoice.name}{/if}</td>
                <td>{$invoice.short_info}</td>
                <td>{$invoice.serial}</td>
                <td>{$invoice.count}</td>

                <td>{if $invoice.currency}{$invoice.amount}{/if}</td>
                <td>{if !$invoice.currency}{$invoice.amount}{/if}</td>
                {if $invoice.currency}
                    {$total_sell = $total_sell + ($invoice.buy_price * $invoice.count)}
                {else}
                    {$total_sell_azn = $total_sell_azn + ($invoice.buy_price * $invoice.count)}
                {/if}

                {$total_count = $total_count + $invoice.count}

                <td>{$invoice.date|substr:0:10}</td>
            </tr>
        {/foreach}
        <tr>
            <th colspan="5">Göstərilənlərin cəmi:</th>
            <th>{$total_count}</th>
            <th>{$total_sell|string_format:"%.2f"}</th>
            <th>{$total_sell_azn|string_format:"%.2f"}</th>
            <th></th>
        </tr>
        <tr>
            <th colspan="5">Bütün obyektlər üzrə cəmi:</th>
            <th>{$summary.count}</th>
            <th>{$summary.total_sell_price}</th>
            <th>{$summary.total_sell_price_azn}</th>
            <th></th>
        </tr>
    </table>

</div>
<script>
    window.print();
</script>
</body>
</html>
