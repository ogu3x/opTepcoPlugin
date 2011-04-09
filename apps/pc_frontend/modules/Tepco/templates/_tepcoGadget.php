<div id="tepcoBox_<?php echo $gadget->id ?>">
<div id="tepco_img" style="padding:0;margin:2px 0;">
<table style="padding:0;margin:0;width:270px">
<tr><td colspan="2" style="padding:0;height:15px;"><td></tr>
<tr><td id="tepco_info" colspan="2" style="width:200px;padding:2px 0 0 3px;font-family:Helvetica;font-size:11pt;"></td></tr>
<tr><td style="width:192px;"></td><td id="tepco_rate" style="padding:0;font-size:23px;font-weight:bold;line-height:30px;"></td></tr>
</table>
</div>
</div>

<script type="text/javascript"> 
//<![CDATA[
new Ajax.Request (
  '<?php echo url_for('Tepco/getData') ?>',
  {
    method: 'POST',
    onCreate: function(){
    },
    onSuccess: function(transport) {
      var json = transport.headerJSON;
      var img = '<?php echo public_path('/opTepcoPlugin/img/') ?>'+json.img;
      $("tepco_img").style.backgroundImage ='url('+img+')';
      $("tepco_img").style.backgroundRepeat="no-repeat";
      $("tepco_info").innerHTML =json.time+" "+json.capacity+"万kw "+json.used+"万kw";
      $("tepco_rate").innerHTML =json.rate+"%";
      $("tepco_rate").style.color =json.color;
    }
  }
);
//]]>
</script>
