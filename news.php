<?php
// start of session
session_start();
include 'includes/connections.php';
//include ('includes/functions.php');
//check if user has logged in. Redirects to login page if fail.
//$username = check_user_logged_in();
?>

<!doctype html>
<html lang="en">

<head>


<meta charset="utf-8">
<meta name="Pragma" content="no-cache;">

<!-- 10 minute warning and refresh at midnight -->
<script language="javascript" type="text/javascript">
  function reloadAtMidnight(){
    var day = new Date();
    var hours = day.getHours();
    // warning for the approaching refresh
    if(hours==23){ // The hour before the switch
      var mins = day.getMinutes();
      if(mins==50){ // 'x' amount of minutes before the switch
        var secs = day.getSeconds();
        if(secs<=9){ // Catch the seconds within the setinterval
          alert("This period will end in 10 minutes.\nPlease ensure all transactions and journal entries\nare completed before the period ends.");
        }
      }
    }

    //refresh on day change
    if(hours==0){// midnight
      var mins = day.getMinutes();
      if(mins==0){ // midnight
        var secs = day.getSeconds();
        if(secs<=9){ // Catch the seconds within the setinterval
          location.reload();
        }
      }    
    }
  }
  window.setInterval("reloadAtMidnight()", 10000);
</script> 
<!-- end warning and refresh -->

<title>My First Trading News</title>
<meta name="description" content="My First Trading. A stock trading simulation game.">
<meta name="author" content="Deakin Learning Environments">
<link rel="stylesheet" href="css/ui.totop.css" />
<link rel="stylesheet" href="css/styles.css">
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<script type="text/javascript">
//set up global js vars

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


/**/
function setUp(){
  
  // new_date = new Date; // Generic JS date object
  // unixtime_ms = new_date.getTime(); // Returns milliseconds since the epoch
  // unixtime = parseInt(unixtime_ms / 1000);

  $.post("includes/time.php",
  {},
  function(result){
    // alert(result);

  unixtime = result;

    //piggy back onto next func
  },
  "text"
  );

  /* CHANGE CLIENT-SIDE TO SERVER-SIDE */

  /* 
  new_date = new Date; // Generic JS date object
  unixtime_ms = new_date.getTime(); // Returns milliseconds since the epoch
  unixtime = parseInt(unixtime_ms / 1000);
  */

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
    if (unixtime>game_start_date){
      
    }else{

    }
    // need to check the current ts againts start ts to work out what period we are in somehow?
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

    // NEED TO REFINE THIS IF TO CHECK IF A BANK BALANCE EXISTS, IF NOT THE INIT BANK BECOMES THE BANKS BALANCE

    /* INIT BANK STUFF TO FIX */
    // if (document.getElementById('revenueBank').innerHTML=' ') {
    //   document.getElementById('revenueBank').innerHTML= Number(init_bank).toFixed(2);
    // }else{
    //   document.getElementById('revenueBank').innerHTML= '5000';
    // }

    var game_on = (unixtime-game_start_date);
    var less_intro = (game_on - game_intro_length);
    period_calc = (Math.ceil(less_intro/game_period_length));

    //piggy back onto next func
    getCompany_details();
  },
  "text"
  );
}

