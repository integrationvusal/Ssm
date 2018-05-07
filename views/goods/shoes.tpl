{if isset($goodsData)}
    <form class="form-horizontal" role="form" class="goods-form" method="post"
          action="{$app_url}/goods/edit/{$goodsData.common.id}" enctype="multipart/form-data">
        <h3>{$model.title}</h3>
        <hr>

        <input type="hidden" name="user_id" value="{$user.id}">
        <input type="hidden" name="goods_id" value="{$goodsData.common.id}">
        <input type="hidden" name="old_images_str" value="{$goodsData.model.image}">
        <input type="hidden" name="model_id" value="{$goodsData.model.id}">
        <input type="hidden" name="model_type" value="{$goodsData.common.goods_type}">

        
        <div class="form-group {if $formattrs.barcode.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Barkod:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="barcode" value="{$goodsData.common.barcode}">
            </div>
        </div>

        <div class="form-group {if $formattrs.code.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Malın kodu:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="code" value="{$goodsData.model.code}">
            </div>
        </div>

        <div class="form-group {if $formattrs.category.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Kategoriya:</label>
            <div class="col-sm-10">
                <select id="category_id" name="category_id" class="form-control">
                    <option value="0">Kategoriyanı seç</option>
                    {foreach from=$categories item=category}
                        <option {if $category.id == $goodsData.model.category_id}selected{/if} value="{$category.id}">{$category.name}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="form-group {if $formattrs.name.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Malın adı:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="name" value="{$goodsData.model.name}">
            </div>
        </div>

        <div class="form-group {if $formattrs.country.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">İstehsal olunduğu ölkə:</label>
            <div class="col-sm-10">
                <select id="country" name="country" class="form-control">
                    <option value="{$goodsData.model.country}">{$goodsData.model.country}</option>
                    {foreach from=$countries item=country}
                        <option value="{$country}">{$country}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="form-group {if $formattrs.brand.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Markası (Brend):</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="brand" value="{$goodsData.model.brand}">
            </div>
        </div>

        <div class="form-group {if $formattrs.model.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Modeli:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="model" value="{$goodsData.model.model}">
            </div>
        </div>

        <div class="form-group {if $formattrs.type.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Növü:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="type" value="{$goodsData.model.type}">
            </div>
        </div>

        <div class="form-group {if $formattrs.color.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Rəngi:</label>
            <div class="col-sm-10">
                <select class="form-control" name="color">
                    {if !empty($goodsData.model.color)}
                        <option value="{$goodsData.model.color}">{$colors[$goodsData.model.color].title}</option>
                    {else}
                        <option value="">Rəngi seçin</option>
                    {/if}
                    {foreach from=$colors item=color key=k}
                        <option value="{$k}" class="class{$k}">{$color.title|ucfirst}</option>
                    {/foreach}
                </select>
            </div>
        </div>
		
		<div class="form-group {if $formattrs.currency.val == 0}goods-rest-field{/if}">
			<label class="control-label col-sm-2" for="">Valyuta:</label>
			<div class="col-sm-10">
				<select class="form-control" name="currency">
					<option value="0">AZN</option>
					{foreach from=$currencies item=currency}
						<option {if $goodsData.common.currency == $currency.id }selected="selected"{/if} value="{$currency.id}">{$currency.name}</option>
					{/foreach}
				</select>
			</div>
		</div>

        <div class="form-group {if $formattrs.size.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2">Ölçüsü:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="size" value="{$goodsData.model.size}">
            </div>
        </div>

        <div class="form-group {if $formattrs.buy_price.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Ədədin alış qiyməti:</label>
            <div class="col-sm-10">
                <input type="number" step="0.01" class="form-control" name="buy_price" value="{$goodsData.common.buy_price}">
            </div>
        </div>

        <div class="form-group {if $formattrs.sell_price.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Ədədin satış qiyməti:</label>
            <div class="col-sm-10">
                <input type="number" step="0.01" class="form-control" name="sell_price" value="{$goodsData.common.sell_price}">
            </div>
        </div>

        <div class="form-group {if $formattrs.description.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2">Əlavə məlumat:</label>
            <div class="col-sm-10">
                <textarea class="form-control" name="description" id="description" cols="30" rows="10">{$goodsData.model.description}</textarea>
            </div>
        </div>

        <div class="img-set {if $formattrs.image.val == 0}goods-rest-field{/if}" style="display: none">
            <label for="image" class="col-md-2 control-label">Şəkil:</label>
            <div class="col-md-8">
                <img src="" width="60" style="float: left; margin-right: 20px; display: none;" class="clientImagePreview">
                <input type="file" name="image[]" data-rel="0" class="btn btn-primary btn-file clientImage" accept="image/*">
            </div>
            <div class="col-md-2">
                <button type="button" data-rel="0" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
            </div>
        </div>

        <span class="img-set-container {if $formattrs.image.val == 0}goods-rest-field{/if}">

            {if !empty($oldImages)}
                {assign var="i" value=0}
                {foreach from=$oldImages item=oldImage}
                    <div class="form-group img-set-{$i} img-set">
                        <label for="image" class="col-md-2 control-label">Şəkil:</label>
                        <div class="col-md-8">
                            <img src="{$app_url}/{$oldImage}" width="60" style="float: left; margin-right: 20px;" class="clientImagePreview0">
                            <input type="file" name="image[]" data-rel="{$i}" id="clientImageReq{$i}" class="btn btn-primary btn-file clientImage" accept="image/*">
                            <input type="hidden" name="old_image[]" value="{$oldImage}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" data-rel="{$i}" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
                            {if $i == 0}
                                <button type="button" data-rel="0" class="btn btn-primary btn-block plusImage"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
                            {/if}
                        </div>
                    </div>
                    {assign var="i" value=$i+1}
                {/foreach}
            {else}
                <div class="form-group img-set-0 img-set">
                <label for="image" class="col-md-2 control-label">Şəkil:</label>
                <div class="col-md-8">
                    <img src="" width="60" style="float: left; margin-right: 20px; display: none;" class="clientImagePreview0">
                    <input type="file" name="image[]" data-rel="0" id="clientImageReq0" class="btn btn-primary btn-file clientImage" accept="image/*">
                </div>
                <div class="col-md-2">
                    <button type="button" data-rel="0" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
                    <button type="button" data-rel="0" class="btn btn-primary btn-block plusImage"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
                </div>
            </div>
            {/if}

        </span>

        <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-warning form-control add-goods">YENİLƏ</span></button>
            </div>
        </div>
    </form>
{else}
    <form class="form-horizontal" role="form" class="goods-form" method="post"
          action="{$app_url}/goods/create" enctype="multipart/form-data">
        <h3>{$model.title}</h3>
        <hr>

        <input type="hidden" name="user_id" value="{$user.id}">

        <div class="form-group {if $formattrs.barcode.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Barkod:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="barcode">
            </div>
        </div>

        <div class="form-group {if $formattrs.code.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Malın kodu:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="code">
            </div>
        </div>

        <div class="form-group {if $formattrs.category.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Kategoriya:</label>
            <div class="col-sm-10">
                <select id="category_id" name="category_id" class="form-control">
                    <option value="0">Kategoriyanı seç</option>
                    {foreach from=$categories item=category}
                        <option value="{$category.id}">{$category.name}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="form-group {if $formattrs.name.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Malın adı:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="name">
            </div>
        </div>

        <div class="form-group {if $formattrs.country.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">İstehsal olunduğu ölkə:</label>
            <div class="col-sm-10">
                <select id="country" name="country" class="form-control">
                    <option value="">İstehsalçı ölkə</option>
                    {foreach from=$countries item=country}
                        <option value="{$country}">{$country}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="form-group {if $formattrs.brand.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Markası (Brend):</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="brand">
            </div>
        </div>

        <div class="form-group {if $formattrs.model.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Modeli:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="model">
            </div>
        </div>

        <div class="form-group {if $formattrs.type.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Növü:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="type">
            </div>
        </div>

        <div class="form-group {if $formattrs.color.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Rəngi:</label>
            <div class="col-sm-10">
                <select class="form-control" name="color">
                    <option value="">Rəngi seçin</option>
                    {foreach from=$colors item=color key=k}
                        <option value="{$k}" class="class{$k}">{$color.title|ucfirst}</option>
                    {/foreach}
                </select>
            </div>
        </div>
		
		<div class="form-group {if $formattrs.currency.val == 0}goods-rest-field{/if}">
			<label class="control-label col-sm-2" for="">Valyuta:</label>
			<div class="col-sm-10">
				<select class="form-control" name="currency">
					<option value="0">AZN</option>
					{foreach from=$currencies item=currency}
						<option value="{$currency.id}">{$currency.name}</option>
					{/foreach}
				</select>
			</div>
		</div>

        <div class="form-group {if $formattrs.size.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2">Ölçüsü:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="size">
            </div>
        </div>

        <div class="form-group {if $formattrs.buy_price.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Ədədin alış qiyməti:</label>
            <div class="col-sm-10">
                <input type="number" step="0.01" class="form-control" name="buy_price" value="0">
            </div>
        </div>

        <div class="form-group {if $formattrs.sell_price.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2" for="">Ədədin satış qiyməti:</label>
            <div class="col-sm-10">
                <input type="number" step="0.01" class="form-control" name="sell_price" value="0">
            </div>
        </div>

        <div class="form-group {if $formattrs.description.val == 0}goods-rest-field{/if}">
            <label class="control-label col-sm-2">Əlavə məlumat:</label>
            <div class="col-sm-10">
                <textarea class="form-control" name="description" id="description" cols="30" rows="10"></textarea>
            </div>
        </div>

        <div class="img-set {if $formattrs.image.val == 0}goods-rest-field{/if}" style="display: none">
            <label for="image" class="col-md-2 control-label">Şəkil:</label>
            <div class="col-md-8">
                <img src="" width="60" style="float: left; margin-right: 20px; display: none;" class="clientImagePreview">
                <input type="file" name="image[]" data-rel="0" class="btn btn-primary btn-file clientImage" accept="image/*">
            </div>
            <div class="col-md-2">
                <button type="button" data-rel="0" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
            </div>
        </div>

        <div class="form-group img-set-0 img-set {if $formattrs.image.val == 0}goods-rest-field{/if}">
            <label for="image" class="col-md-2 control-label">Şəkil:</label>
            <div class="col-md-8">
                <img src="" width="60" style="float: left; margin-right: 20px; display: none;" class="clientImagePreview0">
                <input type="file" name="image[]" data-rel="0" id="clientImageReq0" class="btn btn-primary btn-file clientImage" accept="image/*">
            </div>
            <div class="col-md-2">
                <button type="button" data-rel="0" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
                <button type="button" data-rel="0" class="btn btn-primary btn-block plusImage"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
            </div>
        </div>

        <span class="img-set-container {if $formattrs.image.val == 0}goods-rest-field{/if}"></span>

        <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-danger form-control add-goods">ƏLAVƏ ET</span></button>
            </div>
        </div>
    </form>
{/if}