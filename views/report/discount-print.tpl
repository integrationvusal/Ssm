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
            <th>Kartın nömrəsi</th>
            <th>Qaimə</th>
            <th>Müştəri</th>
            <th>Kartın növü</th>
            <th>Əməliyyat</th>
            <th>Əvvəlki bonus</th>
            <th>Yeni bonus</th>
            <th>Ümumi məbləğ</th>
            <th>Endirim məbləği</th>
            <th>Qalıq</th>
            <th>Tarix</th>
        </tr>
        {assign var="i" value=0}
        {foreach from=$histories item=history}
            {assign var="i" value=$i+1}
            <tr>

                <td>{$i}</td>
                <td>{$history.card_number}</td>
                <th><a href="javascript:void(0)"
                       class="invoice-details"
                       data-invoice-id="{$history.invoice_id}"
                       data-invoice-type="{$history.invoice_type}">{$history.invoice_serial}</a></th>
                <td>{$history.card_user}</td>
                <td>{$discount_card_types[$history.card_type]}</td>
                {if $history.operation_type == '0'}
                    <td>Ləğv</td>
                {else}
                    <td>{$history.operation_type}</td>
                {/if}
                <td>{$history.previous_discount}</td>
                <td>{$history.current_discount}</td>
                <td>{$history.total_amount}</td>
                <td>{$history.discounted_amount}</td>
                <td>{$history.remaining_amount}</td>
                <td>{$history.created_at}</td>

            </tr>
        {/foreach}
    </table>

</div>
<script>
    window.print();
</script>
</body>
</html>