//============= Company details =============
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

    // maybe look at rendering this stuff in the above loop to streamline?
    // add company names to the portfolio table
    document.getElementById('portName1').innerHTML=company_name1;
    document.getElementById('portName2').innerHTML=company_name2;
    document.getElementById('portName3').innerHTML=company_name3;
    document.getElementById('portName4').innerHTML=company_name4;
    document.getElementById('portName5').innerHTML=company_name5;
    // add company names to the market prices table
    document.getElementById('marketName1').innerHTML=company_name1;
    document.getElementById('marketName2').innerHTML=company_name2;
    document.getElementById('marketName3').innerHTML=company_name3;
    document.getElementById('marketName4').innerHTML=company_name4;
    document.getElementById('marketName5').innerHTML=company_name5;
    // add company origins to the market prices table
    document.getElementById('marketOrigin1').innerHTML=company_origin1;
    document.getElementById('marketOrigin2').innerHTML=company_origin2;
    document.getElementById('marketOrigin3').innerHTML=company_origin3;
    document.getElementById('marketOrigin4').innerHTML=company_origin4;
    document.getElementById('marketOrigin5').innerHTML=company_origin5;
    // add company industries to the market prices table
    document.getElementById('marketInd1').innerHTML=company_industry1;
    document.getElementById('marketInd2').innerHTML=company_industry2;
    document.getElementById('marketInd3').innerHTML=company_industry3;
    document.getElementById('marketInd4').innerHTML=company_industry4;
    document.getElementById('marketInd5').innerHTML=company_industry5;
    // add company industries to the market prices table
    document.getElementById('marketType1').innerHTML=company_type1;
    document.getElementById('marketType2').innerHTML=company_type2;
    document.getElementById('marketType3').innerHTML=company_type3;
    document.getElementById('marketType4').innerHTML=company_type4;
    document.getElementById('marketType5').innerHTML=company_type5;

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
    //alert(testAlert)

    for (var i = 0; i<(periodList.length-1); i++) {
      var resultList=periodList[i].split("*");
      //store all period IDs for future reference
      period_idList.push(resultList[0]);
      period_labelList.push(resultList[2]);

      //alert(period_calc)

      // add current day label to interface
      // used the calculated current period to render day etc. ????
      if ((i+1)==period_calc) {
        //current period found
        currentPeriodIndex=i;
        period_id=resultList[0];
        period_order=resultList[1];
        period_label=resultList[2];
        if (i>0) {
          //get the pervious label val
          var resultListPrev=periodList[i-1].split("*");
          period_label_prev=resultListPrev[2];
        };
        document.getElementById('price-current-day').innerHTML=period_label;
        document.getElementById('price-previous-day').innerHTML=period_label_prev;
        document.getElementById('currentDay').innerHTML=period_label;
        document.getElementById('remainingDay').innerHTML=(28 - period_order);
        /* === EDIT IF LENGTH OF GAME IS SHORTER === */
        break;
      };

      //piggy back onto next func
    }

   getFactor_full()

  },
  "text"
  );
}

// function getFactorsStart(){
//   //init call
//   getFactor_details(currentPeriodIndex);
// }

//============= Factors details =============
function getFactor_full(){
  //find what period we are dealing with
    //alert(period_id)

  $.post("includes/getFactor_full.php",
  {PostPERIOD_ID:period_id},
  function(result){
    // alert(result)
    
    var factorList=result.split("~");

    for (var i = 0; i<(factorList.length-1); i++) {
      var resultList=factorList[i].split("*");
      factor_id=resultList[0];
      factor_type=resultList[1];
      factor_string=resultList[2];
      factor_company_id=resultList[3];
      // add factor string to news list if it has a type of news


        // $('#news').append('<li class="news-day">'+ period_labelList[period_index] +'</li>');
        $('#news').append('<li>'+ factor_string +'</li>');



    }
    //piggy back onto next func
    //conditionally loop or move on

    // alert(getFactor_detailsCalls)
    // getFactor_detailsCount++;
    // if(getFactor_detailsCount<getFactor_detailsCalls){
    //   //keep looping
    //   var param=currentPeriodIndex-getFactor_detailsCount;
    //   getFactor_details(param)
    // }else{
    //   //move on
    //   // getPeriodpriceStart()

    // }
    
  },
  "text"
  );
}
</script>
</head>

<body onload="setUp()">
  <?php
/*
  //create database connection
    try
    {
      $conn = db_connect();
    }
    catch (Exception $e)
    {
      die ($e->getMessage());
    }
    //$results = getGame($conn,'1');
  */
