<?php
/**
 * Template Name: Registration Page
 */

get_header();

?>
<style type="text/css">
        @import url(<?php echo get_stylesheet_directory_uri(); ?>/theme-assets/global/css/create3-16.css);
</style>

<script type="text/javascript">//<![CDATA[
var last_ajax_username = "".toLowerCase();
var last_ajax_response = '17';
var has_valid_username = true;
var blocked = false;
var info_showing = '';
var submitted = false;


function _(objid){
 if (typeof objid == "string") objid=document.getElementById(objid);
 if (!objid) return;
 return objid;
}

function findPosY(obj)
{
  var curtop = 0;
  if(obj.offsetParent)
      while(1)
      {
        curtop += obj.offsetTop;
        if(!obj.offsetParent)
          break;
        obj = obj.offsetParent;
      }
  else if(obj.y)
      curtop += obj.y;
  return curtop;
}

function isLeapYear(year) {
 if ( year < 0) return (year +1) % 4 == 0;//>
 if ( year < 1582 ) return year % 4 == 0;//>
 if ( year % 4 != 0 ) return false;
 if ( year % 100 != 0 ) return true;
 if ( year % 400 != 0 ) return false;
 return true;
}

function display(obj){
 info_showing = obj.id;
 var jmesg = _('jmesg');
 var srctext = _(obj.id + '_desc').innerHTML;
 var ypos = findPosY(obj) - findPosY(_('formBoxes')) + 1;
 jmesg.innerHTML = srctext;
 jmesg.style.backgroundPosition =  '0px ' + (ypos - 10) + 'px';
}

function uncross(obj){
 if(obj.className == 'fail'){
  obj.className = '';
 }
 return;
}

function checkit(){
 _('creatForm').action='';
}





var input_order=["username","password1","password2","day","month","year","country","submitbutton"];

function a_pos(val, arr) {
 for(var i=0; i<arr.length; i++) if(val===arr[i]) return i;//>
 return -1;
}

function handle_keypress(event) {
 if(!event) event=window.event;
 if(event.keyCode!=13) return true;
 var p=a_pos(this.id, input_order);
 if(p!=-1 && p<input_order.length-1) var next=_(input_order[p+1]);//>
 if(next && next.focus) {
  next.focus();
  return false;
 }
 return true;
}

function install_textboxes() {
 // Skip the last one (submit button), but it needs to be in the array so things know where to go
 for(var i=0; i<input_order.length-1; i++) if(!_(input_order[i]).onkeypress){//>
  _(input_order[i]).onkeypress=handle_keypress;
 }
}

function install(){
 validate_username(false);
 validate_password(false,true);
 install_textboxes();
 if(_('jmesg').innerHTML == ''){display(_('usr'));}
}

// window.onload fix: Dean Edwards/Matthias Miller/John Resig
function dummy() {
};
function init() {
 if (arguments.callee.done) return;
 arguments.callee.done = true;
 if (_timer) clearInterval(_timer);
 if (install) install();
};
/* for Mozilla/Opera9 */
if (document.addEventListener) {
 document.addEventListener("DOMContentLoaded", init, false);
}
/* for Internet Explorer */
/*@cc_on @*/
/*@if (@_win32)
 document.write("<script id=__ie_onload defer src=dummy()><\/script>");
 var script = _("__ie_onload");
 script.onreadystatechange = function() {
  if (this.readyState == "complete") {
   init(); // call the onload handler
  }
 };
/*@end @*/
/* for Safari */
if (/WebKit/i.test(navigator.userAgent)) { // sniff
 var _timer = setInterval(function() {
  if (/loaded|complete/.test(document.readyState)) {
   init(); // call the onload handler
  }
 }, 10);
}
/* for other browsers */
window.onload = init;//]]></script>

<div class="navigation">
    <div class="location">
        <?php the_breadcrumb(); ?>
    </div>
</div>
<div id="content">
<div id="article">
<div class="sectionHeader">
<div class="left">
<div class="right">
<div class="plaque">
<?php the_title(); ?>

</div>
</div>
</div>
</div>
<div class="section">
    <div class="brown_background" style="padding: 0;">
       <?php character_registration_function(); ?>
  
</div>
</div>
</div>
</div>



<?

get_footer(); 

?>