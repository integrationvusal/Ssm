<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>MEQA SiteBuilder</title>
		<link rel="stylesheet/less" type="text/css" href="{$app_url}/modules/admin/static/{$theme_folder}/less/stylesheets.less" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<script src="{$static_url}/{$theme_folder}/js/less.js"></script>
		<script src="{$app_url}/get_static/js/admin"></script>
		<script src="{$static_url}/{$theme_folder}/js/tiny_mce/tiny_mce.js"></script>
		<script src="{$app_url}/imagemanager/jscripts/mcimagemanager.js"></script>
		<script>
			var app = [];
			app['url'] = '{$app_url}/{$admin_title}';
		</script>
		<style>
			.shortcut-container {
				color: white;
			}
		</style>
	</head>
	<body>
		<div id="desktop-container">
			<div id="top-panel-container">
				<div id="top-panel-wrapper">
					<div id="resizer-container">
						<img src="{$static_url}/{$theme_folder}/img/top-panel-resizer.png" />
					</div>
					<div id="top-panel-content">
						<div id="logo-container">
							<a href="{$app_url}/{$admin_title}"><img src="{$static_url}/{$theme_folder}/img/logo.png" /></a>
						</div>
						<div id="settings-container">
							<div id="user-avatar-container">
								<div id="user-avatar">
									<img src="{$app_url}/imageresizer/resize/50/50/{$public_folder}{$user_info->avatar->value}" />
								</div>
								<div id="user-initials">
									{if isset($user_info)}{$user_info->name->value}{/if}
								</div>
								<div class="clear"></div>
							</div>
							<div id="settings-icon-container">
								<!--<img src="{$static_url}/{$theme_folder}/img/settings-icon.png" />-->
							</div>
							<div class="clear"></div>
							<div id="settings-dropdown">
								<div class="setting-dropdown-item">
									<a class="new-window" data-url="admin/edit/{$users_model_index}/{$user_info->id->value}" href="javascript:void(0)" title="{$messages.interface_common.user_settings}">{$messages.interface_common.user_settings}</a>
								</div>
								<div class="setting-dropdown-item"><a href="{$app_url}/{$admin_title}/logout">{$messages.interface_common.logout}</a></div>
							</div>
						</div>
						<!-- Icons container shortcuts -->
						<div id="top-panel-icons-container">
							{$top_panel_icons}
						</div>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<div id="desktop-elements-container">
				<div class="left">
				{foreach from=$main_model_icons item=m name=model_shortcuts}
					{if $smarty.foreach.model_shortcuts.index % 5 == 0}
						</div>
						<div class="left">
					{/if}
					{$m}
				{/foreach}
				</div>
				<div class="clear"></div>
			</div>
			<div id="bottom-panel-container">
				<div id="bottom-panel-wrapper">
					<div id="bottom-panel-resizer">
						<img src="{$static_url}/{$theme_folder}/img/bottom-panel-resizer.png" />
					</div>
					<div id="bottom-panel-content"></div>
				</div>
			</div>
		</div>
		
		
		
		<!--  hidden layers -->
		<div id="window-popup" class="absolute hide window-popup">
			<div class="popup-item" action="0">{$messages.interface_common.reload}</div>
		</div>
		<!--  hidden layers end -->
		<!-- templates -->
		
		<script type="html/template" id="tab-button-container">
			<div class="window-header-tab-button tab-<%=index%>"><%=title%></div>
		</script>
		
		<script type="html/template" id="tree-item-tpl">
			<div class="tree-node-container" id="tree-item-<%=id%>-<%=randomNum%>" elId="<%=id%>" pId="<%=pId%>" url="<%=app['url']%>">
				<div class="tree-node">
					<div class="tree-node-slide <% if (childs.length == 0) { %>hidden<% } %>"></div>
					<div class="tree-node-title"><% if (first) { %><img src="<%=title%>" /><% } else { %> <%=title%> <% } %></div>
					<div class="tree-node-operations">
						<div class="tree-add-node new-window" data-url="{$app_url}/{$admin_title}/structure/add/<%=treeModelId%>/<%=id%>" reload-parent="1" have-parent="1" title="{$messages.interface_common.add}">
							<img src="{$static_url}/{$theme_folder}/img/tree-plus-icon.png" />
						</div>
						<% if (!first) { %>
						<div class="tree-edit-node new-window" data-url="{$app_url}/{$admin_title}/structure/edit/<%=treeModelId%>/<%=id%>" reload-parent="1" have-parent="1" title="{$messages.interface_common.edit}">
							<img src="{$static_url}/{$theme_folder}/img/tree-edit-icon.png" />
						</div>
						<div class="tree-delete-node" data-url="{$app_url}/{$admin_title}/structure/delete/<%=treeModelId%>/<%=id%>">
							<img src="{$static_url}/{$theme_folder}/img/tree-remove-icon.png" />
						</div>
						<% } %>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="tree-node-sibling"></div>
				<div class="tree-node-childs">
					<%=childs%>
				</div>
			</div>
		</script>
		
		<script type="html/template" id="block-ui-template">
			<div class="block-ui-container" id="<%=id%>">
				<div class="block-ui-window">
					<div class="block-ui-window-header">
						<div class="block-ui-window-title"><%=title%></div>
						<div class="block-ui-window-close-button"></div>
						<div class="clear"></div>
					</div>
					<div class="block-ui-window-content"><%=message%></div>
				</div>
			</div>
		</script>
		
		<script type="html/template" id="taskbar-element-template">
			<div class="bottom-panel-item" id="<%=id%>">
				<div class="left bottom-panel-item-text"><%=title%></div>
				<div class="left bottom-panel-item-close-icon" onclick="closeWindow('<%=windowId%>')"><img src="{$static_url}/{$theme_folder}/img/window-close-button.png" /></div>
			</div>
		</script>
		
		<!-- show message tpl -->
		<script type="html/template" id="message-tpl">
			<div class="message-container" id="<%=id%>">
				<div class="message-title"><%=title%></div>
				<div class="message-content"><%=content%></div>
				<div class="button-std message-close"><%=Lang['close']%></div>
			</div>
		</script>
		
		<!-- window tpl -->
		<script type="html/template" id="window-template">
			<div class="window absolute">
				<div class="relative width100 height100">
					<div class="window-header">
							<div class="window-title"></div>
							<div class="window-buttons">
								<div class="window-button minimize-button"></div>
								<div class="window-button close-button"></div>
								<!--<div class="maximize-button window-button left"></div>-->
							</div>
							<div class="clear"></div>
						<div class="clear"></div>
					</div>
					<div class="window-errors-container"></div>
					<div class="window-header-tabs-container">
						<div class="clear"></div>
					</div>
					<div class="window-content-container relative">
						<div class="window-content"></div>
					</div>
					<div class="window-resizer"></div>
					<!--<div class="window-bottom-panel absolute text">sds</div>-->
				</div>
			</div>
		</script>
		
		<!-- tree item tpl -->
		
		
		<!-- templates end -->
		
		<!-- Javascripts -->
		
		<!-- Javascripts end -->
		
	</body>
</html>