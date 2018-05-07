$(function() {
    $('#easypiechart-teal').easyPieChart({
        scaleColor: false,
        trackColor: false,
        barColor: '#1ebfae'
    });
});

$(function() {
    $('#easypiechart-orange').easyPieChart({
        scaleColor: false,
        trackColor: false,
        barColor: '#ffb53e'
    });
});

$(function() {
    $('#easypiechart-red').easyPieChart({
        scaleColor: false,
        trackColor: false,
        barColor: '#f9243f'
    });
});

$(function() {
   $('#easypiechart-blue').easyPieChart({
       scaleColor: false,
       trackColor: false,
       barColor: '#30a5ff'
   });
});

$('#calendar').datepicker();

!function ($) {
    $(document).on("click","ul.nav li.parent > a > span.icon", function(){          
        $(this).find('em:first').toggleClass("glyphicon-minus");      
    }); 
    $(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
}

(window.jQuery);
	$(window).on('resize', function () {
  if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
})
$(window).on('resize', function () {
  if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
})