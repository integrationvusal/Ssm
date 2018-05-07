<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Ticket</title>
        {literal}
		<style>
			
		</style>
        {/literal}
    </head>
    <body>
		Sifariş № {$order.order->id->value};
		<br/>
		Sifarişçi: {$order.order->name->value};
		<br/>
		Ünvan: {$order.order->address->value};
		<br/>
		Məhsullar:
		<table border="1" cellpadding="0" cellspacing="0">
			<tr>
				<td style="padding: 5px;">
					Məhsulun adı
				</td>
				<td style="padding: 5px;">
					Məhsulun qiyməti
				</td>
				<td style="padding: 5px;">
					Sayı
				</td>
			</tr>
			{foreach from=$order.product item=p}
				<tr>
					<td style="padding: 5px;">
						{$p->productTitle->value}
					</td>
					<td style="text-align: right; padding: 5px;">
						{$p->price->value} AZN
					</td>
					<td style="padding: 5px;">
						{$p->count}
					</td>
				</tr>
			{/foreach}
		</table>
	</body>
</html>