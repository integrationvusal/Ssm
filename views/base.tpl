<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SSM</title>

	<link href="{$static_url}/css/bootstrap.min.css" rel="stylesheet">
	<link href="{$static_url}/css/bootstrap-datepicker.standalone.min.css" rel="stylesheet">
	<link href="{$static_url}/css/bootstrap-switch.min.css" rel="stylesheet">
	<link href="{$static_url}/css/styles.min.css" rel="stylesheet">
	<link href="{$static_url}/js/skins/dhtmlxmessage_dhx_terrace.css" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="{$static_url}/fancybox/jquery.fancybox.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="{$static_url}/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
	<link rel="stylesheet" type="text/css" href="{$static_url}/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />

	<script src="{$static_url}/js/jquery-1.11.1.min.js"></script>

	<!--[if lt IE 9]>
	<link href="{$static_url}/css/rgba-fallback.css" rel="stylesheet">
	<script src="{$static_url}/js/html5shiv.js"></script>
	<script src="{$static_url}/js/respond.min.js"></script>
	<![endif]-->
	<style>
		{if isset($colors) }
			{foreach from=$colors key=k item=color}
				.class{$k} {
					display: block;
				}

				.class{$k}:before{
					content: "   ";
					background-color: {$color.code};
					padding: 2px 5px !important;
					margin-right: 5px;
				}
			{/foreach}
		{/if}
	</style>
</head>

<body {if isset($localcurrencies) }data-localcurrencies="{$localcurrencies}"{/if}>
<div class="loader"><img src="{$static_url}/img/loader.gif" alt=""></div>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{$app_url}"><span>Small Stock Manager</span></a>
			<ul class="nav navbar-top-links navbar-right">
				<li style="display:inline-table"><a href="javascript:void(0)"><span class="glyphicon glyphicon-user"></span> {if isset($user)}{$user.name|strtoupper}{else}User{/if}</a></li>
				<li style="display:inline-table"><a href="{$app_url}/signout"><span class="glyphicon glyphicon-off"></span> Çıxış</a></li>
			</ul>
		</div>
	</div><!-- /.container-fluid -->
</nav>

