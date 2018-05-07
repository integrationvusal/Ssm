<div id="order-tab-container">
	<div class="orders-container tab-container">
		<div class="tab-buttons-container">
			<div class="tab-button tab-button-active">Hamısı</div>
			{foreach from=$orders item=orderItem key=statusKey name=order_status_f}
				<div class="tab-button">{$ordersStatus[$statusKey]}</div>
			{/foreach}
			<div class="clear"></div>
		</div>
		<div class="tab-content">
			<table cellpadding="0" cellspacing="0" class="orders-table">
					<tr class="table-header">
						<td>
							{$messages.model_orders.field_name}
						</td>
						<td>
							{$messages.model_orders.field_phoneNumber}
						</td>
						<td>
							{$messages.model_orders.field_address}
						</td>
						<td>
							{$messages.model_orders.field_time}
						</td>
						<!--
						<td>
							{$messages.model_orders.field_ip}
						</td>
						-->
						<td>
							{$messages.model_orders.field_price}
						</td>
						<td>
							{$messages.model_orders.field_order_status}
						</td>
						<td>
							{*{$messages.model_orders.view_order}*}
						</td>
						<td>
							{*{$messages.model_orders.edit_order}*}
						</td>
						<td>
							
						</td>
						<td>
							
						</td>
					</tr>
			{foreach from=$orders item=orderItem key=statusKey name=order_status_f}
					{foreach from=$orderItem item=o}
					<tr>
						<td>{$o.order->name->value}</td>
						<td>{$o.order->phoneNumber->value}</td>
						<td>{$o.order->address->value}</td>
						<td>{$o.order->time->value}</td>
						<!--<td>{$o.order->ip->value}</td>-->
						<td style="text-align: right;">{$o.totalPrice} AZN</td>
						<td>{$ordersStatus[$o.order->orderStatus->value]}</td>
						<td>
							<a href="javascript:void(0)" class="view-order"><img src="{$static_url}/{$theme_folder}/img/view-icon-dark.png" /></a>
							<div class="ordered-products-container hide">
								<div class="block-title">Məhsullar</div>
								<div class="block-content">
									<div class="ordered-products">
										{foreach from=$o.products item=p}
											<div class="ordered-product">
												<div class="ordered-product-image"><img src="{$public_url}/{$p->image->value}" /></div>
												<div class="ordered-product-info">
													<div class="ordered-product-title">{$p->productTitle->value}</div>
													<div class="ordered-product-price">{$p->price->value} AZN</div>
													<div class="ordered-product-count">{$p->count} ədəd</div>
													<div class="clear"></div>
												</div>
												<div class="clear"></div>
											</div>
										{/foreach}
									</div>
								</div>
							</div>
						</td>
						<td>
							<a href="javascript:void(0)" class="new-window edit-order" have-parent="1" reload-parent="1" title="{$messages.interface_common.edit}" data-url="{$app_url}/{$admin_title}/edit/3/{$o.order->id->value}"><img src="{$static_url}/{$theme_folder}/img/edit-icon-dark.png" /></a>
						</td>
						<td>
							<a class="edit-order" href="{$app_url}/{$admin_title}/get_order_check/{$o.order->id->value}" target="_blank"><img src="{$static_url}/{$theme_folder}/img/print-icon-dark.png" /></a>
						</td>
						<td>
							<a class="delete-order edit-order" element-id="{$o.order->id->value}" href="javascript:void(0)"><img src="{$static_url}/{$theme_folder}/img/delete-icon-dark.png" /></a>
						</td>
					</tr>
				{/foreach}
		{/foreach}
		</table>
		</div>
		{foreach from=$orders item=orderItem key=statusKey name=order_status_f}
		<div class="tab-content hide">
			<table cellpadding="0" cellspacing="0" class="orders-table">
				<tr class="table-header">
					<td>
						{$messages.model_orders.field_name}
					</td>
					<td>
						{$messages.model_orders.field_phoneNumber}
					</td>
					<td>
						{$messages.model_orders.field_address}
					</td>
					<td>
						{$messages.model_orders.field_time}
					</td>
					<!--
					<td>
						{$messages.model_orders.field_ip}
					</td>
					-->
					<td>
						{$messages.model_orders.field_price}
					</td>
					{*<td>
						{$messages.model_orders.field_order_status}
					</td>*}
					<td>
						{*{$messages.model_orders.view_order}*}
					</td>
					<td>
						{*{$messages.model_orders.edit_order}*}
					</td>
					<td>
						
					</td>
					<td>
						
					</td>
				</tr>
					{foreach from=$orderItem item=o}
					<tr>
						<td>{$o.order->name->value}</td>
						<td>{$o.order->phoneNumber->value}</td>
						<td>{$o.order->address->value}</td>
						<td>{$o.order->time->value}</td>
						<!--<td>{$o.order->ip->value}</td>-->
						<td style="text-align: right;">{$o.totalPrice} AZN</td>
						{*<td>{$ordersStatus[$o.order->orderStatus->value]}</td>*}
						<td>
							<a href="javascript:void(0)" class="view-order"><img src="{$static_url}/{$theme_folder}/img/view-icon-dark.png" /></a>
							<div class="ordered-products-container hide">
								<div class="block-title">Məhsullar</div>
								<div class="block-content">
									<div class="ordered-products">
										{foreach from=$o.products item=p}
											<div class="ordered-product">
												<div class="ordered-product-image"><img src="{$public_url}/{$p->image->value}" /></div>
												<div class="ordered-product-info">
													<div class="ordered-product-title">{$p->productTitle->value}</div>
													<div class="ordered-product-price">{$p->price->value} AZN</div>
													<div class="ordered-product-count">{$p->count} ədəd</div>
													<div class="clear"></div>
												</div>
												<div class="clear"></div>
											</div>
										{/foreach}
									</div>
								</div>
							</div>
						</td>
						<td>
							<a href="javascript:void(0)" class="new-window edit-order" have-parent="1" reload-parent="1" title="{$messages.interface_common.edit}" data-url="{$app_url}/{$admin_title}/edit/3/{$o.order->id->value}"><img src="{$static_url}/{$theme_folder}/img/edit-icon-dark.png" /></a>
						</td>
						<td>
							<a class="edit-order" href="{$app_url}/{$admin_title}/get_order_check/{$o.order->id->value}" target="_blank"><img src="{$static_url}/{$theme_folder}/img/print-icon-dark.png" /></a>
						</td>
						<td>
							<a class="delete-order edit-order" element-id="{$o.order->id->value}" href="javascript:void(0)"><img src="{$static_url}/{$theme_folder}/img/delete-icon-dark.png" /></a>
						</td>
					</tr>
				{/foreach}
			</table>
		</div>
		{/foreach}
</div>

{literal}
	<script>
		new TabController('#order-tab-container');
	
		$('.view-order').click(function(){
			var title = $(this).siblings('.ordered-products-container').find('.block-title').html(),
			content = $(this).siblings('.ordered-products-container').find('.block-content').html();
			console.log($(this).siblings('.ordered-products-container'));
			new BlockUI(title, content);
		});
		
		$('.delete-order').click(function(){
			var winId = $(this).parents('.window').attr('id');
			var orderId = $(this).attr('element-id');
			if (confirm(lang['fm_ready_to_delete'])) {
				$.ajax({
					url: app['url'] + '/remove_order/' + orderId,
					type: 'post',
					success: function(){
						reloadWindow(winId);
					}
				});
			}
		});
		
	</script>
{/literal}