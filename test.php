<head>
<script src="./jquery/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="./jquery/jquery-ui-1.8.11.custom.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="./css/jquery-ui-1.8.13.custom.css" type="text/css">
<style>
	span {
		height:150px; float:left; margin:15px 30px;
	}
	.eq{
		width: 80px;
		height:160px;
		border: 1px solid #eeeeee;
		float: left;
		text-align: center;
		padding: 5px 5px;
		font-weight: bold;
		color:#ff6ca0 ;
	}
	
</style>

<script>
$(function() {
	
	$( "#cache" ).slider({
		range: "min",
		aminate: true,
		min: 0,
		max: 6,
		value: 4,
		orientation: "vertical",
		slide: function(event, ui){
			$("#cacheBox").val(ui.value);
		}
	});
	$( "#queryView" ).slider({
		range: "min",
		aminate: true,
		min: 0,
		max: 6,
		value: 2,
		orientation: "vertical",
		slide: function(event, ui){
			$("#queryViewBox").val(ui.value);
		}
	});
	$( "#totalView" ).slider({
		range: "min",
		aminate: true,
		min: 0,
		max: 6,
		value: 1,
		orientation: "vertical",
		slide: function(event, ui){
			$("#totalViewBox").val(ui.value);
		}
	});
	$( "#likeRatio" ).slider({
		range: "min",
		aminate: true,
		min: 0,
		max: 6,
		value: 1,
		orientation: "vertical",
		slide: function(event, ui){
			$("#likeRatioBox").val(ui.value);
		}
	});

	$( "#cacheBox" ).val( $("#cache").slider("value") );
	$( "#queryViewBox" ).val( $("#queryView").slider("value") );
	$( "#totalViewBox" ).val( $("#totalView").slider("value") );
	$( "#likeRatioBox" ).val( $("#likeRatio").slider("value") );

});
</script>
</head>
<body>
<div style="margin: auto; width: 460px;">
<div style="margin: auto; text-align: center; padding: 10px; color:#666666; font-weight: bold; font-size: large;">
POLYRANK SIMPLE UI DEMO PAGE<br/> use jquery ui-slider
</div>
<div style="margin: auto; width: 370px;">
<div class="eq">
	cache
	<br/>
	<span id="cache"></span>
</div>
<div class="eq">
	queryView
	<br/>
	<span id="queryView"></span>
</div>
<div class="eq">
	totalView
	<br/>
	<span id="totalView"></span>
</div>
<div class="eq">
	likeRatio
	<br/>
	<span id="likeRatio"></span>
</div>
<div style="clear:both;">
</div>
</div>
<div id="result" style="margin:10px auto; width: 460px; font-size:medium;">
<b>the ranking formula is</b><br/>
cache * <input type="text" size="2" id="cacheBox" > + 
queryView * <input type="text" size="2" id="queryViewBox" > +
totalView * <input type="text" size="2" id="totalViewBox" > +
likeRatio * <input type="text" size="2" id="likeRatioBox" >
</div>
</div>
</body>