{if !isset($sell_only) || !$sell_only}
<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
	<form role="search">
		<!-- <div class="form-group">
            <input type="text" class="form-control" placeholder="Axtar">
        </div> -->
        <span class="glyphicon glyphicon-menu-left hidemenu"></span>
	</form>
	<ul class="nav menu">

		{if !isset($current_menu)}
			{assign var=current_menu value="store"}
		{/if}
		{if $user.id == 1 && $user.spc == 1}
			<li class="parent">
				<a class="menuCollapse" href="#admin-item">
					<span class="glyphicon glyphicon-list"></span> ADMIN<span class="icon pull-right">
					<em class="glyphicon glyphicon-s admin-item-sign {if $current_menu == 'admin_users' || $current_menu == 'admin_notice'}glyphicon-minus{else}glyphicon-plus{/if}"></em></span>
				</a>
				<ul class="children collapse {if $current_menu == 'admin_users' || $current_menu == 'admin_notice'}in{/if} " id="admin-item">
					<li>
						<a class="" href="{$app_url}/manager">
							<span class="glyphicon glyphicon-share-alt"></span> İstifadəçilər
						</a>
					</li>
					<li>
						<a class="" href="{$app_url}/manager/notice">
							<span class="glyphicon glyphicon-share-alt"></span> Elanlar
						</a>
					</li>
				</ul>
			</li>
		{/if}

		<li {if $current_menu == 'home'}class = "active"{/if}><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span>ƏSAS SƏHİFƏ</a></li>
		{if $permissions.change_subject}
		<li {if $current_menu == 'subject'}class = "active"{/if}><a href="{$app_url}/subject"><span class="glyphicon glyphicon-dashboard"></span>OBYEKTI DƏYİŞ</a></li>
		{/if}
		{if $permissions.service_read || $permissions.category_read || $permissions.catalog_read || $permissions.contragent_read || $permissions.client_read}
		<li class="parent">
			<a class="menuCollapse" href="#sub-item-1">
				<span class="glyphicon glyphicon-list"></span> XÜSUSİYYƏTLƏR<span class="icon pull-right">
					<em class="glyphicon glyphicon-s sub-item-1-sign {if $current_menu == 'category' || $current_menu == 'expense' || $current_menu == 'goods' || $current_menu == 'contragent' || $current_menu == 'client' || $current_menu == 'discount_card' || $current_menu == 'discount_rule'}glyphicon-minus{else}glyphicon-plus{/if}"></em></span>
			</a>
			<ul class="children collapse {if $current_menu == 'category' || $current_menu == 'goods' || $current_menu == 'contragent' || $current_menu == 'client' || $current_menu == 'service' || $current_menu == 'expense' || $current_menu == 'discount_card' || $current_menu == 'discount_rule'}in{/if} " id="sub-item-1">
				{if $permissions.service_read && $subject.type == 2}
					<li {if $current_menu == 'service'}class = "active"{/if}>
						<a class="" href="{$app_url}/service">
							<span class="glyphicon glyphicon-share-alt"></span> Xidmətlər
						</a>
					</li>
				{/if}
				{if $permissions.expense_read}
					<li>
						<a class="{if $current_menu == 'expense'}active{/if}" href="{$app_url}/expense">
							<span class="glyphicon glyphicon-share-alt"></span> Xərclər
						</a>
					</li>
				{/if}
				{if $permissions.category_read}
				<li {if $current_menu == 'category'}class = "active"{/if}>
					<a class="" href="{$app_url}/category">
						<span class="glyphicon glyphicon-share-alt"></span> Kategoriyalar
					</a>
				</li>
				{/if}
				{if $permissions.catalog_read}
				<li {if $current_menu == 'goods'}class = "active"{/if}>
					<a class="" href="{$app_url}/goods">
						<span class="glyphicon glyphicon-share-alt"></span> Katalog
					</a>
				</li>
				{/if}
				{if $permissions.contragent_read}
				<li {if $current_menu == 'contragent'}class = "active"{/if}>
					<a class="" href="{$app_url}/contragent">
						<span class="glyphicon glyphicon-share-alt"></span> Kontragent
					</a>
				</li>
				{/if}
				{if $permissions.client_read}
				<li {if $current_menu == 'client'}class = "active"{/if}>
					<a class="" href="{$app_url}/client">
						<span class="glyphicon glyphicon-share-alt"></span> Müştəri
					</a>
				</li>
				{/if}
				{if $permissions.discount_rule}
				<li {if $current_menu == 'discount_rule'}class = "active"{/if}>
					<a class="" href="{$app_url}/discount/rule">
						<span class="glyphicon glyphicon-share-alt"></span> Bonus xüsusiyyətlər
					</a>
				</li>
				{/if}
				{if $permissions.discount_card}
				<li {if $current_menu == 'discount_card'}class = "active"{/if}>
					<a class="" href="{$app_url}/discount/card">
						<span class="glyphicon glyphicon-share-alt"></span> Bonus kartlar
					</a>
				</li>
				{/if}
			</ul>
		</li>
		{/if}
		{if $permissions.contragent_return}
		<li {if $current_menu == 'contragent/return'}class = "active"{/if}><a href="{$app_url}/contragent/return"><span class="glyphicon glyphicon-credit-card"></span> KONTRAGENTƏ GERİ</a></li>
		{/if}
		{if $permissions.store_read}
		<li {if $current_menu == 'store'}class = "active"{/if}><a href="{$app_url}/store"><span class="glyphicon glyphicon-credit-card"></span> MALLAR</a></li>
		{/if}
		{if ($subject.type == 2)}
			{if $permissions.sell_read}
			<li {if $current_menu == 'sell'}class = "active"{/if}><a href="{$app_url}/sell"><span class="glyphicon glyphicon-credit-card"></span> SATIŞ</a></li>
			<li {if $current_menu == 'return'}class = "active"{/if}><a href="{$app_url}/sell/return"><span class="glyphicon glyphicon-credit-card"></span> MÜŞTƏRİDƏN GERİ</a></li>
			{/if}
			{if $permissions.service_sell}
				<li {if $current_menu == 'service_sell'}class = "active"{/if}><a href="{$app_url}/service/sell"><span class="glyphicon glyphicon-credit-card"></span>XİDMƏT SATIŞI</a></li>
			{/if}
			{if $permissions.expense_sell}
				<li {if $current_menu == 'expense_sell'}class = "active"{/if}><a href="{$app_url}/expense/sell"><span class="glyphicon glyphicon-credit-card"></span>XƏRCLƏR</a></li>
			{/if}
		{/if}
		{if $permissions.store_transfer}
		<li {if $current_menu == 'stock'}class = "active"{/if}><a href="{$app_url}/stock"><span class="glyphicon glyphicon-credit-card"></span> TRANSFER</a></li>
		{/if}
		{if ($subject.type == 2 && $permissions.cashbox_read)}
			<li {if $current_menu == 'cashbox'}class = "active"{/if}><a href="{$app_url}/cashbox"><span class="glyphicon glyphicon-credit-card"></span> KASSA</a></li>
		{/if}
		{if $permissions.invoice_read}
		<li {if $current_menu == 'invoice'}class = "active"{/if}><a href="{$app_url}/invoice"><span class="glyphicon glyphicon-credit-card"></span> QAİMƏLƏR</a></li>
		{/if}
		{if $permissions.operator_read}
		<li {if $current_menu == 'operator'}class = "active"{/if}><a href="{$app_url}/operator"><span class="glyphicon glyphicon-user"></span> İSTİFADƏÇİLƏR</a></li>
		{/if}
		{if $permissions.receipt_read}
			<li {if $current_menu == 'receipt'}class = "active"{/if}><a href="{$app_url}/receipt"><span class="glyphicon glyphicon-credit-card"></span> QƏBZLƏR</a></li>
		{/if}
		{if $permissions.setting}
			<li class="parent">
				<a class="menuCollapse" href="#setting">
					<span class="glyphicon glyphicon-list"></span> SAZLAMALAR<span class="icon pull-right"><em class="glyphicon glyphicon-s setting-sign
					{if $current_menu == 'formsetting' || $current_menu == 'tablesetting' || $current_menu == 'receipt_attributes'}glyphicon-minus{else}glyphicon-plus{/if} "></em></span>
				</a>
				<ul class="children collapse {if in_array($current_menu, ['formsetting', 'tablesetting', 'receipt_attributes', 'receipt_attributes', 'admin_currency'])}in{/if} " id="setting">
					<li>
						<a {if $current_menu == 'admin_currency'}class = "active"{/if} href="{$app_url}/manager/currency">
							<span class="glyphicon glyphicon-share-alt"></span> Valyutalar
						</a>
					</li>
					<li {if $current_menu == 'formsetting'}class = "active"{/if}>
						<a class="" href="{$app_url}/settings/form">
							<span class="glyphicon glyphicon-share-alt"></span> Forma
						</a>
					</li>
					<li {if $current_menu == 'tablesetting'}class = "active"{/if}>
						<a class="" href="{$app_url}/settings/table">
							<span class="glyphicon glyphicon-share-alt"></span> Cədvəl
						</a>
					</li>
					<li {if $current_menu == 'receipt_attributes'}class = "active"{/if}>
						<a class="" href="{$app_url}/receipt/attributes">
							<span class="glyphicon glyphicon-share-alt"></span> Qəbz atributları
						</a>
					</li>
				</ul>
			</li>
		{/if}
		<li role="presentation" class="divider"></li>
		{if $permissions.report_read}
			<li class="parent">
				<a class="menuCollapse" href="#reports">
					<span class="glyphicon glyphicon-list"></span> HESABATLAR<span class="icon pull-right"><em class="glyphicon glyphicon-s reports-sign
					{if $current_menu == 'report_contragent' || $current_menu == 'report_client' || $current_menu == 'report_expense' || $current_menu == 'report_cashbox' || $current_menu == 'report_service' || $current_menu == 'report_sell' || $current_menu == 'report_remain' || $current_menu == 'report_discount'}glyphicon-minus{else}glyphicon-plus{/if} "></em></span>
				</a>
				<ul class="children collapse {if $current_menu == 'report_contragent' || $current_menu == 'report_expense' || $current_menu == 'report_client' || $current_menu == 'report_cashbox' || $current_menu == 'report_service' || $current_menu == 'report_sell' || $current_menu == 'report_remain' || $current_menu == 'report_discount' || $current_menu == 'report_difference'}in{/if} " id="reports">
                    {if $permissions.contragent_read}
					<li {if $current_menu == 'report_contragent'}class = "active"{/if}>
						<a class="" href="{$app_url}/report/contragent">
							<span class="glyphicon glyphicon-share-alt"></span> Kontragentlər
						</a>
					</li>
					{/if}
					{if $permissions.client_read}
					<li {if $current_menu == 'report_client'}class = "active"{/if}>
						<a class="" href="{$app_url}/report/client">
							<span class="glyphicon glyphicon-share-alt"></span> Müştərilər
						</a>
					</li>
                    {/if}
                    {if $permissions.cashbox_read}
					<li {if $current_menu == 'report_cashbox'}class = "active"{/if}>
						<a class="" href="{$app_url}/report/cashbox">
							<span class="glyphicon glyphicon-share-alt"></span> Kassa
						</a>
					</li>
                    {/if}
                    {if $permissions.sell_read}
					<li {if $current_menu == 'report_sell'}class = "active"{/if}>
						<a class="" href="{$app_url}/report/sell">
							<span class="glyphicon glyphicon-share-alt"></span> Satış
						</a>
					</li>
                    {/if}
                    {if $permissions.sell_read}
					<li {if $current_menu == 'report_difference'}class = "active"{/if}>
						<a class="" href="{$app_url}/report/difference">
							<span class="glyphicon glyphicon-share-alt"></span> Ümumi gəlir
						</a>
					</li>
                    {/if}
                    {if $permissions.service_read}
					<li {if $current_menu == 'report_service'}class = "active"{/if}>
						<a class="" href="{$app_url}/report/service">
							<span class="glyphicon glyphicon-share-alt"></span> Xidmətlər
						</a>
					</li>
                    {/if}
                    {if $permissions.expense_read}
					<li {if $current_menu == 'report_expense'}class = "active"{/if}>
						<a class="" href="{$app_url}/report/expense">
							<span class="glyphicon glyphicon-share-alt"></span> Xərclər
						</a>
					</li>
                    {/if}
					<li {if $current_menu == 'report_remain'}class = "active"{/if}>
						<a class="" href="{$app_url}/report/remain">
							<span class="glyphicon glyphicon-share-alt"></span> Qalıq
						</a>
					</li>
                    {if $permissions.discount_card}
					<li {if $current_menu == 'report_discount'}class = "active"{/if}>
						<a class="" href="{$app_url}/report/discount">
							<span class="glyphicon glyphicon-share-alt"></span> Bonus kartlar
						</a>
					</li>
					{/if}
				</ul>
			</li>
		{/if}
		{if $permissions.contact}
			<li {if $current_menu == 'contact'}class = "active"{/if}><a href="{$app_url}/contact"><span class="glyphicon glyphicon-envelope"></span> Əlaqə</a></li>
		{/if}
	</ul>
