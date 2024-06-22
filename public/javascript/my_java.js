jQuery(function() {
  jQuery('#test1').datepicker();
  
   jQuery('#test2').datepicker({
      onSelect: function ()
                {
                  $(this).change();
                }
   });
  
  
  jQuery("input").on("change", function(){
    console.log("change detected");
  });
  
  
  });
  