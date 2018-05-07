var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(r){var t,e,o,a,h,n,c,d="",C=0;for(r=Base64._utf8_encode(r);C<r.length;)t=r.charCodeAt(C++),e=r.charCodeAt(C++),o=r.charCodeAt(C++),a=t>>2,h=(3&t)<<4|e>>4,n=(15&e)<<2|o>>6,c=63&o,isNaN(e)?n=c=64:isNaN(o)&&(c=64),d=d+this._keyStr.charAt(a)+this._keyStr.charAt(h)+this._keyStr.charAt(n)+this._keyStr.charAt(c);return d},decode:function(r){var t,e,o,a,h,n,c,d="",C=0;for(r=r.replace(/[^A-Za-z0-9\+\/\=]/g,"");C<r.length;)a=this._keyStr.indexOf(r.charAt(C++)),h=this._keyStr.indexOf(r.charAt(C++)),n=this._keyStr.indexOf(r.charAt(C++)),c=this._keyStr.indexOf(r.charAt(C++)),t=a<<2|h>>4,e=(15&h)<<4|n>>2,o=(3&n)<<6|c,d+=String.fromCharCode(t),64!=n&&(d+=String.fromCharCode(e)),64!=c&&(d+=String.fromCharCode(o));return d=Base64._utf8_decode(d)},_utf8_encode:function(r){r=r.replace(/\r\n/g,"\n");for(var t="",e=0;e<r.length;e++){var o=r.charCodeAt(e);128>o?t+=String.fromCharCode(o):o>127&&2048>o?(t+=String.fromCharCode(o>>6|192),t+=String.fromCharCode(63&o|128)):(t+=String.fromCharCode(o>>12|224),t+=String.fromCharCode(o>>6&63|128),t+=String.fromCharCode(63&o|128))}return t},_utf8_decode:function(r){for(var t="",e=0,o=c1=c2=0;e<r.length;)o=r.charCodeAt(e),128>o?(t+=String.fromCharCode(o),e++):o>191&&224>o?(c2=r.charCodeAt(e+1),t+=String.fromCharCode((31&o)<<6|63&c2),e+=2):(c2=r.charCodeAt(e+1),c3=r.charCodeAt(e+2),t+=String.fromCharCode((15&o)<<12|(63&c2)<<6|63&c3),e+=3);return t}};

var Loader = {

    timerId:0,
    request:undefined,
    lStart: function(request, timeout){
        clearTimeout(Loader.timerId);

        if(typeof timeout === "undefined") timeout = 20000;
        Loader.request = request;
        $(".loader").css("z-index", 1000000000);
        $(".loader").show();
        Loader.timerId = setTimeout(function() {
            Loader.lStop();
            dhtmlx.alert({
                title:"Bağlantıda problem",
                ok:"OK",
                text:"Bağlantıda probləmlər yaşanır. <br />Lütfən bir qədər sonra təkrarlayın",
                callback:function(){
                }
            });
        }, timeout);
    },
    lStop: function(){

        $(".loader").hide();
        clearTimeout(Loader.timerId);
        if(typeof Loader.request != 'undefined') Loader.request.abort();
    },
    lShowMessage: function(){
        dhtmlx.alert({
            title:"Bağlantıda problem",
            ok:"OK",
            text:"Bağlantıda probləmlər yaşanır. <br />Lütfən bir qədər sonra təkrarlayın",
            callback:function(){
            }
        });
    }
}

function showMessage(title, text){
    dhtmlx.alert({
        title: title,
        text: text
    });
}

function handleContext(context){
    if(context.type == 0){
        showMessage(context.title, context.message);
    }
}

function hasCurrency(currency_id){
    for(x in localcurrencies){
       for(y in localcurrencies[x]){
          if(localcurrencies[x][y] == currency_id) return localcurrencies[x].name;
       }
    }
}

function generateNextInvoiceSerial(serial){
    firstSide = serial.substr(0, 8);
    newCode = (parseInt(serial.substr(-6))+1).toString();

    zeros = '';
    for(i = 0; i < 6-newCode.length; i++)
        zeros += '0';

    return firstSide + zeros + newCode;
}


