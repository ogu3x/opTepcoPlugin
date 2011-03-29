<div id="tepco_img" style="padding:0;margin:2px 0;">
<table style="padding:0;margin:0;width:270px">
<tr><td colspan="2" style="padding:0;height:15px;"><td></tr>
<tr><td id="tepco_info" colspan="2" style="width:200px;padding:2px 0 0 3px;font-family:Helvetica;font-size:11pt;"></td></tr>
<tr><td style="width:192px;"></td><td id="tepco_rate" style="padding:0;font-size:23px;font-weight:bold;line-height:30px;"></td></tr>
</table>
</div>

<script type="text/javascript"> 
//<![CDATA[
new Ajax.Request (
  '/Tepco/getData',
  {
    method: 'POST',
    onCreate: function(){
    },
    onSuccess: function(transport) {
      var json = transport.headerJSON;
      var img = '/opTepcoPlugin/img/'+json.img;
      $("tepco_img").style.backgroundImage ='url('+img+')';
      $("tepco_img").style.backgroundRepeat="no-repeat";
      $("tepco_info").innerHTML =json.time+". "+json.used+"万kw "+json.capacity+"万kw";
      $("tepco_rate").innerHTML =json.rate+"%";
      $("tepco_rate").style.color =json.color;
    }
  }
);
//]]>
</script>
