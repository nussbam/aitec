function setupButton(){
   $("#submit").click(function() {
   	$.post("http://192.168.2.200/input_numberdisplay", { input_numberdisplay: $("#textbox").val() });
   });
};


function setupLightDisplay(){
   setInterval(function(){
      $.getJSON( "http://192.168.2.200/json/output_light", function( json ) {
      		console.log( "JSON Data: " + JSON.stringify(json));
      		$("#output").text(json[0].state);
 			});	
		
	}, 1000);
}

