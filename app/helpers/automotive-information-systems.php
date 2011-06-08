UNABLE TO MOVE FORWARD!

Orange needs to return the AIS key in the company feed.


/* -------------------------------------------------------- */
/*  DealerTrend AIS Rebates Plugin v1.2 8-19-10 J. Roche    */
/* -------------------------------------------------------- */
var $j = jQuery;

$j.fn.exists = function(){return $j(this).length>0;}

$j(document).ready(function() {
  if (typeof ais_data != "undefined") {
    // Initiate overlay function for AIS popup
    setupAIS();
    getTheme();
    $j("a.aislink").overlay({expose: '#4F4F4F', effect: 'apple', onBeforeLoad: function() {$j('.aisframe').attr("src",this.getTrigger().attr("href"));}});
    $j('a.aislink').click(function(event){event.preventDefault()});
  }
});

function getTheme(){
  //Determine what inventory theme the page is using - This will be improved in future orange enhancements using common classes for inventory pages
  if ($j("div.center_stage").exists()) {
    if ($j("div.center_stage > div.detail").exists()) { detailAIS('center_stage') }else{listAIS('center_stage')}
  }
  if ($j("div.premium").exists()) {
    if ($j("div.premium > div.detail").exists()) { detailAIS('premium') }else{listAIS('premium')}
  }
}

function listAIS(theme){
  // Display AIS Rebates on vehicle listing page
  var ais = new Array();
  $j("div.vehicles").children("a.dt-new").each(
    function(i) {
      if (theme == "premium") { var vinStr = $j("span.details", this).html(); }
      else if (theme == "center_stage") {var vinStr = $j("span.vin", this).text();}
      vin=$j.trim(vinStr).replace("VIN #","");
      ais = getAIS(vin);
      if (typeof ais != "undefined") {
        if (vin.match(ais.vin)){
          var rebateammount = '<br/><a href="http://onecar.aisrebates.com/dlr2/inline/IncentiveOutput.php?vID=' + ais.vin + '&wID='+ais.wid+'&zID='+ais.zid+'" class="aislink ais-rebatetext" rel="#aisbox">' + ais.incentives + '</a>';
          // Check if the ais link text is defined by the website before setting it to the default
          if (typeof aistext === 'undefined') {
            aistext = "VIEW AVAILABLE REBATES AND INCENTIVES";
          }
          var incentivelink = '<br/><a href="http://onecar.aisrebates.com/dlr2/inline/IncentiveOutput.php?vID=' + ais.vin + '&wID='+ais.wid+'&zID='+ais.zid+'" class="aislink ais-rebatelink" rel="#aisbox">'+aistext+'</a>';
          $j("span.price", this).append(rebateammount);
          $j("span.title", this).append(incentivelink);
          if (ais.incentives.indexOf("%") < 0){ // is there a % in the rebate? (60mo @ 0%) if so, don't calculate dicounted price.
            if ($j("span.asking",this).exists()) {
              prc=$j("span.asking",this).text();
            }else{
              prc=$j("span.now",this).text();              
            }
            if(isValidprice(prc)){
              var rbt = String(ais.incentives);
              var discprc = String(formatPrice(toNum(prc)-toNum(rbt)));
              var discountprice = '<div class="ais-discountprice"><span>$' + discprc + '</span></div>';
              $j("span.price", this).append(discountprice);
            }
          }
        }
      }
    }
  )
}

function detailAIS(theme){
  // Display AIS Rebates on vehicle detail page
  if (theme == "premium") { var vinStr = $j("span.details", this).html(); }
  else if (theme == "center_stage") {
    var vinStr = $j("div.vin").text();
    vin = vinStr.replace(/[^a-zA-Z 0-9]+/g,'').replace('VIN','');
    ais = getAIS($j.trim(vin));
    if (vin.match(ais.vin)){
      var rebateinfo = '<div style="display:block;clear:both;"><label class="ais-rebatelabel">Incentives:</label><a href="http://onecar.aisrebates.com/dlr2/inline/IncentiveOutput.php?vID=' + vin + '&wID='+ais_wid+'&zID='+ais_zid+'" class="aislink ais-rebatetext" rel="#aisbox"><span class="ais-rebatetext">'+ais.incentives+'</a></div>';
      $j("div.price").append(rebateinfo);
      if (ais.incentives.indexOf("%") < 0){ // is there a % in the rebate? (60mo @ 0%) if so, don't calculate dicounted price.
        if ($j("span.asking","div.price").exists()) {
          prc=$j("span.asking","div.price").text();
        }else{
          prc=$j("span.now","div.price").text();              
        }
        if(isValidprice(prc)){
          var rbt = String(ais.incentives);
          var discprc = String(formatPrice(toNum(prc)-toNum(rbt)));
          var discountprice = '<div style="display:block;clear:both;"><label class="ais-rebatelabel">Your Price:</label><a href="http://onecar.aisrebates.com/dlr2/inline/IncentiveOutput.php?vID=' + vin + '&wID='+ais_wid+'&zID='+ais_zid+'" class="aislink ais-discountprice" rel="#aisbox"><span class="ais-rebatetext">$' + discprc + '</span></a></div>';
          $j("div.price").append(discountprice);
        }
      }
    }
  }
}

function isValidprice(n){
  //Verify valid price format ($1 $1,000 $1,000.00 - not $Call, etc)
  var prx = /\$([\d,.]+)/g;
  if(n.match(prx)){return true}else{return false}
}

function toNum(n){
  // strip ascii, aplpha and convert string to Number
  return Number(n.replace(/[^\d\.]/g,""));
}

function getAIS(vin){
  // Check ais_data array for athe specified VIN
  for ( var i=ais_data.length-1; i>=0; --i ){
    if(ais_data[i].vin == vin){
       return ais_data[i];
    }
  }
}

function formatPrice(value){
  // Convert to human readable price (10000 to $10,000)
  var buf="";var sBuf="";var j=0;value=String(value);if(value.indexOf(".")>0){buf=value.substring(0,value.indexOf("."));}else{buf=value;}
  if(buf.length%3!=0&&(buf.length/3-1)>0){sBuf=buf.substring(0,buf.length%3)+",";buf=buf.substring(buf.length%3);}
  j=buf.length;for(var i=0;i<(j/3-1);i++){sBuf=sBuf+buf.substring(0,3)+",";buf=buf.substring(3);}
  sBuf=sBuf+buf;if(value.indexOf(".")>0){value=sBuf+value.substring(value.indexOf("."));}
  else{value=sBuf;}
  return value;
}

function setupAIS(){
  // Add the AIS overlay container and include AIS stylesheet
  $j('head').append('<link rel="stylesheet" href="http://js.s3.dealertrend.com/ais/ais-display.css" type="text/css"/><link rel="stylesheet" href="http://static.flowplayer.org/tools/css/overlay-apple.css" type="text/css" />');  
  $j("body").append('<div class="apple_overlay black" id="aisbox" style="display:none;"> <iframe class="aisframe" src="about:blank" width="785" height="615" frameborder="0"></iframe></div>');
}
