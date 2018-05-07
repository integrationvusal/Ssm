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
    <table class="table table-bordered" border="1">
        <tr>
            <td>#</td>
            <td>Adı</td>
            <td>Qaimə №</td>
            <td>Borc (USD)</td>
            <td>Borc (AZN)</td>
            <td>Ödəniş (USD)</td>
            <td>Ödəniş (AZN)</td>
            <td>Qalıq (USD)</td>
            <td>Qalıq (AZN)</td>
            <td>Tarix</td>
        </tr>
        {assign var=total_debt value=0}
        {assign var=total_debt_azn value=0}
        {assign var=total_payed value=0}
        {assign var=total_payed_azn value=0}
        {assign var=i value=0}
        {foreach from=$invoices item=invoice}
            {assign var=i value=$i + 1}
            {if $invoice.type == 5}
                {assign var='amount' value = 0}
            {else}
                {assign var='amount' value = $invoice.amount}
            {/if}
            <tr>
                <td>{$i}</td>
                <td>{$invoice.name}</td>
                <td>{$invoice.serial}</td>

                <td>{if $invoice.currency}{$amount}{/if}</td>
                <td>{if !$invoice.currency}{$amount}{/if}</td>

                {if $invoice.currency}
                    {$total_debt=$total_debt + $amount}
                    {$total_payed=$total_payed + $invoice.payed}
                {else}
                    {$total_debt_azn=$total_debt_azn + $amount}
                    {$total_payed_azn=$total_payed_azn + $invoice.payed}
                {/if}

                <td>{if $invoice.currency}{$invoice.payed}{/if}</td>
                <td>{if !$invoice.currency}{$invoice.payed}{/if}</td>

                <td>{if $invoice.currency}{$invoice.total_amount + $amount - $invoice.payed}{/if}</td>
                <td>{if !$invoice.currency}{$invoice.total_amount + $amount - $invoice.payed}{/if}</td>

                <td>{$invoice.date|substr:0:10}</td>
            </tr>
        {/foreach}
        <tr>
            <td colspan="3">Göstərilənlərin cəmi:</td>
            <td>{$total_debt}</td>
            <td>{$total_debt_azn}</td>
            <td>{$total_payed}</td>
            <td>{$total_payed_azn}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3">Cəmi:</td>
            <td>{$summary.debt}</td>
            <td>{$summary.debt_azn}</td>
            <td>{$summary.payed}</td>
            <td>{$summary.payed_azn}</td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>
<script>
    window.print();
</script>
</body>
</html>
