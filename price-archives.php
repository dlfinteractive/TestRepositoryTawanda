<?php
// start of session
session_start();
include 'includes/connections.php';
?>

<!doctype html>
<html lang="en">

<head>

<meta charset="utf-8">
<meta name="Pragma" content="no-cache;">

<title>My first Trading Price Archives</title>

<link type='text/css' href='css/styles.css' rel='stylesheet' media='all' />
<link rel="stylesheet" href="css/ui.totop.css" />

<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>

<script language="javascript" type="text/javascript">
<!-- -->
// time
var new_date;
var unixtime_ms;
var unixtime;
//dummy student details - need to come form SCORM or somewhere???
var student_fname="init";
var student_lname="init";
var student_number="init";
var student_id;
var gameInstID='1';//work out how to set this later
var game_id;
var game_start_date;
var game_end_date;
/* getGame_details() */
var init_bank;
var comp_price_list=new Array();//stores the comp prices for the current period
var comp_id_list=new Array();
var comp_id_1;
var comp_id_2;
var comp_id_3;
var comp_id_4;
var comp_id_5;
var game_intro_length;
var game_period_length;
/* getCompany_details() */
var company_id;
var company_name1;
var company_name2;
var company_name3;
var company_name4;
var company_name5;
var company_origin;
var company_industry;
var company_type;
/* getPeriod_details() */
var period_id;
var period_calc;
/* getFactor_details() */
var factor_id;
var factor_type;
var factor_string;
var factor_company_id;

var period_idList=new Array();
var currentPeriodIndex;

var period_labelList=new Array();
//compare
var currentPrice1;
var prevPrice1;
var currentPrice2;
var prevPrice2;
var currentPrice3;
var prevPrice3;
var currentPrice4;
var prevPrice4;
var currentPrice5;
var prevPrice5;
//share_tally
var revenue_total;
var share_tally1;
var share_tally2;
var share_tally3;
var share_tally4;
var share_tally5;
//looping trackers
var getFactor_detailsCalls=3;
var getPeriodprice_detailsCalls=2;
var getFactor_detailsCount=0;
var getPeriodprice_detailsCount=0;
//journal
var journal_string;
var journal_all=""; 
// capital gain
var cap_gain;
// check share numbers before allowing buying
var total_shareNum=0;
var total_commonNum=0;
var total_debentureNum=0;
// buy/sell
var tally_total;

function setUp(){  
  /* CHANGE CLIENT-SIDE TO SERVER-SIDE */
  // new_date = new Date; // Generic JS date object
  // unixtime_ms = new_date.getTime(); // Returns milliseconds since the epoch
  // unixtime = parseInt(unixtime_ms / 1000);
  $.post("includes/time.php",
  {},
  function(result){
  //alert(result);
  unixtime = result;
    
  },
  "text"
  );
  //piggy back onto next func
  getGameInst_details();
}
//============= Game Instance details =============
function getGameInst_details(){
  //find what game instance we are dealing with
  $.post("includes/getGameInst_details.php",
  {PostGAME_INST_ID:gameInstID},
  function(result){
    //split the string in to an array and get all values from it
    var resultList=result.split("*");
    game_id=resultList[0];
    game_start_date=resultList[1];
    game_end_date=resultList[2];
    // alert(game_start_date);
    // need to check the current ts againts start ts to work out what period we are in
    //piggy back onto next func
    getGame_details();
  },
  "text"
  );
}

//============= Game details =============
function getGame_details(){
  //find what game instance we are dealing with
  $.post("includes/getGame_details.php",
  {PostGAME_ID:game_id},
  function(result){
    //split the string in to an array and get all values from it
    var resultList=result.split("*");
    init_bank=resultList[0];
    comp_id_list.push(resultList[1]);
    comp_id_list.push(resultList[2]);
    comp_id_list.push(resultList[3]);
    comp_id_list.push(resultList[4]);
    comp_id_list.push(resultList[5]);
    game_intro_length=resultList[6];
    game_period_length=resultList[7];

    var game_on = (unixtime-game_start_date);

    var less_intro = (game_on - game_intro_length);

    period_calc = (Math.ceil(less_intro/game_period_length));
    //alert("period_calc is "+period_calc);
    //piggy back onto next func
    getCompany_details();
  },
  "text"
  );
}