function restrictNumberInput(){
    var val = "";
    $("input[type='number']").off('keypress').on('keypress', function(e){
        var type = $(e.target).attr('data-negative');

        if(type == 0){
            if((e.which >= 48 && e.which <= 57) || e.which == 0 || e.which == 8){

            } else if(e.which == 44) {
                val = $(e.target).val();
                if(val != "" && val.indexOf('.') == -1) {

                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            if((e.which >= 48 && e.which <= 57) || e.which == 0 || e.which == 8){

            } else if(e.which == 44) {
                val = $(e.target).val();
                if(val != "" && val.indexOf('.') == -1) {

                } else {
                    return false;
                }
            } else if(e.which == 45 || e.which == 45){
                val = $(e.target).val();
                if(val != "") return false;
            } else {
                return false;
            }
        }

    });

}

var colors = {
    black: {title: 'Qara', code: '#000000'},
    white: {title: 'Ağ', code: '#FFFFFF'},
    green: {title: 'Yaşıl', code: '#008000'},
    red: {title: 'Qırmızı', code: '#FF0000'},
    blue: {title: 'Mavi', code: '#0000FF'},
    yellow: {title: 'Sarı', code: 'FFFF00'},
    lime: {title: 'Əhəng', code: '#00FF00'},
    silver: {title: 'Gümüş', code: '#C0C0C0'},
    gray: {title: 'Boz', code: '#808080'},
    maroon: {title: 'Tünd qırmızı', code: '#800000'},
    teal: {title: 'Albalı', code: '008080'}
};

var mainContainerClasses = {
    'small':'col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main',
    'full':'col-sm-12 col-lg-12 main'
};

$(document).ready(function() {

    localcurrencies = $.parseJSON(Base64.decode($('body').data('localcurrencies')));

    $('.hidemenu').click(function(){
        $('.sidebar').clearQueue().animate({left:-300}, 800);
        setTimeout(function(){
            $('.main').attr('class', mainContainerClasses.full);
            $('.showmenu').show();
        }, 500);
    });

    $('.showmenu').click(function(){
        setTimeout(function(){
            $('.main').attr('class', mainContainerClasses.small);
            $('.showmenu').hide();
        },300);
        $('.sidebar').clearQueue().animate({left:0}, 800);
    });

    $(".loader").css("display", "none");
    restrictNumberInput();

    $("a.menuCollapse").unbind().click(function(e){
        e.preventDefault();
        var targetId = $(this).attr("href");
        var signClass = (targetId.replace("#", ".")) + "-sign";

        if($(targetId).hasClass("in")){
            $(targetId).removeClass("in");
            $(signClass).removeClass("glyphicon-minus");
            $(signClass).addClass("glyphicon-plus");

        } else {
            $(targetId).addClass("in");
            $(signClass).removeClass("glyphicon-plus");
            $(signClass).addClass("glyphicon-minus");

        }

    });

    $("[name='sell_code_switcher']").bootstrapSwitch({
        offColor: 'danger',
        onColor: 'success',
        offText: 'Axtar',
        onText: 'Əlavə et'
    });

    $("[name='sell_code_switcher']").on('switchChange.bootstrapSwitch', function (event, state) {
        if(state) {
            $(".sell-goods-code").css("display", "block");
            $(".sell-goods-barcode").css("display", "none");
        } else {
            $(".sell-goods-code").css("display", "none");
            $(".sell-goods-barcode").css("display", "block");
        }
    });

    $(".datepicker").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        startView: 0,
        todayHighlight: true
    });

    /**
     * SSM design issues
     * @type {number}
     */
    var max_fields      = 5; //maximum input boxes allowed
    var wrapper         = $(".after_phone"); //Fields wrapper
    var add_button      = $(".plus-sign"); //Add button ID
    var wrapper1         = $(".tmp").html();
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append(wrapper1);
        }
        $(".minus-sign").click(function(e){ //user click on remove text
            e.preventDefault();
            $(this).closest('.form-group').remove();
            x--;
        })
    });



    var mySelect = $('#first-disabled2');

    $('#special').on('click', function () {
        mySelect.find('option:selected').prop('disabled', true);
        mySelect.selectpicker('refresh');
    });

    $('#special2').on('click', function () {
        mySelect.find('option:disabled').prop('disabled', false);
        mySelect.selectpicker('refresh');
    });

    /*
     $('#basic2').selectpicker({
     liveSearch: true,
     maxOptions: 1
     });
     */

    $('#collapseExampleKontragent [name="currency"]').change(function(){
        _curtxt = $(':selected', this).text();
        $('.auto-currency').text(_curtxt);
    });


    $(".payment").css("display", "none");

    $(".inlineRadio3").click(function(){
        _parent = $(this).parents('dl');

        _parent.find('.discount').show();
        _parent.find('.discount input').removeAttr("disabled").val("").css("border", "1px solid lightgray");
        //$("#client-default").remove();
        _parent.find('[name^="client"]').removeClass('hide');
        _parent.find('.payment').hide();
        _parent.find('[name^="client"]').removeClass('hide');
        _parent.find('.returnings').show();
         submitable = false;
    });

    $(".inlineRadio2").click(function(){

        _parent = $(this).parents('dl');

        _parent.find('.payment').show();
        _parent.find('[name^="client"]').removeClass('hide');
        _parent.find('.discount, .discount_remain, .bonus_remain').hide();
        _parent.find('.discount input, .discount_remain input').attr('disabled', 'disabled');
        _parent.find('.returnings').hide();
    });

    $(".inlineRadio1").click(function(){
        _parent = $(this).parents('dl');

        _parent.find('[name^="debtamount"]').val('');
        _parent.find('.payment').hide();
        _parent.find('[name^="client"]').addClass('hide');
        _parent.find('.discount, .discount_remain, .bonus_remain').hide();
        _parent.find('.discount input, .discount_remain input').attr('disabled', 'disabled');
        _parent.find('.returnings').show();

    });

    $(".minus-sign").click(function(e){ //user click on remove text
        e.preventDefault();
        $(this).closest('.form-group').remove();
        x--;
    });

    // SSM design


    /**
     * Delete baza element
     */
    $(".delete-baza").click(function(e){
        var bazaId = $(this).attr("data-rel");
        dhtmlx.confirm({
            title: "Silməni təsdiqlə",
            text: "Obyekt silinməsini təsdiqləyin",
            ok: "Təsdiq",
            cancel: "İmtina",
            callback: function(res){
                if(res){

                    if(bazaId > 0){
                        $("#deleteBaza" + bazaId).submit();
                    }
                }
            }
        });
    });
    // Delete baza element

    /**
     * Delete contragent
     */
    $(".delete-contragent").click(function(e){
        var contragentId = $(this).attr("data-rel");
        dhtmlx.confirm({
            title: "Silməni təsdiqlə",
            text: "Kontragent silinməsini təsdiqləyin",
            ok: "Təsdiq",
            cancel: "İmtina",
            callback: function(res){
                if(res){

                    if(contragentId > 0){
                        $("#deleteContragent" + contragentId).submit();
                    }
                }
            }
        });
    });
    // Delete contragent

    /**
     * Delete client
     */
    $(".delete-client").unbind().click(function(e){
        var clientId = $(this).attr("data-rel");
        dhtmlx.confirm({
            title: "Silməni təsdiqlə",
            text: "Müştəri silinməsini təsdiqləyin",
            ok: "Təsdiq",
            cancel: "İmtina",
            callback: function(res){
                if(res){

                    if(clientId > 0){
                        $("#deleteClient" + clientId).submit();
                    }
                }
            }
        });
    });
    // Delete client

    /**
     * Client image set
     */
    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.clientImagePreview' + $(input).attr("data-rel")).attr('src', e.target.result);
                $('.clientImagePreview' + $(input).attr("data-rel")).css("display", "block");
            }
            reader.readAsDataURL(input.files[0]);
        }

    }

    $(".clientImage").unbind().change(function(){
        readURL(this);
    });
    // Client image set

    /**
     *  Client images
     */

    var maxImageCount = 5;
    var currentNum = 0;
    $(".plusImage").unbind().click(plusImage);

    function plusImage(e){
        var currentImageCount = $(".img-set").length - 1;
        if(currentImageCount >= maxImageCount) return;
        var lastElement = $(".img-set").last().find(".clientImage");
        var currentNum =  parseInt($(lastElement).attr("data-rel")) + 1;
        var htmlData = '<div class="form-group img-set img-set-' + currentNum + '">' + $(".img-set").html() + '</div>';

        htmlData = htmlData.replace(/clientImagePreview/g, "clientImagePreview" + currentNum);
        htmlData = htmlData.replace(/data-rel="0"/g, 'data-rel="' + currentNum + '"');

        $(".img-set-container").append(htmlData);
        $(".minusImage").unbind().click(function(e){
            var dataRel = $(this).attr("data-rel");
            if(dataRel == 0 || dataRel == 1000){
                $(".clientImagePreview" + dataRel).css("display", "none");
                $("#clientImageReq" + dataRel).val("");
                $("#clientImageReq" + dataRel).removeAttr("disabled");
                $("#clientImageOld" + dataRel).remove();
                return;
            }
            $(".img-set-" + dataRel).remove();
        });

        $(".clientImage").unbind().change(function(){
            readURL(this);
        });
    }

    $(".minusImage").unbind().click(minusImage);

    function minusImage(e){
        var dataRel = $(this).attr("data-rel");
        if(dataRel == 0){
            $(".clientImagePreview" + dataRel).css("display", "none");
            $("#clientImageReq" + dataRel).val("");
            $("#clientImageReq" + dataRel).removeAttr("disabled");
            $("#clientImageOld" + dataRel).remove();
            $(".oldImage" + dataRel).remove();
            return;
        }
        $(".img-set-" + dataRel).remove();
    }
    // Client images

    /**
     * Delete client
     */
    $(".delete-client").unbind().click(function(e){
        var clientId = $(this).attr("data-rel");
        dhtmlx.confirm({
            title: "Silməni təsdiqlə",
            text: "Müştəri silinməsini təsdiqləyin",
            ok: "Təsdiq",
            cancel: "İmtina",
            callback: function(res){
                if(res){

                    if(clientId > 0){
                        $("#deleteClient" + clientId).submit();
                    }
                }
            }
        });
    });
    // Delete client

    /**
     *  Subjects
     */

    var goodsData = [];
    var bycode = null;
    $("#goods_code").keyup(function(e){

        if($(this).attr('rel') == 'sell') return;
        if(bycode != null) bycode.abort();
        var goodsCode = $(this).val(),
            goodsType = $("#goods_type").val(),
            userId = $("#user_id").val();

        bycode = $.ajax({
            url: URL + "/goods/bycode",
            type: "POST",
            data: "code=" + goodsCode + "&goods_type=" + goodsType + "&user_id=" + userId,
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();
                if(response.status){
                    var option = "";
                    $(response.data).each(function(key, val){
                        goodsData[key] = val;
                        option += '<option value="' + val.gid + '">' + val.str + '</option>';
                    });
                    $("#goods_id").html(option);
                    goodsChosen();
                } else {
                    lastGoods = null;
                    $("#goods_id").html('<option value="0">Malı seç</option>');
                }
            }
        });

    });

    $("#goods_id").change(goodsChosen);

    function goodsChosen(e){

        var cid = $("#goods_id").prop("selectedIndex"),
            count = $("#count"),
            buyPrice = $("#buy_price"),
            buyPriceAZN = $("#buy_price_azn"),
            sellPrice = $("#sell_price"),
            goodsCode = $("#goods_code_val"),
            barcode = $("#barcode"),
            totalBuyPrice = $("#total_buy_price"),
            totalSellPrice = $("#total_sell_price"),
            currency = $("#currency_id"),
            currency_archive = $("#currency_archive");

        count.val(1);

        if(goodsData[cid]){
            var total = 1 * goodsData[cid].buy_price;
            totalBuyPrice.val(total);
            buyPrice.val(goodsData[cid].buy_price);
            sellPrice.val(goodsData[cid].sell_price);
            goodsCode.val(goodsData[cid].code);
            barcode.val(goodsData[cid].barcode);

            total = 1 * goodsData[cid].sell_price;
            totalSellPrice.val(total);
            currency.val(goodsData[cid].currency);
            currency_archive.val(currency.find(':selected').attr('accesskey'));

            currency.attr('disabled', 'disabled');
        } else {

            count.val(1);
            buyPrice.val(0);
            sellPrice.val(0);
            totalBuyPrice.val(0);
            totalSellPrice.val(0);
            currency.val(0).trigger('change');
            goodsCode.val("");
            barcode.val("");
            currency.removeAttr('disabled');
        }

        $('.live-currency').text(currency.find(':selected').text());
    }

    $("#date").keyup(function(){return false});
    $("#date").keydown(function(){return false});

    $("#count").keyup(countChange1);
    $("#count").change(countChange1);
    $("#buy_price").keyup(countChange1);
    $("#buy_price").change(countChange1);
    $("#sell_price").keyup(countChange1);
    $("#sell_price").change(countChange1);

    function countChange1(e){

        var count = $("#count").val(),
            buyPrice = $("#buy_price").val(),
            sellPrice = $("#sell_price").val(),
            totalBuyPrice = $("#total_buy_price"),
            totalSellPrice = $("#total_sell_price");


        totalBuyPrice.val(count * buyPrice);
        totalSellPrice.val(count * sellPrice);

    }

    $("#contragent_id").change(function(e){

        var contragentName = $("#contragent_id option:selected").text();
        $("#contragent").val(contragentName);

    });


    // Subjects

    /**
     *  Store
     */

    $("#add_goods").click(addStoreObject);

    function addStoreObject(event){

        var
            contragnet_idField = $("#contragent_id"),
            dateField = $("#date"),
            notesField = $("#notes"),
            user_id = $("#user_id").val(),
            subject_id = $("#subject_id").val(),
            barcode = $("#barcode").val(),
            goods_code = $("#goods_code_val").val(),
            short_info = $("#goods_id option:selected").text(),
            goods_id = $("#goods_id").val(),
            currency = $("#currency_id").val(),
            currency_archive = $("#currency_archive").val(),
            count = parseInt($("#count").val(), 10),
            buy_price = $("#buy_price").val(),
            sell_price = $("#sell_price").val(),
            contragent = $("#contragent").val(),
            operator = $("#operator").val(),
            notes = notesField.val(),
            date = dateField.val(),
            invoice_type = $("#invoice_type").val(),
            invoice_serial = $("#invoice_serial").val(),
            contragent_id = contragnet_idField.val();

        if($('#goods_preview').children(':not(:empty)').length && !$('#goods_preview .delete_goods[data-currency="'+currency+'"]').length)
        {
            invoice_serial = generateNextInvoiceSerial(invoice_serial);
            $("#invoice_serial").val(invoice_serial);
        }

        if(goods_id <= 0) return false;

        contragnet_idField.attr("disabled", "disabled"),
        dateField.attr("disabled", "disabled"),
        notesField.attr("disabled", "disabled");

        $.ajax({
            url: URL + "/store/add",
            type: "POST",
            data: {
                user_id: user_id,
                subject_id: subject_id,
                barcode: barcode,
                goods_code: goods_code,
                short_info: short_info,
                goods_id: goods_id,
                count: count,
                buy_price: buy_price,
                sell_price: sell_price,
                contragent: contragent,
                notes: notes,
                date: date,
                invoice_type: invoice_type,
                invoice_serial: invoice_serial,
                contragent_id: contragent_id,
                operator: operator,
                currency: currency,
                currency_archive: currency_archive
            },
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();
                if(response.status > 0){
                    console.log(response);
                    response.data.buy_price = (response.data.buy_price*1).toFixed(2);
                    response.data.sell_price = (response.data.sell_price*1).toFixed(2);
                    response.data.currency = response.data.currency*1;

                    var table = $("#goods_preview"),
                        data = response.data,
                        total_buy = data.buy_price * data.count,
                        total_sell = data.sell_price * data.count,
                        row = "<tr>\
                                <th><span class='pending_object'></span></th>\
                                <th>" + data.goods_code + "</th>\
                                <th>" + data.barcode + "</th>\
                                <th>" + data.short_info + "</th>\
                                <th>" + data.count + "</th>\
                                <th>" + (data.currency?data.buy_price:'') + "</th>\
                                <th>" + (!data.currency?data.buy_price:'') + "</th>\
                                <th>" + (data.currency?data.sell_price:'') + "</th>\
                                <th>" + (!data.currency?data.sell_price:'') + "</th>\
                                <th>" + (data.currency?total_buy.toFixed(2):'') + "</th>\
                                <th>" + (!data.currency?total_buy.toFixed(2):'') + "</th>\
                                <th>" + (data.currency?total_sell.toFixed(2):'') + "</th>\
                                <th>" + (!data.currency?total_sell.toFixed(2):'') + "</th>\
                                <th>\
                                <span class='delete_button_container'>\
                                    <button class='btn btn-danger delete_goods'\
                                     data-user-id='" + user_id + "'\
                                     data-item-id='" + data.item_id + "'\
                                     data-subject-id='" + subject_id + "'\
                                     data-goods-id='" + goods_id + "'\
                                     data-buy-price='" + buy_price + "'\
                                     data-sell-price='" + sell_price + "'\
                                     data-invoice-id='" + data.invoice_id + "'\
                                     data-count='" + data.count + "'\
                                     data-currency='" + data.currency + "'\
                                     >Sil</button>\
                                </span>\
                                </th>\
                               </tr>";

                    $(".contragent_id").val(contragent_id);

                    if(!$('.activeforms [name="invoice_id['+data.currency+']"]').length){
                        $('.activeforms').append('<input type="hidden" name="invoice_id['+data.currency+']" class="invoice_id" value="'+data.invoice_id+'">');
                        $('.activeforms').append('<input type="hidden" name="invoice_archive['+data.currency+']" value="'+data.currency_archive+'">');
                    }

                    if(data.currency){
                        $("#result_buy_price").text((parseFloat($("#result_buy_price").text() || 0) + parseFloat(total_buy)).toFixed(2));
                        $("#result_sell_price").text((parseFloat($("#result_sell_price").text() || 0) + parseFloat(total_sell)).toFixed(2));
                    }
                    else{
                        $("#result_buy_price_azn").text((parseFloat($("#result_buy_price_azn").text() || 0) + parseFloat(total_buy)).toFixed(2));
                        $("#result_sell_price_azn").text((parseFloat($("#result_sell_price_azn").text() || 0) + parseFloat(total_sell)).toFixed(2));
                    }

                    var exclude = $("#goods_preview").attr("data-exclude");
                    var tmpDiv = $('<div>').append(row);
                    if(typeof exclude !== typeof undefined && exclude !== false){
                        exclude = exclude.split(",");
                        $(exclude).each(function(key, val){
                            $(tmpDiv).find("th:nth-child(" + val + "),td:nth-child(" + val + ")").remove();
                        });
                        row = tmpDiv.html();
                        tmpDiv.remove();
                    }
                    table.append(row);

                    $(".pending_object").each(function(index, item){

                        $(item).html(index + 1);

                    });

                    if($(".pending_object").length > 1){

                        $(".delete_button_container").css("display", "block");

                    } else {
                        $(".delete_button_container").css("display", "none");
                    }

                    $(".delete_goods").unbind().click(deleteStoreObject);

                }
            }
        });
    }

    function deleteStoreObject(event){

        var button = $(this);

        dhtmlx.confirm({
            title: "Silinmə",
            text: "Obyektin silinməsini təsdiqləyin",
            ok: "Təsdiq",
            cancel: "İmtina",
            callback: function(response){

                if(response){
                    var postData = {
                        user_id: button.attr("data-user-id"),
                        item_id: button.attr("data-item-id"),
                        subject_id: button.attr("data-subject-id"),
                        goods_id: button.attr("data-goods-id"),
                        buy_price: button.attr("data-buy-price"),
                        sell_price: button.attr("data-sell-price"),
                        invoice_id: button.attr("data-invoice-id"),
                        count: button.attr("data-count")
                    };

                    $.ajax({
                        url: URL + "/store/delete",
                        type: "POST",
                        data: postData,
                        dataType: "JSON",
                        beforeSend: function(xhr){
                            Loader.lStart(xhr);
                        },
                        success: function(response){
                            Loader.lStop();

                            if(response.status > 0){

                                button.closest("tr").remove();

                                var total_buy = parseFloat(postData.count) * parseFloat(postData.buy_price),
                                    total_sell = parseFloat(postData.count) * parseFloat(postData.sell_price),
                                    total_buy_priceField = $("#result_buy_price"),
                                    total_sell_priceField = $("#result_sell_price"),
                                    total_buy_price = parseFloat(total_buy_priceField.html()) - parseFloat(total_buy),
                                    total_sell_price = parseFloat(total_sell_priceField.html()) - parseFloat(total_sell);

                                total_buy_priceField.text(total_buy_price.toFixed(2));
                                total_sell_priceField.text(total_sell_price.toFixed(2));

                                $(".pending_object").each(function(index, item){

                                    $(item).html(index + 1);

                                });

                                if($(".pending_object").length > 1){

                                    $(".delete_button_container").css("display", "block");

                                } else {
                                    $(".delete_button_container").css("display", "none");
                                }

                                $(".delete_goods").unbind().click(deleteStoreObject);

                            }

                        }

                    });
                }

            }

        });

    }

    /**
     *  Catalog goods delete
     */

    $(".catalog-goods-delete").unbind().click(function(event){

        var btn = $(event.target);
        dhtmlx.confirm({
            title: "Silinmə",
            text: "Silinməni təsdiqlə",
            ok: "Təsdiqlə",
            cancel: "İmtina",
            callback: function(response){
                if(response){
                    $(btn).closest("form").submit();
                }
            }
        });

    });

    //  Catalog goods delete

    $(".delete_goods").unbind().click(deleteStoreObject);

    // Store

    /**
     *  Categories
     */

    $(".delete-category").click(function(event){

        dhtmlx.confirm({
            title: "Silinmə",
            text: "Obyektin silinməsini təsdiqləyin",
            ok: "Təsdiq",
            cancel: "İmtina",
            callback: function(response){
                if(response) $(".delete-category").closest("form").submit();
            }
        });

    });

    // Categories

    /**
     *  ReturnGoods search
     */
    /**
     *  Sell search
     */
    var returnGoodsSearchXHR = null;
    $("#return-goods-search").unbind().click(function(event){

        var searchLine = $("#return_goods_code").val(),
            userId = $("#user_id").val(),
            subjectId = $("#subject_id").val();

        if(searchLine == "") {
            $("#search_code").css("border", "3px solid red");
            return;
        } else {
            $("#search_code").css("border", "none");
        }

        if(returnGoodsSearchXHR !== null) returnGoodsSearchXHR.abort();

        returnGoodsSearchXHR = $.ajax({
            url: URL + "/sell/return/search",
            type: "POST",
            data: {
                search_code : searchLine,
                user_id : userId,
                subject_id : subjectId
            },
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                if(response.status > 0){

                    var html = "", i = 0;
                    $(response.data).each(function(index, value){
                        i++;
                        var cname = "Alıcı";
                        if(value.cname != null) cname = value.cname;
                        html += '<tr id="' + value.id + '">\
                            <th class="order-num">' + i + '</th>\
                            <th class="client">' + cname + '</th>\
                            <th>' + value.goods_code + '</th>\
                            <th>' + value.barcode + '</th>\
                            <th><a href="javascript:void(0)" data-toggle="modal" data-target="#goods-info"  class="goods-info" data-rel="' + value.store_item_id + '">' + value.short_info + '</a></th>\
                            <th class="count" style="display: none;"></th>\
                            <th>' + value.buy_price + '</th>\
                            <th class="sell-price">' + value.sell_price + '</th>\
                            <th class="currency">' + value.currency + '</th>\
                            <th class="date">' + value.date + '</th>\
                            <th class="plus-button">\
                                <button type="button" data-rel="' + value.id + '" data-store-item-id="' + value.store_item_id + '" class="btn btn-primary return-goods-add">\
                                <span class="glyphicon glyphicon-plus"></span>\
                                </button>\
                            </th>\
                        </tr>';
                    });

                    $("#sell-search-table").css("display", "block");
                    $("#sell-search-table-tbody").html(html);

                    $(".return-goods-add").unbind().click(addToReturnPendings);
                    $(".goods-info").unbind().click(getGoodsInfo);

                    var exclude = $("#sell-search-table table").attr("data-exclude");
                    if (typeof exclude !== typeof undefined && exclude !== false) {
                        $("#sell-search-table table").find("tr th:nth-child(" + exclude + ")").each(function(key, val){
                            $(val).hide();
                        });
                        $("#sell-search-table table").find("tr td:nth-child(" + exclude + ")").each(function(key, val){
                            $(val).hide();
                        });
                    }

                } else {
                    html += '<tr>\
                            <th colspan="9">Axtarış parameterlərinə uyğun nəticə yoxdur</th>\
                        </tr>';
                    $("#sell-search-table-tbody").html(html);
                }

            }
        });

    });


    /**
     * Return to contragent
     * @type {null}
     */

    var returnContragentSearchXHR = null;
    $("#return-contragent-search").unbind().click(function(event){

        var searchLine = $("#return_contragent_code").val(),
            userId = $("#user_id").val(),
            subjectId = $("#subject_id").val();

        if(searchLine == "") {
            $("#return_contragent_code").css("border", "3px solid red");
            return;
        } else {
            $("#return_contragent_code").css("border", "none");
        }

        if(returnContragentSearchXHR !== null) returnContragentSearchXHR.abort();

        returnContragentSearchXHR = $.ajax({
            url: URL + "/stock/search",
            type: "POST",
            data: {
                search_code : searchLine,
                user_id : userId,
                subject_id : subjectId
            },
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                if(response.status > 0){

                    var html = "", i = 0;
                    $(response.data).each(function(index, value){
                        i++;
                        var cname = "Alıcı";
                        if(value.cname != null) cname = value.cname;
                        html += '<tr id="' + value.id + '">\
                            <th class="order-num">' + i + '</th>\
                            <th class="client">' + cname + '</th>\
                            <th>' + value.goods_code + '</th>\
                            <th>' + value.barcode + '</th>\
                            <th><a href="javascript:void(0)" data-toggle="modal" data-target="#goods-info"  class="goods-info" data-rel="' + value.id + '">' + value.short_info + '</a></th>\
                            <th class="count">' + value.count + '</th>\
                            <th data-buyprice="'+value.buy_price+'" data-currency="'+value.currency+'" class="sell-price">' + value.buy_price + ' ' + (value.currency?(value.currency+' <br/> (<font color="red">'+value.currency_archive+'</font>)'):'AZN') + '</th>\
                            <th>' + value.sell_price + '</th>\
                            <th class="plus-button">\
                                <button type="button" data-rel="' + value.id + '" class="btn btn-primary return-contragent-add">\
                                <span class="glyphicon glyphicon-plus"></span>\
                                </button>\
                            </th>\
                        </tr>';
                    });

                    $("#sell-search-table").css("display", "block");
                    $("#sell-search-table-tbody").html(html);

                    // var exclude = $("#sell-search-table table").attr("data-exclude");
                    // if (typeof exclude !== typeof undefined && exclude !== false) {
                    //     $("#sell-search-table table").find("tr th:nth-child(" + exclude + ")").each(function(key, val){
                    //         $(val).hide();
                    //     });
                    //     $("#sell-search-table table").find("tr td:nth-child(" + exclude + ")").each(function(key, val){
                    //         $(val).hide();
                    //     });
                    // }

                    $(".return-contragent-add").unbind().click(addToReturnContragentPendings);
                    $(".goods-info").unbind().click(getGoodsInfo);



                } else {
                    html += '<tr>\
                            <th colspan="9">Axtarış parameterlərinə uyğun nəticə yoxdur</th>\
                        </tr>';
                    $("#sell-search-table").css("display", "block");
                    $("#sell-search-table-tbody").html(html);
                }

            }
        });

    });

    function addToReturnContragentPendings(event){

        $("#sell-search-table").css("display", "none");
        var objId = $(this).attr("data-rel"),
            row = '<tr id="pending' + objId + '">' + $("tr#" + objId).html() + '</tr>';

        if($("tr#pending" + objId).length > 0){
            $("#count" + objId).val(parseInt($("#count" + objId).val()) + 1);
            $("tr#pending" + objId + " .pending-sell-total").html((parseInt($("#count" + objId).val()) * parseFloat($("tr#pending" + objId + " .sell-price").html())).toFixed(2));

        } else {
            var buyPrice = parseFloat($(row).find(".sell-price").data('buyprice')),
                sellPrice = parseFloat($(row).find(".sell-price + th").text()),
                cur_currency = $(row).find(".sell-price").data('currency'),
                _count = parseInt( $(row).find(".count").text());

            row = $(row).find(".plus-button").remove().end();
            row = $(row).find(".date").remove().end();
            row = $(row).find(".client").remove().end();
            row = $(row).find('.sell-price + th').remove().end();
            row = $(row).find('.sell-price').remove().end();

            row = $(row).append('<th class="buy-price" data-currency="'+cur_currency+'">' + (cur_currency?buyPrice:'') + '</th>');
            row = $(row).append('<th class="buy-price-azn">' + (!cur_currency?buyPrice:'') + '</th>');
            row = $(row).append('<th class="sell-price">' + (cur_currency?sellPrice:'') + '</th>');
            row = $(row).append('<th class="sell-price-azn">' + (!cur_currency?sellPrice:'') + '</th>');
            row = $(row).append('<th class="total_sell_price">' + (cur_currency?buyPrice:'') + '</th>');
            row = $(row).append('<th class="total_sell_price_azn">' + (!cur_currency?buyPrice:'') + '</th>');

            row = $(row).append('<th>'
                + '<button type="button" data-rel="' + objId + '" class="btn btn-danger return-pending-delete">'
                + 'Sil'
                + '</button></th>');

            $("#return-pendings").append(row);

            $(".return-pending-delete").unbind().click(deleteFromReturnPendings);

            $("tr#pending" + objId + " .count").html('<input type="number" data-rel="' + objId + '" class="form-control" id="count' + objId + '" name="count['+(cur_currency?cur_currency.toLowerCase():'azn')+'][' + objId + ']" value="1">');
            $("tr#pending" + objId + " .count").css("display", "table-cell")
            $(".goods-info").unbind().click(getGoodsInfo);
            $("#return-pendings .order-num").each(function(key, val){
                $(val).html(parseInt(key) + 1);
            });

            $("tr#pending" + objId + " .count input[type='number']").unbind().keyup(countChangeReturnContragent).change(countChangeReturnContragent);

        }

        reCallReturn();

        if($("#return-pendings .order-num").length <= 0) {
            $("#confirm-return-contragent").attr("disabled", "disabled");
        } else {
            $("#confirm-return-contragent").removeAttr("disabled");
        }

        var exclude = $("#return-pendings").attr("data-exclude");
        if (typeof exclude !== typeof undefined && exclude !== false) {
            $("#return-pendings").find("tr th:nth-child(" + exclude + ")").each(function(key, val){
                $(val).html("");
                $(val).show();
            });
            $("#return-pendings").find("tr td:nth-child(" + exclude + ")").each(function(key, val){
                $(val).html("");
                $(val).show();
            });
        }

    }

    function countChangeReturnContragent(event){

        var objId = $(this).attr("data-rel");

        if($(this).val() == "" || parseInt($(this).val()) <= 0){
            $("#confirm-return-contragent").attr("disabled", "disabled");
            $(this).css("border", "red 1px solid");
            return false;
        } else {
            $("#confirm-return-contragent").removeAttr("disabled");
            $(this).css("border", "none");
        }

        _count = parseInt($("#count" + objId).val());
        _currency = $("tr#pending" + objId + " .buy-price").data('currency');
        _amount = $("tr#pending" + objId + " .buy-price"+(!_currency?'-azn':'')).text() || 0;

        $("tr#pending" + objId + " .total_sell_price"+(!_currency?'_azn':'')).text((_amount*_count).toFixed(2));

       reCallReturn();

    }

    function reCallReturn(){
        var totalPrice = 0, totalPriceAZN = 0;
        $(".total_sell_price").each(function(key, val){
            totalPrice += parseFloat($(val).html() || 0);
        });
        $(".total_sell_price_azn").each(function(key, val){
            totalPriceAZN += parseFloat($(val).html() || 0);
        });

        $(".total_all_price").text(totalPrice.toFixed(2));
        $(".total_all_price_azn").text(totalPriceAZN.toFixed(2));

        $("#total_amount").val((totalPrice).toFixed(2));
        $("#total_amount_azn").val((totalPriceAZN).toFixed(2));
    }

    var approveReturnContragent = null;
    $("#confirm-return-contragent").unbind().click(function(event){
        event.preventDefault();

        $(this).attr("disabled", "disabled");
        var data = $("#returnContragentInvoiceForm").serialize() + '&' + $("#returnContragentForm").serialize();

        if(approveReturnContragent !== null) approveReturnContragent.abort();

        approveReturnContragent = $.ajax({
            url : URL + '/contragent/return/approve',
            type: "POST",
            data: data,
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                document.location = URL + "/contragent/return";

            }
        });

    });

    // Return to contragent

    var stockGoodsSearchXHR = null;
    $("#stock-goods-search").unbind().click(function(event){

        var searchLine = $("#stock_goods_code").val(),
            userId = $("#user_id").val(),
            subjectId = $("#subject_id").val();

        if(searchLine == "") {
            $("#search_code").css("border", "3px solid red");
            return;
        } else {
            $("#search_code").css("border", "none");
        }

        if(stockGoodsSearchXHR !== null) stockGoodsSearchXHR.abort();

        stockGoodsSearchXHR = $.ajax({
            url: URL + "/stock/search",
            type: "POST",
            data: {
                search_code : searchLine,
                user_id : userId,
                subject_id : subjectId
            },
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                if(response.status > 0){

                    var html = "", i = 0;
                    $(response.data).each(function(index, value){
                        i++;
                        var cname = "Alıcı";
                        if(value.cname != null) cname = value.cname;
                        html += '<tr id="' + value.id + '">\
                            <th class="order-num">' + i + '</th>\
                            <th>' + value.goods_code + '</th>\
                            <th>' + value.barcode + '</th>\
                            <th><a href="javascript:void(0)" data-toggle="modal" data-target="#goods-info"  class="goods-info" data-rel="' + value.id + '">' + value.short_info + '</a></th>\
                            <th class="count">' + value.count + '</th>\
                            <th>' + value.buy_price + '</th>\
                            <th class="sell-price">' + value.sell_price + '</th>\
                            <th class="currency">' + (value.currency || 'AZN') + '</th>\
                            <th class="plus-button">\
                                <button type="button" data-rel="' + value.id + '" class="btn btn-primary stock-goods-add">\
                                <span class="glyphicon glyphicon-plus"></span>\
                                </button>\
                            </th>\
                        </tr>';
                    });

                    $("#sell-search-table").css("display", "block");
                    $("#sell-search-table-tbody").html(html);

                    $(".stock-goods-add").unbind().click(addToStockPendings);
                    $(".goods-info").unbind().click(getGoodsInfo);



                } else {
                    html += '<tr>\
                            <th colspan="8">Axtarış parameterlərinə uyğun nəticə yoxdur</th>\
                        </tr>';
                    $("#sell-search-table-tbody").html(html);
                }

            }
        });

    });

    function addToStockPendings(event){

        $("#sell-search-table").css("display", "none");
        var objId = $(this).attr("data-rel"),
            row = '<tr id="pending' + objId + '">' + $("tr#" + objId).html() + '</tr>';


        if($("tr#pending" + objId).length > 0){

            $("#count" + objId).val(parseInt($("#count" + objId).val()) + 1);
            $("tr#pending" + objId + " .pending-sell-total").html((parseInt($("#count" + objId).val()) * parseFloat($("tr#pending" + objId + " .sell-price").html())).toFixed(2));

        } else {
            curr_txt = $(row).find(".currency").text().toLowerCase();
            cur_currency = curr_txt == 'azn'?false:true;
            buyPrice = parseFloat($(row).find(".count + th").text());
            sellPrice = parseFloat($(row).find(".sell-price").text());

            row = $(row).find(".plus-button").remove().end();
            row = $(row).find(".date").remove().end();
            row = $(row).find(".client").remove().end();
            row = $(row).find(".currency").remove().end();
            row = $(row).find(".count + th").remove().end();
            row = $(row).find(".sell-price").remove().end();

            row = $(row).append('<th class="buy-price" data-currency="'+cur_currency+'">' + (cur_currency?buyPrice:'') + '</th>');
            row = $(row).append('<th class="buy-price-azn">' + (!cur_currency?buyPrice:'') + '</th>');
            row = $(row).append('<th class="sell-price">' + (cur_currency?sellPrice:'') + '</th>');
            row = $(row).append('<th class="sell-price-azn">' + (!cur_currency?sellPrice:'') + '</th>');
            row = $(row).append('<th class="total_sell_price">' + (cur_currency?sellPrice:'') + '</th>');
            row = $(row).append('<th class="total_sell_price_azn">' + (!cur_currency?sellPrice:'') + '</th>');
            row = $(row).append('<th>'
                + '<button type="button" data-rel="' + objId + '" class="btn btn-danger return-pending-delete">'
                + 'Sil'
                + '</button></th>');


            $("#return-pendings").append(row);

            $(".return-pending-delete").unbind().click(deleteFromReturnPendings);

            $("tr#pending" + objId + " .count").html('<input name="ids['+objId+']" value="1" type="hidden"/><input type="number" data-rel="' + objId + '" class="form-control" id="count' + objId + '" name="count['+curr_txt+'][' + objId + ']" value="1">');
            $("tr#pending" + objId + " .count").css("display", "table-cell")
            $(".goods-info").unbind().click(getGoodsInfo);
            $("#return-pendings .order-num").each(function(key, val){
                $(val).html(parseInt(key) + 1);
            });

            $("tr#pending" + objId + " .count input[type='number']").unbind().keyup(countChange).change(countChange);

        }

        reCallReturn();

        if($("#return-pendings .order-num").length <= 0) {
            $("#confirm-return-button").attr("disabled", "disabled");
        } else {
            $("#confirm-return-button").removeAttr("disabled");
        }

    }

    var approveStockGoods = null;
    function approveStockTransfer(event){

        event.preventDefault();
        $(event.target).attr("disabled", "disabled");
        var data = $("#stockGoodsInvoiceForm").serialize() + '&' + $("#stockGoodsForm").serialize();

        if(approveStockGoods !== null) approveStockGoods.abort();
        approveStockGoods = $.ajax({
            url : URL + '/stock/transfer/approve',
            type: "POST",
            data: data,
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                document.location = URL + "/stock";

            }
        });
    }

    function addToReturnPendings(event){

        $("#sell-search-table").css("display", "none");
        var objId = $(this).attr("data-rel"),
            row = '<tr id="pending' + objId + '">' + $("tr#" + objId).html() + '</tr>',
            storeItemId = $(this).attr("data-store-item-id");

        $("#confirm-return-button").removeAttr("disabled");

        _currentElm = $('#returnGoodsForm [name="ids['+storeItemId+']"]');

        if(_currentElm.length){
            _newcount = parseInt(_currentElm.next().val()) + 1;
            _currentElm.next().val(_newcount).trigger('change');
        } else {
            curr_txt = $(row).find(".currency").text().toLowerCase();
            cur_currency = curr_txt == 'azn'?false:true;
            buyPrice = parseFloat($(row).find(".count + th").text());
            sellPrice = parseFloat($(row).find(".sell-price").text());

            row = $(row).find(".plus-button").remove().end();
            row = $(row).find(".currency").remove().end();
            row = $(row).find(".count + th").remove().end();
            row = $(row).find(".sell-price").remove().end();
            row = $(row).find(".date").remove().end();
            row = $(row).find(".client").remove().end();

            row = $(row).append('<th class="buy-price" data-currency="'+cur_currency+'">' + (cur_currency?buyPrice:'') + '</th>');
            row = $(row).append('<th class="buy-price-azn">' + (!cur_currency?buyPrice:'') + '</th>');
            row = $(row).append('<th class="sell-price">' + (cur_currency?sellPrice:'') + '</th>');
            row = $(row).append('<th class="sell-price-azn">' + (!cur_currency?sellPrice:'') + '</th>');
            row = $(row).append('<th class="total_sell_price">' + (cur_currency?sellPrice:'') + '</th>');
            row = $(row).append('<th class="total_sell_price_azn">' + (!cur_currency?sellPrice:'') + '</th>');
            row = $(row).append('<th>'
                + '<button type="button" data-rel="' + objId + '" class="btn btn-danger return-pending-delete">'
                + 'Sil'
                + '</button></th>');

            var exclude = $("#return-pendings").attr("data-exclude");
            var tmpDiv = $('<div>').append(row);
            if(typeof exclude !== typeof undefined && exclude !== false){
                exclude = exclude.split(",");
                $(exclude).each(function(key, val){
                    $(tmpDiv).find("th:nth-child(" + val + "),td:nth-child(" + val + ")").remove();
                });
                row = tmpDiv.html();
                tmpDiv.remove();
            }

            $("#return-pendings").append(row);

            $(".return-pending-delete").unbind().click(deleteFromReturnPendings);

            $("tr#pending" + objId + " .count").html('<input name="ids['+storeItemId+']" value="1" type="hidden"/><input type="number" data-rel="' + objId + '" class="form-control" id="count' + objId + '" name="count['+curr_txt+'][' + storeItemId + ']" value="1">');
            $("tr#pending" + objId + " .count").css("display", "table-cell")
            $(".goods-info").unbind().click(getGoodsInfo);
            $("#return-pendings .order-num").each(function(key, val){
                $(val).html(parseInt(key) + 1);
            });

            $("tr#pending" + objId + " .count input[type='number']").unbind().keyup(countChange).change(countChange);

        }

        reCallReturn();
    }

    function countChange(event){

        var objId = $(this).attr("data-rel");

        if($(this).val() == "" || parseInt($(this).val()) <= 0){
            $("#confirm-return-button").attr("disabled", "disabled");
            $(this).css("border", "red 1px solid");
            return false;
        } else {
            $("#confirm-return-button").removeAttr("disabled");
            $(this).css("border", "none");
        }

        _count = parseInt($("#count" + objId).val());
        _currency = $("tr#pending" + objId + " .buy-price").data('currency');
        _amount = $("tr#pending" + objId + " .sell-price"+(!_currency?'-azn':'')).text() || 0;

        $("tr#pending" + objId + " .total_sell_price"+(!_currency?'_azn':'')).text((_amount*_count).toFixed(2));

        reCallReturn();
    }

    function deleteFromReturnPendings(){

        var objId = $(this).attr("data-rel");
        $("tr#pending" + objId).remove();

        $("#return-pendings .order-num").each(function(key, val){
            $(val).html(parseInt(key) + 1);
        });

        reCallReturn();

        if($("#return-pendings .order-num").length <= 0) {
            $("#confirm-return-button").attr("disabled", "disabled");
        } else {
            $("#confirm-return-button").removeAttr("disabled");
        }

    }

    var approveReturnGoods = null;
    $("#confirm-return-button").unbind().click(function(event){
        event.preventDefault();

        $('[class^="total_all_price"').each(function(){
            _tprice = parseFloat( $(this).text() );
            _tcur = $(this).attr('class').slice('total_all_price_'.length) || 'usd';
            $(".form-horizontal").prepend('<input name="amount['+_tcur+']" type="hidden" value="'+_tprice+'"/>');
        });

        $('[name^="ids"]').each(function(){
            $(this).val($(this).next().val());
        });

        if($(this).attr("data-rel") == 'stock'){
            approveStockTransfer(event);
            return;
        }

        $(this).attr("disabled", "disabled");
        var data = $("#returnGoodsInvoiceForm").serialize() + '&' + $("#returnGoodsForm").serialize();

        if(approveReturnGoods !== null) approveReturnGoods.abort();
        approveReturnGoods = $.ajax({
            url : URL + '/sell/return/approve',
            type: "POST",
            data: data,
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                document.location = URL + "/sell/return";

            }
        });

    });

    // Return pendings

    /**
     *  Sell search
     */

    $("#sell-search-goods").unbind().click(function(event){

        var goodsCode = $("#goods_code").val(),
            userId = $("#user_id").val(),
            subjectId = $("#subject_id").val();

        if(goodsCode == "") {
            $("#goods_code").css("border", "3px solid red");
            return;
        } else {
            $("#goods_code").css("border", "none");
        }

        $.ajax({
            url: URL + "/sell/search",
            type: "POST",
            data: {
                goods_code : goodsCode,
                user_id : userId,
                subject_id : subjectId
            },
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                if(response.status > 0){

                    var html = "", i = 0;
                    $(response.data).each(function(index, value){
                        i++;
                        html += '<tr>\
                            <th>' + i + '</th>\
                            <th>' + value.goods_code + '</th>\
                            <th>' + value.barcode + '</th>\
                            <th><a href="javascript:void(0)" data-toggle="modal" data-target="#goods-info"  class="goods-info" data-rel="' + value.id + '">' + value.short_info + '</a></th>\
                            <th>' + value.count + '</th>\
                            <th>' + value.buy_price + ' ' + (value.currency?(value.currency+' <br/> (<font color="red">'+value.currency_archive+'</font>)'):'AZN') + '</th>\
                            <th>' + value.sell_price + '</th>\
                            <th>\
                                <button type="button" data-rel="' + value.id + '" class="btn btn-primary sell-add">\
                                <span class="glyphicon glyphicon-plus"></span>\
                                </button>\
                            </th>\
                        </tr>';
                    });

                    $("#sell-search-table").css("display", "block");
                    $("#sell-search-table-tbody").html(html);

                    $(".sell-add").unbind().click(addToCart);
                    $(".goods-info").unbind().click(getGoodsInfo);

                    var exclude = $("#sell-search-table table").attr("data-exclude");
                    if (typeof exclude !== typeof undefined && exclude !== false) {
                        $("#sell-search-table table").find("tr th:nth-child(" + exclude + ")").each(function(key, val){
                            $(val).hide();
                        });
                        $("#sell-search-table table").find("tr td:nth-child(" + exclude + ")").each(function(key, val){
                            $(val).hide();
                        });
                    }

                } else {
                    html += '<tr>\
                            <th colspan="8">Axtarış parameterlərinə uyğun nəticə yoxdur</th>\
                        </tr>';
                    $("#sell-search-table-tbody").html(html);
                }

            }
        });

    });

    $("#close-search-table").click(function(e){

        $("#sell-search-table").css("display", "none");

    });

    $("#barcode_reader").change(function(event){
        if($(this).is(":checked")){
            $("#barcode").unbind().keyup(addByBarcode);
        } else {
            $("#barcode").unbind();
        }
    });

    $("#sell-add-goods").unbind().click(addByBarcode);

    function addByBarcode(event){

        $("#barcode").css("border", "none");

        var btn = $(event.target),
            barcode = $("#barcode").val(),
            userId = $("#user_id").val(),
            subjectId = $("#subject_id").val(),
            invoiceSerial = $("#invoice_serial").val(),
            invoiceType = $("#invoice_type").val(),
            date = $("#date").val(),
            notes = $("#notes").val(),
            add_type = 'insert',
            count = 1,
            sell_price = 0,
            operator = $("#operator").val(),
            operator_type = $("#operator_type").val();

        if(operator_type == 0) operator = 0;

        barcode = barcode.trim();
        if(barcode.length <= 0) return;

        $.ajax({
            url : URL + '/store/getbybarcode',
            type: "POST",
            data: {

                barcode: barcode,
                user_id: userId,
                subject_id: subjectId

            },
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();
                if(response.status > 0) {

                    var storeItem = response.data,
                        storeItemId = storeItem.id;

                    if($("tr." + storeItemId).length > 0){
                        add_type = 'update';
                        var tmpID = $("tr." + storeItemId).attr("data-rel");
                        count = $("#pending-count-" + tmpID).val();
                        sell_price = $("#pending-sell-price-" + tmpID).val();
                    }

                    var data = {
                        user_id: userId,
                        subject_id: subjectId,
                        store_item_id: storeItemId,
                        invoice_type: invoiceType,
                        invoice_serial: invoiceSerial,
                        date: date,
                        notes: notes,
                        add_type: add_type,
                        count: count,
                        sell_price: sell_price,
                        sell_id: tmpID,
                        operator: operator
                    };

                    $.ajax({
                        url: URL + "/sell/add",
                        type: "POST",
                        data: data,
                        dataType: "JSON",
                        beforeSend: function(xhr){
                            Loader.lStart(xhr);
                        },
                        success: function(response){
                            Loader.lStop();
                            if(response.status > 0){

                                $("#sell-search-table").css("display", "none");
                                if(add_type == 'update') {

                                    var remCount = $("#remain-count-" + tmpID);
                                    var tmpRemCount = parseInt(remCount.attr("data-real-count")) - parseInt($("#pending-count-" + tmpID).val());
                                    if(tmpRemCount <= 0) return false;

                                    $("#pending-count-" + tmpID).val(parseFloat(count) + 1);
                                    $("#pending-total-" + tmpID).html(((parseFloat(count) + 1) * parseFloat(sell_price)).toFixed(2));

                                    var total = 0;
                                    $(".pending-total").each(function(key, val){
                                        total += parseFloat($(val).html());
                                    });

                                    $(".total_sell_price").html(total.toFixed(2));

                                    $("#barcode").val("").attr("placeholder", "Barkod");

                                    var remCount = $("#remain-count-" + tmpID);
                                    remCount.html(parseInt(remCount.attr("data-real-count")) - parseInt($("#pending-count-" + tmpID).val()));

                                } else {

                                    var item = response.data,
                                        row = '<tr class="' + item.id + '" data-rel="' + item.sell_id + '" id="' + item.sell_id + '">\
                                        <th><span class="order_num"></span></th>\
                                        <th>' + item.goods_code + '</th>\
                                        <th>' + item.barcode + '</th>\
                                        <th><a href="javascript:void(0)" data-toggle="modal" data-target="#goods-info"  class="goods-info" data-rel="' + item.store_item_id + '">' + item.short_info + '</a></th>\
                                        <th data-real-count="' + item.count + '" class="remain-count" id="remain-count-' + item.sell_id + '">' + parseInt(item.count - 1) + '</th>\
                                        <th><input type="number" data-rel="' + item.sell_id + '" id="pending-count-' + item.sell_id + '" class="form-control input-sm pending-count" name="count[]" value="1"></th>\
                                        <th><input type="number" data-rel="' + item.sell_id + '" id="pending-sell-price-' + item.sell_id + '" step="0.01" class="form-control input-sm pending-sell-price" name="sell_price[]" value="' + item.sell_price + '"></th>\
                                        <th><span data-currency="'+item.currency+'" class="pending-total" id="pending-total-' + item.sell_id+ '">' + (item.currency?item.sell_price:'') + '</span></th>\
                                        <th><span data-currency="'+item.currency+'" class="pending-total" id="pending-total-azn' + item.sell_id + '">' + (!item.currency?item.sell_price:'') + '</span></th>\
                                        <th class="sell-delete" data-rel="' + item.sell_id + '"></th>\
                                        </tr>';

                                    $("input[name='invoice_id']").val(item.sell_invoice_id);

                                    $("#sell-pendings").append(row);

                                    $(".order_num").each(function(key, val){
                                        $(val).html(key + 1);
                                    });

                                    if($(".sell-delete").length > 1){

                                        $(".sell-delete").each(function(key, val){
                                            var btn = '<button class="btn btn-danger delete-pending" data-rel="' + $(val).attr("data-rel") + '">Sil</button>';
                                            $(val).html(btn);
                                        });

                                    }

                                    $(".delete-pending").unbind().click(deleteSell);

                                    var totalSellPrice = $(".total_sell_price"+(item.currency?'':'_azn'));
                                    totalSellPrice.html((parseFloat(totalSellPrice.html()) + parseFloat(item.sell_price)).toFixed(2));

                                    $(".goods-info").unbind().click(getGoodsInfo);

                                    $(".pending-count").unbind().bind('keyup mousekeyup change', changePriceCount);
                                    $(".pending-sell-price").unbind().bind('keyup mousekeyup change', changePriceCount);

                                    $("#barcode").val("").attr("placeholder", "Barkod");

                                    restrictNumberInput();

                                }

                            }

                        }
                    });

                } else {
                    $("#barcode").css("border", "red 2px solid");
                }
            }
        })

    }

    function addToCart(event){

        var btn = $(this),
            storeItemId = btn.attr("data-rel"),
            userId = $("#user_id").val(),
            subjectId = $("#subject_id").val(),
            invoiceSerial = $("#invoice_serial").val(),
            invoiceType = $("#invoice_type").val(),
            date = $("#date").val(),
            notes = $("#notes").val(),
            add_type = 'insert',
            count = 1,
            sell_price = 0,
            operator = $("#operator").val();

        if($("tr." + storeItemId).length > 0){
            add_type = 'update';
            var tmpID = $("tr." + storeItemId).attr("data-rel");
            count = $("#pending-count-" + tmpID).val();
            sell_price = $("#pending-sell-price-" + tmpID).val();
        }

        $.ajax({
            url: URL + "/sell/add",
            type: "POST",
            data: {
                user_id: userId,
                subject_id: subjectId,
                store_item_id: storeItemId,
                invoice_type: invoiceType,
                invoice_serial: invoiceSerial,
                date: date,
                notes: notes,
                add_type: add_type,
                count: count,
                sell_price: sell_price,
                sell_id: tmpID,
                operator: operator
            },
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                if(response.status > 0){
                    currency = parseInt(response.data.currency);

                    //$("#sell-search-table").css("display", "none");
                    if(add_type == 'update') {

                        var remCount = $("#remain-count-" + tmpID);
                        var tmpRemCount = parseInt(remCount.attr("data-real-count")) - parseInt($("#pending-count-" + tmpID).val());
                        if(tmpRemCount <= 0) return false;

                        $("#pending-count-" + tmpID).val(parseFloat(count) + 1);
                        $("#pending-total-" + (currency?tmpID:'azn-'+tmpID)).html(((parseFloat(count) + 1) * parseFloat(sell_price)).toFixed(2));

                        calcSell();

                        var remCount = $("#remain-count-" + tmpID);
                        remCount.html(parseInt(remCount.attr("data-real-count")) - parseInt($("#pending-count-" + tmpID).val()));

                    } else {

                        var item = response.data,
                            row = '<tr class="' + item.id + '" data-rel="' + item.sell_id + '" id="' + item.sell_id + '">\
                                <th><span class="order_num"></span></th>\
                                <th>' + item.goods_code + '</th>\
                                <th>' + item.barcode + '</th>\
                                <th><a href="javascript:void(0)" data-toggle="modal" data-target="#goods-info"  class="goods-info" data-rel="' + item.store_item_id + '">' + item.short_info + '</a></th>\
                                <th data-real-count="' + item.count + '" class="remain-count" id="remain-count-' + item.sell_id + '">' + parseInt(item.count - 1) + '</th>\
                                <th><input type="number" data-rel="' + item.sell_id + '" id="pending-count-' + item.sell_id + '" class="form-control input-sm pending-count" name="count[]" value="1"></th>\
                                <th><input type="number" data-rel="' + item.sell_id + '" id="pending-sell-price-' + item.sell_id + '" step="0.01" class="form-control input-sm pending-sell-price" name="sell_price[]" value="' + item.sell_price + '"></th>\
                                <th><span data-currency="'+item.currency+'" class="pending-total" id="pending-total-' + item.sell_id + '">' + (currency?item.sell_price:'') + '</span></th>\
                                <th><span data-currency="'+item.currency+'" class="pending-total" id="pending-total-azn-' + item.sell_id + '">' + (!currency?item.sell_price:'') + '</span></th>\
                                <th class="sell-delete" data-rel="' + item.sell_id + '"></th>\
                            </tr>';

                        $("input[name='invoice_id']").val(item.sell_invoice_id);

                        $("#sell-pendings").append(row);

                        $(".order_num").each(function(key, val){
                            $(val).html(key + 1);
                        });

                        if($(".sell-delete").length > 1){
                            $(".sell-delete").each(function(key, val){
                                var btn = '<button class="btn btn-danger delete-pending" data-rel="' + $(val).attr("data-rel") + '">Sil</button>';
                                $(val).html(btn);
                            });
                        }

                        $(".delete-pending").unbind().click(deleteSell);

                        var totalSellPrice = $(".total_sell_price"+(currency?'':'_azn'));
                        totalSellPrice.html((parseFloat(totalSellPrice.html()) + parseFloat(item.sell_price)).toFixed(2));

                        $(".goods-info").unbind().click(getGoodsInfo);

                        $(".pending-count").unbind().bind('keyup mousekeyup', changePriceCount);
                        $(".pending-sell-price").unbind().bind('keyup mousekeyup', changePriceCount);

                        restrictNumberInput();

                    }
                }

            }
        });

    }

    $(".delete-pending").unbind().click(deleteSell);
    $(".pending-count").unbind().bind('keyup mousekeyup', changePriceCount);
    $(".pending-sell-price").unbind().bind('keyup mousekeyup', changePriceCount);

    var xhr = null;
    function changePriceCount(event){
        if(xhr != null) xhr.abort();
        $("#confirm-sell-button").attr("disabled", true);

        var self = $(event.target),
            id = self.attr("data-rel"),
            count = $("#pending-count-" + id).val(),
            currency = $("#pending-total-" + id).data('currency'),
            price = $("#pending-sell-price-" + id).val(),
            remCount = $("#remain-count-" + id),
            userId = $("#user_id").val(),
            subjectId = $("#subject_id").val();

        if(count <= 0){
            $("#pending-count-" + id).css("border", "red 2px solid");
            $(".sell-approve").attr("disabled", "disabled");
            $("#confirm-sell-button").attr("disabled", true);
            return;
        } else {
            $("#pending-count-" + id).css("border", "none");
            $(".sell-approve").removeAttr("disabled");
        }

        if(price <= 0){
            $("#pending-sell-price-" + id).css("border", "red 2px solid");
            $(".sell-approve").attr("disabled", "disabled");
            $("#confirm-sell-button").attr("disabled", true);
            return;
        } else {
            $("#pending-sell-price-" + id).css("border", "none");
            $(".sell-approve").removeAttr("disabled");
        }

        remCount.html(parseInt(remCount.attr("data-real-count") - count));

        if(parseInt(remCount.html()) < 0) {
            $("#pending-count-" + id).css("border", "red 2px solid");
            $(".sell-approve").attr("disabled", "disabled");
            $("#confirm-sell-button").attr("disabled", true);
            return;
        } else {
            $("#pending-count-" + id).css("border", "none");
            $(".sell-approve").removeAttr("disabled");
        }

        $("#pending-total-"+(currency?id:'azn-'+id) ).html((parseFloat(count) * parseFloat(price)).toFixed(2));

        calcSell();

        xhr = $.ajax({

            url: URL + "/sell/updatepriceandcount",
            type: "POST",
            data: {sell_id : id, count : count, sell_price : price, user_id : userId, subject_id : subjectId},
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                if(response.status <= 0){

                    showMessage("Yenilənmə", "Parametri yeniləmək mümkün olmadı");

                } else {

                    $("#confirm-sell-button").removeAttr("disabled");
                    return;

                }

            }
        });

    }

    function calcSell(){
        var total = 0;
        var total_azn = 0;
        $(".pending-total").each(function(key, val){
            _val = $(val).html();
            if(_val>0){
                if($(this).data('currency'))
                    total += parseFloat(_val);
                else
                    total_azn += parseFloat(_val);
            }
        });

        $(".total_sell_price").html(total.toFixed(2));
        $(".total_sell_price_azn").html(total_azn.toFixed(2));
    }

    function deleteSell(event){

        dhtmlx.confirm({
            title: "Silinməni təsdiqlə",
            text: "Silinməni təsdiqləyin",
            ok: "Təsdiq",
            cancel: "İmtina",
            callback: function(res){

                if(res){

                    var self = $(event.target),
                        sellId = self.attr("data-rel"),
                        userId = $("#user_id").val(),
                        subjectId = $("#subject_id").val();

                    if(sellId > 0) {

                        $.ajax({

                            url: URL + "/sell/delete",
                            type: "POST",
                            data: {
                                sell_id: sellId,
                                user_id: userId,
                                subject_id: subjectId
                            },
                            dataType: "JSON",
                            beforeSend: function(xhr){
                                Loader.lStart(xhr);
                            },
                            success: function(response){
                                Loader.lStop();

                                if(response.status > 0){

                                    $("tr#" + sellId).remove();

                                    $(".order_num").each(function(key, val){
                                        $(val).html(key + 1);
                                    });

                                    if($(".sell-delete").length <= 1){

                                        $(".sell-delete .btn").remove();

                                    }

                                    calcSell();

                                }

                            }

                        });

                    }

                }

            }
        });

    }

    // Sell search

    /**
     *  Get goods info
     */

    $(".goods-info").unbind().click(getGoodsInfo);

    function getGoodsInfo(event){
        $("#goods-info .modal-content").html();

        var goodsId = $(this).attr("data-rel"),
            userId = $("#user_id").val();

        $.ajax({
            url : URL + '/goods/getinfo',
            type: "POST",
            data: {
                id : goodsId,
                user_id : userId
            },
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(data){
                Loader.lStop();

                if(data.status > 0){

                    var attrs = data.data['attrs'],
                        goods = data.data['common'],
                        model = data.data['model'],
                        images = model['image'].split(";"),
                        image = '',
                        info = '';

                    $(images).each(function(key, val){

                        image += '<a class="fancybox-thumbs" data-fancybox-group="thumb" href="' + URL + '/' + val + '"><img src="' + URL + "/" + val + '" alt=""></a>';

                    });

                    $.map(attrs, function(val, key){

                        var res = model[key];
                        if(key == 'color' && colors[model[key]] != undefined) res = colors[model[key]].title;
                        if (typeof res == 'undefined') res = goods[key];
                        if (typeof res == 'undefined') res = "";
                        if(res == null) res = 'AZN';
                        info += '<dt>' + val + '</dt>\
                                 <dd>' + res + '</dd>';

                    });

                    var html = '<div class="modal-header">\
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
                                    <h4 class="modal-title" id="myModalLabel">' + model.name +  '</h4>\
                                </div>\
                                <div class="modal-body">\
                                    <div class="row">\
                                        <div class="col-xs-12 col-sm-4">' + image + '</div>\
                                        <div class="col-xs-12 col-sm-8">\
                                            <dl class="dl-horizontal">' + info + '</dl>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="modal-footer">\
                                    <button type="button" class="btn btn-primary" data-dismiss="modal">Bağla</button>\
                                </div>';

                    $("#goods-info .modal-content").html(html);

                    $('.fancybox-thumbs').unbind().fancybox({
                        prevEffect : 'none',
                        nextEffect : 'none',

                        closeBtn  : false,
                        arrows    : false,
                        nextClick : true,

                        helpers : {
                            thumbs : {
                                width  : 50,
                                height : 50
                            }
                        }
                    });

                }

            }
        });
    }

    // Get goods info

    /**
     * Sell approve
     */

     $('select[name^="client"]').change(function(){
        $(this).removeAttr('style');
     });

    $(".sell-approve").unbind().click(function(event){

        event.preventDefault();


        _radioElms = $('.inlineRadio2:checked');
        for(i=0;i<_radioElms.length;i++){
            _clientElm = $(_radioElms[i]).parents('.dl-horizontal').find('[name^="client"]');
            if( _clientElm.val() == '0'){
                _clientElm.css("border", "2px solid red");
                return false;
            }
        }


        aznElements = [];

        aznElementsQuery = $('#sell-pendings [data-currency][id^="pending-total-azn"][data-currency="0"]');

        if(aznElementsQuery.length){
            aznElementsQuery.each(function(){
                aznElements.push($(this).parents('tr').attr('id'));
            });
        }


        var data = {
            user_id : $("#user_id").val(),
            subject_id : $("#subject_id").val(),
            invoice_id : $("#invoice_id").val(),
            invoice_type : $("#invoice_type").val(),
            currency : $('#sell-approve-form').data('currency'),
            currency_archive : $('#sell-approve-form').attr('data-archive'),
            invoice_serial : $("#invoice_serial").val(),
            invoice_serial_azn : $('.addon dd:first').text(),
            amount : $(".total_sell_price").html(),
            amount_azn : $(".total_sell_price_azn").html(),
            cash : $("input[name='cash']:checked").val(),
            date : $("#date").val(),
            notes : $("#notes").val(),
            debtamount_azn: $('[name="debtamount"]').val(),
            debtamount: $('[name="debtamount_azn"]').val(),
            client_azn: $('[name="client"]').val(),
            client: $('[name="client_azn"]').val(),
            cashbox_id: $("#cashbox_id").val(),
            operator: $("#operator").val(),
            received_payment_azn: $("input[name=received_payment]").val(),
            received_payment: $(".addon input[name=received_payment]").val(),
            azns:aznElements
        };

        if(data.cash == 0 && data.client == null) {
            $("#client").css("border", "2px solid red");
            return false;
        }

        data.client = (data.client > 0 && data.client_azn > 0)?data.client:(data.client>0?data.client:data.client_azn);
        data.debtamount = (data.debtamount > 0 && data.debtamount_azn > 0)?data.debtamount:(data.debtamount>0?data.client:data.debtamount_azn);

        if(data.cash == 2){
            if(!submitable){
                $("#card_number").css("border", "2px solid red");
                return false;
            } else {
                var discountData = {
                    discount : 1,
                    card_number : $("#card_number").val(),
                    discount_or_bonus : $("#discount_or_bonus").val(),
                    discounted_amount : $("span.amount_of_discount").html()
                };
                data = Object.assign(data, discountData);
            }
        }

        if(data.amount <= 0 && data.amount_azn <= 0) return;
        if(parseFloat(data.payed) > parseFloat(data['amount'+(!data.currency?'_azn':'')])) {showMessage("İlkin ödəniş", "İlkin ödəniş məbləğdən artıq ola bilməz"); return;}

        $.ajax({
            url : URL + "/sell/approve",
            type: "POST",
            data: data,
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                if(response.status > 0) {
                    location.reload();
                } else {
                    showMessage("Satış", "Əməliyyat zamanı xəta yarandı");
                    return;
                }

            }
        });



    });

    // Sell approve

    /**
     *  Invoice detailes
     */

    $(".invoice-details").unbind().click(getInvoiceDetails);

    function getInvoiceDetails(e){

        $("#invoice_detail_table").html("");
        $("#invoice_details").modal('hide');

        var invoiceType = $(this).attr("data-invoice-type"),
            invoiceId = $(this).attr("data-invoice-id"),
            userId = $("#user_id").val(),
            serial = $(this).html();

        $.ajax({
            url : URL + "/invoice/getdetail",
            type: "POST",
            data : {
                invoice_type : invoiceType,
                invoice_id : invoiceId,
                user_id : userId
            },
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                if(response.status > 0){
                    var table = "",
                        data = response.data,
                        attrs = response.attrs;

                    table += "<tr>";
                    $.each(attrs, function(key, val){
                        table += '<th>' + val + '</th>';
                    });
                    table += "</tr>";

                    $.each(data, function(dataKey, dataVal){
                        table += "<tr>";
                        $.each(attrs, function(key, val){
                            if(key == 'operation_type'){
                                var tmp = "";
                                if(dataVal[key] == '+') tmp = 'Mədaxil'; else tmp = 'Məxaric';
                                table += '<td>' + tmp + '</td>';
                            } else {
                                table += '<td>' + dataVal[key] + '</td>';
                            }
                        });
                        table += "</tr>";
                    });


                    if(invoiceType == 1){

                        var total_sum = 0;
                        $.each(data, function(dataKey, dataVal){
                            total_sum += parseFloat(dataVal['sell_price']) * parseInt(dataVal['count']);
                        });
                        table += '<tr>';
                        table += '<td colspan=' + Object.keys(attrs).length + '><strong>Cəmi: ' + total_sum + '</strong></td>';
                        table += '</tr>';

                        var invoice = data[0];
                        var tmp_discount_data = null;
                        if(invoice.discount_type == 'bonus'){
                            tmp_discount_data = {
                                name : 'Bonus',
                                key : 'bonus',
                                param: 'İstifadə olunub'
                            };
                        } else if(invoice.discount_type == 'discount'){
                            tmp_discount_data = {
                                name : 'Endirim',
                                key : '%',
                                param : 'Dərəcə'
                            };
                        }
                        if(invoice.discount == 1){

                            table += '<tr>';
                            table += '<td colspan=' + Object.keys(attrs).length + '>';
                            table += '<table class="table">';
                            table += '<tr><th>Kartın nömrəsi</th><th>Kartın növü</th><th>' + tmp_discount_data.param + '</th><th>Cəmi məbləğ</th><th>Endirim</th><th>Ödənilən məbləğ</th></tr>';
                            table += '<td>' + invoice.discount_card_number + '</td>';
                            table += '<td>' + tmp_discount_data.name + '</td>';
                            table += '<td>' + invoice.discount_value + ' ' + tmp_discount_data.key + '</td>';
                            table += '<td>' + total_sum + '</td>';
                            table += '<td>' + invoice.discounted_amount + '</td>';
                            table += '<td>' + (parseFloat(total_sum) - parseFloat(invoice.discounted_amount)) + '</td></tr>';
                            table += '</table>';
                            table += '</td>';
                            table += '</tr>';
                        }

                    }

                    $("#invoice_detail_table").html(table);
                    $("#modal_is").html(serial);
                    $('#invoice_details .modal-dialog').css('width', '90%');
                    $('#invoice_details').css('margin', '100px auto 100px auto');
                    $('#invoice_details').modal('show');
                }

            }
        });

    }

    // Invoice detailes

    /**
     *  Contragent payment
     */

        $(".contragent-payment").unbind().click(runClientComponents);
        $('#contragent_payment [name="currency"]').change(changeClientDebts);

    // Contragent payment

    /**
     *  Client payment
     */

        $(".client-payment").unbind().click(runClientComponents);
        $('#client_payment [name="currency"]').change(changeClientDebts);

     // Client payment


    function runClientComponents(e){
        _namespace = $(e.target).parents('body').find('form[data-namespace]').data('namespace');

        buildClientComponents(e, _namespace);

        $('#'+_namespace+'_payment').modal("toggle");

    }

    function buildClientComponents(e, _namespace){
        _this = $(e.target);

        $('#'+_namespace+'_id').val(_this.attr('data-'+_namespace+'-id'));

        _curs = [];
        $('.amounts').empty();
        $('#'+_namespace+'_payment [name="currency"]').empty();

        _this.parents('tr').find('.debts.has').each(function(){
            _curs.push($(this).data('currency-name'));

            _val = parseFloat($(this).text());
            _max = 0;
            _min = 0.01;

            if(_val > 0) _max = _val;

            else if(_val < 0) {
                _val = -1*(Math.round(-1 * _val * 100) / 100);
                _min = _max = -1*_min;
            }

            _val = _val.toFixed(2);

            $('#'+_namespace+'_payment [name="currency"]').append('<option accesskey="'+$(this).data('archive')+'" value="'+$(this).data('currency-id')+'">'+$(this).data('currency-name')+'</option>');
            $('.amounts').append('<input type="number" data-cur-id="'+$(this).data('currency-id')+'" step="0.01" class="hide form-control payed" data-negative="1" max="'+_max+'" min="'+_min+'" value="'+_val+'">');
        });

        $('#'+_namespace+'_payment [name="currency"] option').each(function(){
            if(!~_curs.indexOf($(this).text()))
                $(this).remove();
        });

        changeClientDebts(e);
    }

    function changeClientDebts(e){
        _namespace = $(e.target).parents('body').find('form[data-namespace]').data('namespace');
        $('.payed:not(.hide)').addClass('hide').removeAttr('name');
        _amount = $('.payed[data-cur-id="'+$('#'+_namespace+'_payment [name="currency"]').val()+'"]').removeClass('hide').attr('name', 'amount').val();
        $("#total-debt").html(_amount);
        $("#total_debt").val(_amount);
    }

    $('.fancybox-thumbs').unbind().fancybox({
        prevEffect : 'none',
        nextEffect : 'none',

        closeBtn  : false,
        arrows    : false,
        nextClick : true,

        helpers : {
            thumbs : {
                width  : 50,
                height : 50
            }
        }
    });

    // FancyBox area


    /**
     *  CashBox actions
     */

    $('#outgoing [name="currency"], #transfer [name="currency"]').unbind().change(function(){
        _max = parseFloat($(this).parents('form').find('.hint:eq('+$(this).val()+') strong').text());
        $(this).parents('form').find('[name="amount"]').attr('max', _max);

        if($(this).is('[name="currency"]:last')){
            _shortcur = $(this).val() == 0?'.azn':'.usd';
            $('#destination_cb .hide').removeClass('hide');
            $('#destination_cb option:not("'+_shortcur+'")').addClass('hide');

            $('#destination_cb :selected').removeAttr('selected');
            $('#destination_cb '+_shortcur+':eq(1)').attr('selected', 'selected');
        }
    });

    $(".cashbox-actions").unbind().click(function(e){
        $('div.collapse h3').css('color', $(this).css('background-color'));

        var ob = $(this).attr("data-target");
        if($(ob).hasClass("in")){
            $(".collapse").removeClass("in");
        } else if($(".collapse").hasClass("in")) {
            $(".collapse").removeClass("in");
            $($(this).attr("data-target")).addClass("in");
        } else {
            $($(this).attr("data-target")).addClass("in");
        }

    });

    // CashBox actions

    /**
     *  Operator section
     */

    $("#change_password").click(function(e){

        if($(this).is(":checked")){

            $(".change_password").css("display", "block");
            $(".change_password input[type='password']").removeAttr("disabled");

        } else {

            $(".change_password").css("display", "none");
            $(".change_password input[type='password']").attr("disabled", "disabled");

        }

    });

    $(".operator_delete").unbind().click(function(e){

        e.preventDefault();
        var obj = $(e.target),
            relatedID = $(obj).attr("data-rel");

        dhtmlx.confirm({
            title: 'Silinmə',
            text:  'Silinməni təsdiqlə',
            ok:    'Təsdiq',
            cancel:'İmtina',
            callback: function(response){
                if(response){
                    $("#operator_delete_" + relatedID).submit();
                }
            }
        });

    });

    // Operator section

    /**
     *  Report get contragents
     */
    var csAjax = null;
    $(".contragent_search").keyup(function(event){

        if(csAjax != null) csAjax.abort();
        var empty_data = '<option value="0">Kontragenti seç</option>',
            contragent_search = $(this).val(),
            select_box = $("#contragent_id"),
            user_id = $("#user_id").val();

        if(contragent_search.length > 0) {

            csAjax = $.ajax({
                url: URL + "/contragent/search",
                type: "POST",
                data: { contragent_search : contragent_search, user_id : user_id },
                dataType: "JSON",
                beforeSend: function(xhr){
                    Loader.lStart(xhr);
                },
                success: function(response){
                    Loader.lStop();
                    if(response.status > 0) {
                        var html = "";
                        $.each(response.data, function(key, val){
                            html += '<option value="' + val.id + '">' + val.name + '</option>';
                        });
                        select_box.html(html);
                    } else {
                        select_box.html(empty_data);
                    }
                }
            });

        } else {
            select_box.html(empty_data);
        }

    });

    // Report get contragents

    /**
     *  Report get clients
     */
    var clsAjax = null;
    $(".client_search").keyup(function(event){

        if(clsAjax != null) clsAjax.abort();
        var empty_data = '<option value="0">Müştərini seç</option>',
            client_search = $(this).val(),
            select_box = $("#client_id"),
            user_id = $("#user_id").val();

        if(client_search.length > 0) {

            clsAjax = $.ajax({
                url: URL + "/client/search",
                type: "POST",
                data: { client_search : client_search, user_id : user_id },
                dataType: "JSON",
                beforeSend: function(xhr){
                    Loader.lStart(xhr);
                },
                success: function(response){
                    Loader.lStop();
                    if(response.status > 0) {
                        var html = "";
                        $.each(response.data, function(key, val){
                            html += '<option value="' + val.id + '">' + val.name + '</option>';
                        });
                        select_box.html(html);
                    } else {
                        select_box.html(empty_data);
                    }
                }
            });

        } else {
            select_box.html(empty_data);
        }

    });

    // Report get clients


    /**
     *  Report get clients
     */
    var cbsAjax = null;
    $(".cashbox_search").keyup(function(event){

        if(cbsAjax != null) cbsAjax.abort();
        var empty_data = '<option value="0">Kassanı seç</option>',
            cashbox_search = $(this).val(),
            select_box = $("#cashbox_id"),
            user_id = $("#user_id").val();

        if(cashbox_search.length > 0) {

            cbsAjax = $.ajax({
                url: URL + "/cashbox/search",
                type: "POST",
                data: { cashbox_search : cashbox_search, user_id : user_id },
                dataType: "JSON",
                beforeSend: function(xhr){
                    Loader.lStart(xhr);
                },
                success: function(response){
                    Loader.lStop();
                    if(response.status > 0) {
                        var html = "";
                        $.each(response.data, function(key, val){
                            html += '<option value="' + val.id + '">' + val.name + '</option>';
                        });
                        select_box.html(html);
                    } else {
                        select_box.html(empty_data);
                    }
                }
            });

        } else {
            select_box.html(empty_data);
        }

    });

    // Report get clients

    /**
     *  Subject search
     */
    var ssAjax = null;
    $(".subject_search").keyup(function(event){

        if(ssAjax != null) ssAjax.abort();
        var empty_data = '<option value="0">Obyekti seç</option>',
            subject_search = $(this).val(),
            select_box = $("#subject_sb"),
            user_id = $("#user_id").val();

        if(subject_search.length > 0) {

            ssAjax = $.ajax({
                url: URL + "/subject/search",
                type: "POST",
                data: { subject_search : subject_search, user_id : user_id },
                dataType: "JSON",
                beforeSend: function(xhr){
                    Loader.lStart(xhr);
                },
                success: function(response){
                    Loader.lStop();
                    if(response.status > 0) {
                        var html = "";
                        $.each(response.data, function(key, val){
                            html += '<option value="' + val.id + '">' + val.name + '</option>';
                        });
                        select_box.html(html);
                    } else {
                        select_box.html(empty_data);
                    }
                }
            });

        } else {
            select_box.html(empty_data);
        }

    });

    // Subject search

    /**
     *  Goods search
     */
    var gsAjax = null;
    $(".goods_search").keyup(function(event){

        if(gsAjax != null) gsAjax.abort();
        var empty_data = '<option value="0">Malı seç</option>',
            search_code = $(this).val(),
            select_box = $("#goods_id"),
            user_id = $("#user_id").val(),
            subject_id = 0;

        if(search_code.length > 0) {

            gsAjax = $.ajax({
                url: URL + "/sell/searchgoods",
                type: "POST",
                data: { search_code : search_code, user_id : user_id, subject_id : subject_id },
                dataType: "JSON",
                beforeSend: function(xhr){
                    Loader.lStart(xhr);
                },
                success: function(response){
                    Loader.lStop();
                    if(response.status > 0) {
                        var html = "";
                        $.each(response.data, function(key, val){
                            html += '<option value="' + val.goods_id + '">' + val.short_info + '</option>';
                        });
                        select_box.html(html);
                    } else {
                        select_box.html(empty_data);
                    }
                }
            });

        } else {
            select_box.html(empty_data);
        }

    });

    // Subject search

    /**
     *  Stock transfer search
     */

    /**
     *  Sell search
     */

    $(".stock_goods_search_button").unbind().click(function(event){

        var goodsCode = $("#goods_code").val(),
            userId = $("#user_id").val(),
            subjectId = $("#subject_id").val();

        if(goodsCode == "") {
            $("#goods_code").css("border", "3px solid red");
            return;
        } else {
            $("#goods_code").css("border", "none");
        }

        $.ajax({
            url: URL + "/stock/search",
            type: "POST",
            data: {
                search_code : goodsCode,
                user_id : userId,
                subject_id : subjectId
            },
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();

                if(response.status > 0){

                    var html = "", i = 0;
                    $(response.data).each(function(index, value){
                        i++;
                        html += '<tr>\
                            <th>' + i + '</th>\
                            <th>' + value.goods_code + '</th>\
                            <th>' + value.barcode + '</th>\
                            <th><a href="javascript:void(0)" data-toggle="modal" data-target="#goods-info"  class="goods-info" data-rel="' + value.id + '">' + value.short_info + '</a></th>\
                            <th>' + value.count + '</th>\
                            <th>' + value.buy_price + ' ' + (value.currency?(value.currency+' <br/> (<font color="red">'+value.currency_archive+'</font>)'):'AZN') + '</th>\
                            <th>' + value.sell_price + '</th>\
                            <th>\
                                <button type="button" data-rel="' + value.id + '" class="btn btn-primary stock-add">\
                                <span class="glyphicon glyphicon-plus"></span>\
                                </button>\
                            </th>\
                        </tr>';
                    });

                    $("#stock-search-table").css("display", "block");
                    $("#stock-search-table-tbody").html(html);

                    $(".stock-add").unbind().click(addToCart);
                    $(".goods-info").unbind().click(getGoodsInfo);


                } else {
                    html += '<tr>\
                            <th colspan="8">Axtarış parameterlərinə uyğun nəticə yoxdur</th>\
                        </tr>';
                    $("#stock-search-table-tbody").html(html);
                }

            }
        });

    });

    $("#close-search-table").click(function(e){

        $("#stock-search-table").css("display", "none");

    });

    // Stock transfer search

    /**
     *  User password change
     */


    var userPasswordChangeApproveXHR = null;
    $(".user-password-change-approve").click(function(event){

        var old_password = $("#old_password").val(),
            new_password = $("#new_password").val();

        if(old_password.length < 1){
            $("#old_password").css("border", "red 1px solid");
            return;
        } else {
            $("#old_password").css("border", "none");
        }

        if(new_password.length < 5){
            $("#new_password").css("border", "red 1px solid");
            return;
        } else {
            $("#new_password").css("border", "none");
        }

        if(old_password == new_password){

            $("#new_password").css("border", "red 1px solid");
            $("#old_password").css("border", "red 1px solid");
            return;
        } else {

            $("#new_password").css("border", "none");
            $("#old_password").css("border", "none");
        }

        if(userPasswordChangeApproveXHR != null) userPasswordChangeApproveXHR.abort();
        userPasswordChangeApproveXHR = $.ajax({

            url: URL + '/user/passwordchange',
            method: 'POST',
            data: {old_password: old_password, new_password: new_password},
            dataType: 'JSON',
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(res){
                Loader.lStop();

                dhtmlx.alert({
                    title: res.title,
                    text: res.message,
                    callback: function(){
                        if(res.status > 0){
                            window.location.href = URL + '/signout';
                        }
                    }
                });

            }

        });

    });

    // User password change approve

    /**
     *  SERVICES SECTION
     */

    $(".delete-service").unbind().click(function(event){
        event.preventDefault();
        dhtmlx.confirm({

            title: 'Xidmətin silinməsi',
            text: 'Xidmətin silnməsini təsdiqlə',
            ok: 'Təsdiq',
            cancel: 'İmtina',
            callback: function(res){

                if(res){
                    $("#delete-service-" + $(event.target).attr('rel')).submit();
                }

            }

        });

    });


    $("#service_id").unbind().change(function(event){
        $("#amount").val("");
        var $currentVal = $(this).val();
        if($currentVal > 0){
            var price = $("#service_id option:selected").attr('rel');
            $("#amount").val(price);
        }

    });

    $("#service_subject_sb").unbind().change(getServices);
    $(".service_search").unbind().keyup(getServices);


    var getServicesByName = null;
    function getServices(event){
        var subject_id = $("#service_subject_sb").val(),
            user_id = $("#user_id").val(),
            search_service = $(".service_search").val(),
            empty = '<option value="0">Xidmət növünü seç</option>';

        $("#service_id").html(empty);

        if(getServicesByName != null){
            getServicesByName.abort();
        }

        getServicesByName = $.ajax({
            url: URL + '/service/getbyname',
            data: {subject_id: subject_id, search_service: search_service, user_id: user_id},
            type: "POST",
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();
                if(response.status > 0){

                    if(search_service.length > 0) $("#service_id").html("");
                    $(response.data).each(function(key, val){
                        $("#service_id").append('<option value="' + val.id + '">' + val.name + '</option>');
                    });

                }
            }
        });
    }

    var sssAjax = null;
    $(".service_subject_search").keyup(function(event){

        if(sssAjax != null) sssAjax.abort();
        var empty_data = '<option value="0">Obyekti seç</option>',
            subject_search = $(this).val(),
            select_box = $("#service_subject_sb"),
            user_id = $("#user_id").val();

        if(subject_search.length > 0) {

            sssAjax = $.ajax({
                url: URL + "/subject/search",
                type: "POST",
                data: { subject_search : subject_search, user_id : user_id },
                dataType: "JSON",
                beforeSend: function(xhr){
                    Loader.lStart(xhr);
                },
                success: function(response){
                    Loader.lStop();
                    if(response.status > 0) {
                        var html = "";
                        $.each(response.data, function(key, val){
                            html += '<option value="' + val.id + '">' + val.name + '</option>';
                        });
                        select_box.html(html);
                    } else {
                        select_box.html(empty_data);
                    }
                    getServices(event);
                }
            });

        } else {
            select_box.html(empty_data);
        }

    });

    // Services section

    /**
     *  Expenses SECTION
     */

    $(".delete-expense").unbind().click(function(event){
        event.preventDefault();
        dhtmlx.confirm({

            title: 'Xərcin silinməsi',
            text: 'Xərcin silnməsini təsdiqlə',
            ok: 'Təsdiq',
            cancel: 'İmtina',
            callback: function(res){

                if(res){
                    $("#delete-expense-" + $(event.target).attr('rel')).submit();
                }

            }

        });

    });


    $("#expense_id, #service_id").unbind().change(function(event){
        $("#amount").val("");
        $('input[name="currency"]').val(0);
        $('select[name="currency"]').val(0).trigger('change');
        if($(this).val() > 0){
            var price = $(':selected',this).attr('rel');
            var currency = $(':selected',this).data('currency');
            $("#amount").val(price);
            $('input[name="currency"]').val(currency);
            $('select[name="currency"]').val(currency).trigger('change');
        }
    });

    $("#expense_subject_sb").unbind().change(getExpenses);
    $(".expense_search").unbind().keyup(getExpenses);

    $('select[name="currency"]').change(function(){
        $(this).next().val($(':selected',this).attr('accesskey'));
    });

    var getExpensesByName = null;
    function getExpenses(event){
        var subject_id = $("#expense_subject_sb").val(),
            user_id = $("#user_id").val(),
            search_service = $(".expense_search").val(),
            empty = '<option value="0">Xərc növünü seç</option>';

        $("#expense_id").html(empty);

        if(getExpensesByName != null){
            getExpensesByName.abort();
        }

        getExpensesByName = $.ajax({
            url: URL + '/expense/getbyname',
            data: {subject_id: subject_id, search_expense: search_expense, user_id: user_id},
            type: "POST",
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){
                Loader.lStop();
                if(response.status > 0){

                    if(search_expense.length > 0) $("#expense_id").html("");
                    $(response.data).each(function(key, val){
                        $("#expense_id").append('<option value="' + val.id + '">' + val.name + '</option>');
                    });

                }
            }
        });
    }

    var essAjax = null;
    $(".expense_subject_search").keyup(function(event){

        if(essAjax != null) essAjax.abort();
        var empty_data = '<option value="0">Obyekti seç</option>',
            subject_search = $(this).val(),
            select_box = $("#expense_subject_sb"),
            user_id = $("#user_id").val();

        if(subject_search.length > 0) {

            essAjax = $.ajax({
                url: URL + "/subject/search",
                type: "POST",
                data: { subject_search : subject_search, user_id : user_id },
                dataType: "JSON",
                beforeSend: function(xhr){
                    Loader.lStart(xhr);
                },
                success: function(response){
                    Loader.lStop();
                    if(response.status > 0) {
                        var html = "";
                        $.each(response.data, function(key, val){
                            html += '<option value="' + val.id + '">' + val.name + '</option>';
                        });
                        select_box.html(html);
                    } else {
                        select_box.html(empty_data);
                    }
                    getExpenses(event);
                }
            });

        } else {
            select_box.html(empty_data);
        }

    });

    // Expenses section

    /**
     * Notices section
     */

    $(".delete-notice, .delete-data").unbind().click(function(event){
        _this = $(this);

        dhtmlx.confirm({

            title: 'Silinmə',
            text: 'Silinməni təsdiqlə',
            ok: 'Təsdiq',
            cancel: 'İmtina',
            callback: function(res){

                if(res){
                   // $("#notice_delete_" + $(event.target).attr('rel')).submit();
                   _this.parent().submit();
                }

            }

        });

    });

    $(".notice-expire").unbind().click(function(event){

        var notice_id = $(event.target).attr('rel');
        $.ajax({
            url : URL + '/manager/notice/expire',
            type: "POST",
            data: {notice_id: notice_id},
            dataType: "JSON",
            beforeSend: function(xhr){
                Loader.lStart(xhr);
            },
            success: function(response){

                Loader.lStop();
                $("#show_modal_" + notice_id).modal('hide');

            }
        });

    });

    // Notices section

    /**
     * OPERATOR ADDON
     */

    $(".operator_prefix").unbind().keypress(function(event){

        var prefix = $(event.target).attr('data-val'),
            val = $(event.target).val(),
            keycode = event.which || event.keyCode;

        if(prefix == val && keycode == 8) {
            return false;
        }

    });

    // OPERATOR ADDON

    /**
     *  DELETE MANAGER
     */

    $(".delete-manager").unbind().click(function(event){

        dhtmlx.confirm({
            title: 'İstifadəçini sil',
            text: 'İstifadəçini sil',
            ok: 'Təsdiq',
            cancel: 'İmtina',
            callback: function(res){

                if(res) $("#delete-manager-" + $(event.target).attr('rel')).submit();

            }
        });

    });

    //  DELETE MANAGER

});
