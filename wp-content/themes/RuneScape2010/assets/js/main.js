
jQuery.noConflict();
jQuery(document).ready(function($){
  $('.wp-smiley img').each(function(){
  $(this).removeAttr('width');
  $(this).removeAttr('height');
 });
});