function getCompany_details(){

  //find what company details are
  $.post("includes/getCompany_details.php",
  {PostCOMP1_ID:comp_id_list[0],PostCOMP2_ID:comp_id_list[1],PostCOMP3_ID:comp_id_list[2],PostCOMP4_ID:comp_id_list[3],PostCOMP5_ID:comp_id_list[4]},
  function(result){
    var companyList=result.split("~");
    for (var i = 0; i<companyList.length; i++) {
    //split the string in to an array and get all values from it
      var resultList=companyList[i].split("*");
      window['company_name' + (i+1) ] =resultList[0];
      window['company_origin' + (i+1) ] =resultList[1];
      window['company_industry' + (i+1) ] =resultList[2];
      window['company_type' + (i+1) ]=resultList[3];
    }

    // alert(company_name1);
    // alert(company_name2);
    // alert(company_name3);
    // alert(company_name4);
    // alert(company_name5);
    //piggy back onto next func
    getPeriod_details();
  },
  "text"
  );
}

//============= Period details =============
function getPeriod_details(){

  //find what period we are dealing with
  $.post("includes/getPeriod_details.php",
  {PostGAME_ID:game_id},
  function(result){
    //split the string in to an array and get all values from it
    var periodList=result.split("~");
    var testAlert = periodList.length-1;
    for (var i = 0; i<(periodList.length-1); i++) {
      var resultList=periodList[i].split("*");
      //store all period IDs for future reference
      period_idList.push(resultList[0]);
      period_labelList.push(resultList[2]);
      // add current day label to interface
      // used the calculated current period to render day etc. ????
      // alert(period_calc)
      if ((i+1)==period_calc) {
        //current period found
        currentPeriodIndex=i;
        period_id=resultList[0];
        period_order=resultList[1];
        period_label=resultList[2];
        //alert(period_id);
        if (i>0) {
          //get the pervious label val
          var resultListPrev=periodList[i-1].split("*");
          period_label_prev=resultListPrev[2];
        };
        //alert(period_label)
        document.getElementById('currentDay').innerHTML=period_label;
        document.getElementById('remainingDay').innerHTML=(28 - period_order);
        if (document.getElementById('remainingDay').innerHTML== 0) {
          document.getElementById('remainingDay').innerHTML='final day';
        };
        break;
      };
      
    }
    //piggy back onto next func
   getPriceArchives()

  },
  "text"
  );
}

//============= Game Instance details =============
function getPriceArchives(){

  //find what game instance we are dealing with
  $.post("includes/getPriceArchives.php",
  {PostGAME_ID:game_id,PostPERIOD_ID:period_id},
  function(result){
    //alert(result)
    //split the string in to an array and get all values from it

    var archiveRow=result.split("~");
    //alert(archiveRow);
    for (var i = 0; i<(archiveRow.length-1); i++) {
      var resultList=archiveRow[i].split("*");
      period_id=resultList[0];
      company_id=resultList[1];
      price=resultList[2];
      company_name=resultList[3];
      period_label=resultList[4];

      $('#priceArch').append('<tr><td>'+ period_label +'</td><td>'+ company_name +'</td><td>'+ price +'</td></tr>');
      //alert(price)
    }
      
  },
  "text"
  );
}

</script>

</head>

<body onload="setUp()">

  <!-- -->
  <div id="loaded" class="container arch">
    <header id="navtop" class="grid-wrap">
      <div class="grid col-one-half">
         <h2>My First Trading game Price Archives</h2>
      </div>
      <div class="grid col-one-half">

        <p class="alignright">Current Trading Period: <span class="highlight" id="currentDay"> </span></p>
        <p class="alignright">Trading Periods Remaining: <span class="highlight" id="remainingDay"></span></p>
        <p class="alignright"><a href="javascript:if(confirm('Close the My First Trading Price archives?'))window.close()">Close Archives</a></p>
        
      </div>
    </header>
    <div id='arch-wrapper'>
      <table id='arch'>
        <thead>
        <tr> 
        <th class="quarter">Trading Period</th>
        <th class="half">Company</th>
        <th class="quarter">Price</th>
        </tr>
      </thead>

      <tbody id="priceArch">

      </tbody>
    </table>
    
    </div>
  </div>

  <!-- -->
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

<script src="js/scripts.js"></script>

</body>

</html>