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
            <th>Kassa</th>
            <th>Operator</th>
            <th>Mədaxil</th>
            <th>Məxaric</th>
            <th>Qaimə №</th>
            <th>Tarix</th>
            <th>Balans (USD)</th>
            <th>Balans (AZN)</th>
        </tr>
        {assign var="i" value=0}
        {foreach from=$invoices item=invoice}
            {assign var="i" value=$i+1}
            <tr>
                <td>{$i}</td>
                <td>{$invoice.cashbox_name}</td>
                <td>{$invoice.name}</td>
                <td>{if $invoice.operation_type == '+'}+{$invoice.amount}{else}0{/if}</td>
                <td>{if $invoice.operation_type == '-'}-{$invoice.amount}{else}0{/if}</td>
                <td>{$invoice.serial}</td>
                <td>{$invoice.date|substr:0:10}</td>
                <td>{if $invoice.currency}{$invoice.total_amount}{/if}</td>
                <td>{if !$invoice.currency}{$invoice.total_amount}{/if}</td>
            </tr>
        {/foreach}
    </table>

</div>
<script>
    window.print();
</script>
</body>
</html>