</div><!--/.sidebar-->
<span class="glyphicon glyphicon-menu-right showmenu"></span>
{/if}

{block name="dashboard"}

{/block}

<script src="{$static_url}/js/bootstrap.min.js"></script>
<script src="{$static_url}/js/chart.min.js"></script>
<script src="{$static_url}/js/chart-data.js"></script>
<script src="{$static_url}/js/bootstrap-datepicker.min.js"></script>
<script src="{$static_url}/js/bootstrap-datepicker.az.min.js"></script>
<script src="{$static_url}/js/easypiechart.js"></script>
<script src="{$static_url}/js/bootstrap-switch.min.js"></script>
<script src="{$static_url}/js/bootstrap-table.js"></script>
<script src="{$static_url}/js/custom.js"></script>
<script src="{$static_url}/js/dhtmlxmessage.js"></script>
<script src="{$static_url}/js/printThis.js"></script>

<!--script type="text/javascript" src="{$static_url}/fancybox/jquery.mousewheel-3.0.6.pack.js"></script-->
<script type="text/javascript" src="{$static_url}/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="{$static_url}/fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
<script type="text/javascript" src="{$static_url}/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
<script type="text/javascript" src="{$static_url}/fancybox/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
<script>
	var URL = '{$app_url}';
</script>
<script src="{$static_url}/js/ssm.min.js"></script>

{if isset($flash) && $flash != null}
<script>
	handleContext({json_encode($flash)});
</script>
{/if}
</body>

</html>