?>

  <div class="container">
  <!-- Begin header content -->
  <header id="navtop" class="grid-wrap">
    <div class="grid col-two-thirds">
       <h1>My First Trading News</h1>
    </div>
    <div class="grid col-one-third">

      <p class="alignright">Current Trading Period: <span class="highlight" id="currentDay"> </span></p>
      <p class="alignright">Trading Periods Remaining: <span class="highlight" id="remainingDay"></span></p>
      <p class="alignright"><a href="javascript:if(confirm('Close the My First Trading News archives?'))window.close()">Close News</a></p>
      
    </div>
  </header>
  <!-- End header content -->

  <section class="grid-wrap hide">
    <!-- Begin Portfolio content -->
    <article class="grid col-one-half">
      <header>
        <h3>My Portfolio</h3>
      </header>
      <table class="trading-floor open-close">
        <tr>
          <th scope="col" width="15%">Name</th>
          <th scope="col" width="15%">Shares</th>
          <th scope="col" width="15%">Today's Price</th>
          <th scope="col" width="15%">Total</th>
          <th scope="col" width="20%">Buy</th>
          <th scope="col" width="20%">Sell</th>
        </tr>
        <!-- -->
        <tr>
          <td id="portName1"> </td>
          <td id="portTally1"> 0 </td>
          <td id="portPrice1"> </td>
          <td id="portTotal1"> 0 </td>
          <td>
            <div class="buy1">
                <form action="JavaScript:buyShares()" method="post" id="buy1" >
                  <input name="shares" id="numBuy1" class="valEmpty" onkeyup="calcBuy1()" type="text" placeholder="shares" />
                  <p>x <span id="portBuy1"> </span></p>
                  <p>= <span id="totalBuy1">total</span></p>
                  <input name="confirm"type="submit" value="TRADE" />
                </form>
                <p id="nobuy1" class="highlight">You can only hold a maximum 3 Securites at any one time, comprising a maximum 2 Common Shares and/or 1 Debenture.</p>
            </div>
          </td>
        <td>
          <div class="sell1">
                <form action="JavaScript:sellShares()" method="post" id="sell1">
                  <input name="shares" id="numSell1" class="valEmpty" onkeyup="calcSell1()" type="text" placeholder="shares"/>
                  <p>x <span id="portSell1"> </span></p>
                  <p>= <span id="totalSell1">total</span></p>
                  <input name="confirm" id="sell1" type="submit" value="TRADE" />
                </form>
            </div>
        </td>
        </tr>
        <!-- -->
        <tr>
          <td id="portName2"> </td>
          <td id="portTally2"> 0 </td>
          <td id="portPrice2"> </td>
          <td id="portTotal2"> 0 </td>
          <td>
            <div class="buy2">
                <form action="JavaScript:buyShares()" method="post" id="buy2">
                  <input name="shares" id="numBuy2" class="valEmpty" onkeyup="calcBuy2()" type="text" placeholder="shares" />
                  <p>x <span id="portBuy2"> </span></p>
                  <p>= <span id="totalBuy2">total</span></p>
                  <input name="confirm" type="submit" value="TRADE" />
                </form>
                <p id="nobuy2" class="highlight">You can only hold a maximum 3 Securites at any one time, comprising a maximum 2 Common Shares and/or 1 Debenture.</p>
            </div>
          </td>
        <td>
          <div class="sell2">
                <form action="JavaScript:sellShares()" method="post" id="sell2">
                  <input name="shares" id="numSell2" class="valEmpty" onkeyup="calcSell2()" type="text" placeholder="shares"/>
                  <p>x <span id="portSell2"> </span></p>
                  <p>= <span id="totalSell2">total</span></p>
                  <input name="confirm" id="sell2" type="submit" value="TRADE" />
                </form>
            </div>
        </td>
        </tr>
        <!-- -->
        <tr>
          <td id="portName3"> </td>
          <td id="portTally3"> 0 </td>
          <td id="portPrice3"> </td>
          <td id="portTotal3"> 0 </td>
          <td>
            <div class="buy3">
                <form action="JavaScript:buyShares()" method="post"  id="buy3">
                  <input name="shares" id="numBuy3" class="valEmpty" onkeyup="calcBuy3()" type="text" placeholder="shares"/>
                  <p>x <span id="portBuy3"> </span></p>
                  <p>= <span id="totalBuy3">total</span></p>
                  <input name="confirm" type="submit" value="TRADE" />
                </form>
                <p id="nobuy3" class="highlight">You can only hold a maximum 3 Securites at any one time, comprising a maximum 2 Common Shares and/or 1 Debenture.</p>
            </div>
          </td>
        <td>
          <div class="sell3">
                <form action="JavaScript:sellShares()" method="post" id="sell3">
                  <input name="shares" id="numSell3" class="valEmpty" onkeyup="calcSell3()" type="text" placeholder="shares"/>
                  <p>x <span id="portSell3"> </span></p>
                  <p>= <span id="totalSell3">total</span></p>
                  <input name="confirm" id="sell3" type="submit" value="TRADE" />
                </form>
            </div>
        </td>
        </tr>
        <!-- -->
        <tr>
          <td id="portName4"> </td>
          <td id="portTally4"> 0 </td>
          <td id="portPrice4"> </td>
          <td id="portTotal4"> 0 </td>
          <td>
            <div class="buy4">
                <form action="JavaScript:buyShares()" method="post" id="buy4">
                  <input name="shares" id="numBuy4" class="valEmpty" onkeyup="calcBuy4()" type="text" placeholder="shares"/>
                  <p>x <span id="portBuy4"> </span></p>
                  <p>= <span id="totalBuy4">total</span></p>
                  <input name="confirm" type="submit" value="TRADE" />
                </form>
                <p id="nobuy4" class="highlight">You can only hold a maximum 3 Securites at any one time, comprising a maximum 2 Common Shares and/or 1 Debenture.</p>
            </div>
          </td>
        <td>
          <div class="sell4">
                <form action="JavaScript:sellShares()" method="post" id="sell4">
                  <input name="shares" id="numSell4" class="valEmpty" onkeyup="calcSell4()" type="text" placeholder="shares"/>
                  <p>x <span id="portSell4"> </span></p>
                  <p>= <span id="totalSell4">total</span></p>
                  <input name="confirm" id="sell4" type="submit" value="TRADE" />
                </form>
            </div>
        </td>
        </tr>
        <!-- -->
        <tr>
          <td id="portName5"> </td>
          <td id="portTally5"> 0 </td>
          <td id="portPrice5"> </td>
          <td id="portTotal5"> 0 </td>
          <td>
            <div class="buy5">
                <form action="JavaScript:buyShares()" method="post" id="buy5">
                  <input name="shares" id="numBuy5" class="valEmpty" onkeyup="calcBuy5()" type="text" placeholder="shares"/>
                  <p>x <span id="portBuy5"> </span></p>
                  <p>= <span id="totalBuy5">total</span></p>
                  <input name="confirm" type="submit" value="TRADE" />
                </form>
                <p id="nobuy5" class="highlight">You can only hold a maximum 3 Securites at any one time, comprising a maximum 2 Common Shares and/or 1 Debenture.</p>
            </div>
          </td>
        <td>
          <div class="sell5">
                <form action="JavaScript:sellShares()" method="post" id="sell5">
                  <input name="shares" id="numSell5" class="valEmpty" onkeyup="calcSell5()" type="text" placeholder="shares"/>
                  <p>x <span id="portSell5"> </span></p>
                  <p>= <span id="totalSell5">total</span></p>
                  <input name="confirm" id="sell5" type="submit" value="TRADE" />
                </form>
            </div>
        </td>
        </tr>
      </table>
    </article>
    <!-- End Portfolio content -->

    <!-- Begin Market Prices content -->
    <article class="grid col-one-half">
      <header>
        <h3>Market Prices</h3>
      </header>
      <table class="open-close">
        <tr>
          <th scope="col" width="15%">Name</th>
          <th scope="col" width="15%">Origin</th>
          <th scope="col" width="20%">Industry</th>
          <th scope="col" width="20%">Type</th>
          <th scope="col" width="10%" id="price-previous-day"> </th>
          <th scope="col" width="10%" id="price-current-day"> </th>
          <th scope="col" width="10%">Change</th>
        </tr>
        <tr>
          <td id="marketName1"> </td>
          <td id="marketOrigin1"> </td>
          <td id="marketInd1"> </td>
          <td id="marketType1"> </td>
          <td id="marketPrevious1"> </td>
          <td id="marketPrice1"> </td>
          <td id="marketComp1"> </td>
        </tr>
        <tr>
          <td id="marketName2"> </td>
          <td id="marketOrigin2"> </td>
          <td id="marketInd2"> </td>
          <td id="marketType2"> </td>
          <td id="marketPrevious2"> </td>
          <td id="marketPrice2"> </td>
          <td id="marketComp2"> </td>
        </tr>
        <tr>
          <td id="marketName3"> </td>
          <td id="marketOrigin3"> </td>
          <td id="marketInd3"> </td>
          <td id="marketType3"> </td>
          <td id="marketPrevious3"> </td>
          <td id="marketPrice3"> </td>
          <td id="marketComp3"> </td>
        </tr>
        <tr>
          <td id="marketName4"> </td>
          <td id="marketOrigin4"> </td>
          <td id="marketInd4"> </td>
          <td id="marketType4"> </td>
          <td id="marketPrevious4"> </td>
          <td id="marketPrice4"> </td>
          <td id="marketComp4"> </td>
        </tr>
        <tr>
          <td id="marketName5"> </td>
          <td id="marketOrigin5"> </td>
          <td id="marketInd5"> </td>
          <td id="marketType5"> </td>
          <td id="marketPrevious5"> </td>
          <td id="marketPrice5"> </td>
          <td id="marketComp5"> </td>
        </tr>
      </table>
    </article>
    <!-- End Market Prices content -->
  </section>


  <section class="grid-wrap">

    <!-- Begin News content -->
    <article class="grid col-full">
      <header>
        <h3>News archives</h3>
      </header>
      <ol id="news" class="news-full" reversed>
      </ul>

    </article>
    <!-- End News content -->
  </section>

</div>

<!-- jquery -->
<script src="http://code.jquery.com/jquery.min.js"></script>

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>


<script src="js/scripts.js"></script>
<!-- easing plugin ( optional ) -->
<script src="js/easing.js" type="text/javascript"></script>
<!-- Starting the plugin -->
<!--<script type="text/javascript">
$(function() {
  $(window).scroll(function() {
    if($(this).scrollTop() >= 50) {
      $('#toTop').fadeIn();
    } else {
      $('#toTop').fadeOut();
    }
  });

  $('#toTop').click(function() {
    $('body,html').animate({scrollTop:0},800);
  });
});
</script>-->
</body>
</html>
