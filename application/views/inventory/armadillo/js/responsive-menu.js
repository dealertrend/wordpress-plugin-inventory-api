// Runs Drop Down for Armadillo Listing Page
jQuery(document).ready(function(){
 if (jQuery('#dealertrend-inventory-api').length){ 
  jQuery('#armadillo-quick-links').attr('name','hidden');
  jQuery('#armadillo-quick-links > h3').click(function(){
   if (jQuery('#armadillo-quick-links').attr('name').match(/hidden/i) != null){
    jQuery('#armadillo-quick-links').attr('name','show');
    jQuery('#armadillo-quick-links > ul').slideDown();
   } else {
    jQuery('#armadillo-quick-links').attr('name','hidden');
    jQuery('#armadillo-quick-links > ul').slideUp();
   }
  });
 }
});

