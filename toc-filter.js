$(function(){
	$("#toc-filter").bind("change paste keyup", toc_filter);
});

function toc_filter( event )
{
	var val = $(this).val().toLowerCase();
	$("#toc li").each(function(){
		if($(this).text().toLowerCase().indexOf(val) == -1)
		{
			$(this).hide();
		}
		else
		{
			$(this).show();
		}
	});
}