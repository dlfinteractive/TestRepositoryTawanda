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


<title>Pay dividends</title>
<meta name="description" content="My First Trading. A stock trading simulation game.">
<meta name="author" content="Deakin Learning Environments">
<link rel="stylesheet" href="css/ui.totop.css" />
<link rel="stylesheet" href="css/styles.css">
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<script type="text/javascript">
//set up global js vars

// trading
var posting = false;

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
var total_trades;

var student_id_list=new Array();
var share_tally_list=new Array();
var resultCount=0;

/**/
function setUp(){

// alert("go")

$.post("includes/time.php",
  {},
  function(result){
    // alert(result);

  unixtime = result;

  //piggy back onto next func
  },
  "text"
  );

    //the student details are here, enter the game
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
    // if (unixtime>game_start_date){
      
    // }else{

    // }

    //alert(game_start_date)

    // alert(game_end_date);
    // alert(unixtime);
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
     // alert("game_on is "+game_on);
    var less_intro = (game_on - game_intro_length);
    // alert("less_intro is "+less_intro)
    period_calc = (Math.ceil(less_intro/game_period_length));

    // alert("period_calc is "+period_calc);

    // document.getElementById('loadText').innerHTML="Loading Game details"
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
         // alert(period_label)
      };

      //piggy back onto next func
    }

   getFactorsStart()

  },
  "text"
  );
}

function getFactorsStart(){
  //init call
  
  getFactor_dividends();
}

//============= Factors dividend details =============
function getFactor_dividends(){

  //alert(period_calc)

  
  document.getElementById('currentDay').innerHTML = period_calc;
  //find what period we are dealing with
  // alert("getFactor_dividends")

  $.post("includes/getFactor_dividends.php",
  {PostPERIOD_ID:period_id},
  function(result){
    
    var factorList=result.split("~");

    for (var i = 0; i<(factorList.length-1); i++) {
      var resultList=factorList[i].split("*");
      factor_id=resultList[0];
      factor_type=resultList[1];
      factor_string=resultList[2];
      factor_company_id=resultList[3];
      // alert("factor_type is "+factor_type)
      //alert("factor_string is "+factor_string)
      // alert("factor_company_id is "+factor_company_id)
      // add factor string to news list if it has a type of news
      if (factor_type == "dividend"){
        //alert("dividend today");
        //alert("Dividend on factor_company_id: "+factor_company_id)

        
        document.getElementById('dividend').innerHTML = 'YES';
        //alert("factor_company_id is "+factor_company_id)
        // checkFactor_dividend()
        getDividend_pay_details();


      } 
      else
      {
         //alert("no dividend today");
         document.getElementById('dividend').innerHTML = 'NO';

        //END!
      };

    }
    //piggy back onto next func   
  },
  "text"
  );
}

function getDividend_pay_details(){
  //alert("check the student_to_company table for student_id and share_tally where company_id equals: "+factor_company_id);

 

  $.post("includes/getDividend_pay_details.php",
  {PostFACTORY_COMPANY_ID:factor_company_id},
  function(result){
    //alert(result)
    var dividendList=result.split("~");
    for (var i = 0; i<dividendList.length; i++) {
    //split the string in to an array and get all values from it
      var resultList=dividendList[i].split("*");
      student_id_list.push(resultList[0]);
      share_tally_list.push(resultList[1]);
    }
    //alert(student_id_list)
    //alert(share_tally_list)
    //piggy back onto next func
    //getPeriod_details();

    for (resultCount; resultCount<(student_id_list.length-1); resultCount++) {
      //alert("go "+resultCount);
      //alert("student_id_list[resultCount] is "+ student_id_list[resultCount]);
      student_id = student_id_list[resultCount];
      share_tally = share_tally_list[resultCount];

      divCalc = share_tally*2;

      document.getElementById("dividends_added").innerHTML+="<tr><td>"+student_id+"</td><td>"+share_tally+" * 2 = <b>"+divCalc+"</b></td></tr>"

      //alert("share_tally_list[resultCount] is "+ share_tally_list[resultCount]);

      //alert("divCalc is"+divCalc);
      
      $.post("includes/addDividend.php",
      {PostSTUDENT_ID:student_id,PostDIVIDEND:divCalc},
      function(result){ 

      //alert("Trade fee will be " + tally_total + " x 5 = " + tally_total*5);
      // tradeFee = ((tally_total*5)/100).toFixed(2);

      //alert("tradeFee is $"+tradeFee);
      //piggy back onto next func 
      // getTradeFeeTotals();  
      //piggy back onto next func
       
      //END
        
      },
      "text"
      );
    }
    // alert("Dividends added.")

  },
  "text"
  );

}


</script>



</head>

<body onload="setUp()">

  <div id="loaded" class="container">
  <!-- Begin header content -->
  <header id="navtop" class="grid-wrap">
    <div class="grid col-two-thirds">
       <h1>My First Trading dividend payment</h1>
      <!-- <h2 id="student-Name">Welcome <span id="firstName"> </span> <span id="lastName"> </span></h2> -->
    </div>
    <div class="grid col-one-third">
      <p class="alignright">Current Trading Period: <span class="highlight" id="currentDay"> </span></p>
      <p class="alignright">Dividend Payable today: <span class="highlight" id="dividend"></span></p>
    </div>
  </header>
  <!-- End header content -->

  <table id="score" class="sortable" style="width:50%;">
    <thead>
      <tr> 
        <th>Student_id</th>
        <th>Dividend received</th>
      </tr>
    </thead>

    <tbody id="dividends_added">

    </tbody>
  </table>

</div>



<!-- jquery -->
<script src="http://code.jquery.com/jquery.min.js"></script>

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>


<script src="js/scripts.js"></script>
<!-- easing plugin ( optional ) -->

</body>
</html>
