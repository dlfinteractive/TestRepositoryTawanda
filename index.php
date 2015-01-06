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

<title>My First Trading</title>
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
//dummy student details - need to come from SCORM?
var student_fname="init";
var student_lname="init";
var student_number="init";
var student_id;
var gameInstID='1';
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

var participation;

/**/
function setUp(){

 $('#loaded').css('display','none');
    $('#loading').css('display','block');
     $('#trading').css('display','none');

    // alert("The date has come!!");
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

  
/* CHANGE CLIENT-SIDE TO SERVER-SIDE */

   
  // new_date = new Date; // Generic JS date object
  // unixtime_ms = new_date.getTime(); // Returns milliseconds since the epoch
  // unixtime = parseInt(unixtime_ms / 1000);
  

  //set up for init render of page

  //--open up again when the sql statement adds or leaves it if already exists----- sendStudDeets();
  //render the welcome div
  student_fname=getParameterByName('student_fname');
  student_lname=getParameterByName('student_lname');
  student_number=getParameterByName('student_number');

  //alert("student_fname is "+getParameterByName('student_fname'))
  
  if (student_fname == 'noresult') {
    //there are no student details in the URL, redirect to the error page
    window.location = 'oops.html';
  }else{ 
    //the student details are here, enter the game
    getGameInst_details();
  }


}

function getParameterByName(name)
{
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.search);
  if(results == null)
    return "noresult";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
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

    // alert(game_start_date)

    // alert(game_end_date);
    // alert(unixtime);


    // game has ended
    if (game_end_date<unixtime) {
      window.location = 'gameover.html';
    } else
    // game has not yet begun
    if (game_start_date>unixtime) {
      window.location = 'early.html';
    } else

    // game is on, continue

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

    document.getElementById('loadText').innerHTML="Loading Game details"
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
        if (i>0) {
          //get the pervious label val
          var resultListPrev=periodList[i-1].split("*");
          period_label_prev=resultListPrev[2];
        };
        document.getElementById('price-current-day').innerHTML=period_label;
        // document.getElementById('price-previous-day').innerHTML=period_label_prev;
        if (period_calc>1) {
          document.getElementById('price-previous-day').innerHTML=period_label_prev;
          /*
          document.getElementById('marketPrevious1').innerHTML=comp_price_list[0];
          document.getElementById('marketPrevious2').innerHTML=comp_price_list[1];
          document.getElementById('marketPrevious3').innerHTML=comp_price_list[2];
          document.getElementById('marketPrevious4').innerHTML=comp_price_list[3];
          document.getElementById('marketPrevious5').innerHTML=comp_price_list[4];
          */
        }
        document.getElementById('currentDay').innerHTML=period_label;
        document.getElementById('remainingDay').innerHTML=(28 - period_order);
        /*=== EDIT IF GAME LENGTH IS SHORTENED ===*/
        if (document.getElementById('remainingDay').innerHTML== 0) {
          document.getElementById('remainingDay').innerHTML='final day';
        };
        break;
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
  
  getFactor_details(currentPeriodIndex);
}

//============= Factors details =============
function getFactor_details(period_index){
  //find what period we are dealing with
  // alert("can i check div factor here?")
  //document.getElementById("news").innerHTML = "";
  period_id_param=period_idList[period_index]



  $.post("includes/getFactor_details.php",
  {PostPERIOD_ID:period_id_param},
  function(result){

    // alert(result)
    
    var factorList=result.split("~");

    //alert(factorList)

    for (var i = 0; i<(factorList.length-1); i++) {
      var resultList=factorList[i].split("*");
      factor_id=resultList[0];
      factor_type=resultList[1];
      factor_string=resultList[2];
      factor_company_id=resultList[3];
      // add factor string to news list if it has a type of news



      if (factor_type == "news"){
        // alert(factor_string)

        $('#news').append('<li class="news-day">'+ period_labelList[period_index] +'</li>');
        $('#news').append('<li>'+ factor_string +'</li>');

      }

    }
    //piggy back onto next func
    //conditionally loop or move on
    getFactor_detailsCount++;
    //alert(getFactor_detailsCount+" + "+getFactor_detailsCalls)
    if(getFactor_detailsCount<getFactor_detailsCalls){
      //keep looping
      var param=currentPeriodIndex-getFactor_detailsCount;
      getFactor_details(param)
    }else{
      //move on
      
      getPeriodpriceStart()

    }
    
  },
  "text"
  );
}


function getPeriodpriceStart(){
  getPeriodprice_details(currentPeriodIndex,'current')

}

//============= Company Period Price details =============
function getPeriodprice_details(period_index,mode){
  //find what period we are dealing with
  period_id_param=period_idList[period_index]
  //
  $.post("includes/getPeriodprice_details.php",
  {PostGAME_ID:game_id,PostPERIOD_ID:period_id_param},

  function(result){

  //split the string in to an array and get all values from it
	var priceList=result.split("~");

  	for (var i = 0; i<(priceList.length-1); i++) {
  		var priceListSplit=priceList[i].split("*");
  		var thisCompID=priceListSplit[0];
  		var thisPrice=priceListSplit[1];
  		//now look for a match in the comp_id_list
  		for (var j = 0; j<comp_id_list.length; j++) {
  			if(thisCompID==comp_id_list[j]){
  			//this is the company so add the price at the matching index
  			comp_price_list[j]=thisPrice;
  			 break;	
			}
		}
		
	}
	
	//now render with comp_price_list values
	if(mode=='current'){
		//render in the current column
		for (var i = 0; i<(comp_price_list.length-1); i++) {
			//innerHTML
      document.getElementById('portPrice1').innerHTML=comp_price_list[0];
      document.getElementById('portPrice2').innerHTML=comp_price_list[1];
      document.getElementById('portPrice3').innerHTML=comp_price_list[2];
      document.getElementById('portPrice4').innerHTML=comp_price_list[3];
      document.getElementById('portPrice5').innerHTML=comp_price_list[4];

      document.getElementById('marketPrice1').innerHTML=comp_price_list[0];
      document.getElementById('marketPrice2').innerHTML=comp_price_list[1];
      document.getElementById('marketPrice3').innerHTML=comp_price_list[2];
      document.getElementById('marketPrice4').innerHTML=comp_price_list[3];
      document.getElementById('marketPrice5').innerHTML=comp_price_list[4];

      document.getElementById('portBuy1').innerHTML=comp_price_list[0];
      document.getElementById('portBuy2').innerHTML=comp_price_list[1];
      document.getElementById('portBuy3').innerHTML=comp_price_list[2];
      document.getElementById('portBuy4').innerHTML=comp_price_list[3];
      document.getElementById('portBuy5').innerHTML=comp_price_list[4];

      document.getElementById('portSell1').innerHTML=comp_price_list[0];
      document.getElementById('portSell2').innerHTML=comp_price_list[1];
      document.getElementById('portSell3').innerHTML=comp_price_list[2];
      document.getElementById('portSell4').innerHTML=comp_price_list[3];
      document.getElementById('portSell5').innerHTML=comp_price_list[4];

      currentPrice1=Number(comp_price_list[0]);
      currentPrice2=Number(comp_price_list[1]);
      currentPrice3=Number(comp_price_list[2]);
      currentPrice4=Number(comp_price_list[3]);
      currentPrice5=Number(comp_price_list[4]);
      
		}
	}else{
		for (var i = 0; i<(comp_price_list.length-1); i++) {
			//innerHTML
      // alert(period_calc);
      if (period_calc>1) {
      document.getElementById('marketPrevious1').innerHTML=comp_price_list[0];
      document.getElementById('marketPrevious2').innerHTML=comp_price_list[1];
      document.getElementById('marketPrevious3').innerHTML=comp_price_list[2];
      document.getElementById('marketPrevious4').innerHTML=comp_price_list[3];
      document.getElementById('marketPrevious5').innerHTML=comp_price_list[4];
    }
    else
    {
      document.getElementById('marketPrevious1').innerHTML="&mdash;";
      document.getElementById('marketPrevious2').innerHTML="&mdash;";
      document.getElementById('marketPrevious3').innerHTML="&mdash;";
      document.getElementById('marketPrevious4').innerHTML="&mdash;";
      document.getElementById('marketPrevious5').innerHTML="&mdash;";

    }

      prevPrice1=Number(comp_price_list[0]);
      prevPrice2=Number(comp_price_list[1]);
      prevPrice3=Number(comp_price_list[2]);
      prevPrice4=Number(comp_price_list[3]);
      prevPrice5=Number(comp_price_list[4]);
      
		}
	}
 
    //piggy back onto next func or loop
    getPeriodprice_detailsCount++;
    if(getPeriodprice_detailsCount<getPeriodprice_detailsCalls){
      //keep looping
      var param1=currentPeriodIndex-getPeriodprice_detailsCount;
      var param2='archived';//default after first call
      getPeriodprice_details(param1,param2);
    }else{
      //move on
      comparePrices()

    }
    
  },

  "text"
  );
}

//============= Compare Prices =============
function comparePrices(){
  var compare1 = (currentPrice1-prevPrice1).toFixed(2);
  var compare2 = (currentPrice2-prevPrice2).toFixed(2);
  var compare3 = (currentPrice3-prevPrice3).toFixed(2);
  var compare4 = (currentPrice4-prevPrice4).toFixed(2);
  var compare5 = (currentPrice5-prevPrice5).toFixed(2);

  if (compare1 > 0){ 
    document.getElementById('marketComp1').setAttribute('class', 'up');
  }
  else if (compare1 < 0){ 
    document.getElementById('marketComp1').setAttribute('class', 'down');
  }
  else { 
    document.getElementById('marketComp1').setAttribute('class', 'steady');
  }

  if (compare2 > 0){ 
    document.getElementById('marketComp2').setAttribute('class', 'up');
  }
  else if (compare2 < 0){ 
    document.getElementById('marketComp2').setAttribute('class', 'down');
  }
  else { 
    document.getElementById('marketComp2').setAttribute('class', 'steady');
  }

  if (compare3 > 0){ 
    document.getElementById('marketComp3').setAttribute('class', 'up');
  }
  else if (compare3 < 0){ 
    document.getElementById('marketComp3').setAttribute('class', 'down');
  }
  else { 
    document.getElementById('marketComp3').setAttribute('class', 'steady'); 
  }

  if (compare4 > 0){ 
    document.getElementById('marketComp4').setAttribute('class', 'up');
  }
  else if (compare4 < 0){ 
    document.getElementById('marketComp4').setAttribute('class', 'down');
  }
  else { 
    document.getElementById('marketComp4').setAttribute('class', 'steady');
  }

  if (compare5 > 0){ 
    document.getElementById('marketComp5').setAttribute('class', 'up');
  }
  else if (compare5 < 0){ 
    document.getElementById('marketComp5').setAttribute('class', 'down');
  }
  else { 
    document.getElementById('marketComp5').setAttribute('class', 'steady');
  }

  document.getElementById('marketComp1').innerHTML=compare1;
  document.getElementById('marketComp2').innerHTML=compare2;
  document.getElementById('marketComp3').innerHTML=compare3;
  document.getElementById('marketComp4').innerHTML=compare4;
  document.getElementById('marketComp5').innerHTML=compare5;

  //piggy back onto next func
  //getFactor_dividends();
  document.getElementById('loadText').innerHTML="Loading News items"
  sendStudDeets();
}


//============= Student details =============
function sendStudDeets(){
  //student details

  $.post("includes/setOrCheckStudDetails.php",
  {PostSTUDENT_FNAME:student_fname,PostSTUDENT_LNAME:student_lname,PostSTUDENT_NUMBER:student_number},
  function(result){

    //split the string in to an array and get all values from it
    var resultList=result.split("*");
    student_fname=resultList[0];
    student_lname=resultList[1];
    student_number=resultList[2];

    document.getElementById('firstName').innerHTML=student_fname;
    document.getElementById('lastName').innerHTML=student_lname;
    //piggy back onto next func
    getStudentID();
  },
  "text"
  );
}

function getStudentID(){


  
  $.post("includes/getStudentID.php",
  {PostSTUDENT_NUMBER:student_number},
  function(result){
    student_id = result;

     //alert(student_id)

    
    //piggy back onto next func
    setupStudScore();
  },
  "text"
  ); 
  
}

// let's set up the student row in the score table here

//============= Student details =============
function setupStudScore(){

  //alert(period_label)
  //student details
  $.post("includes/setupStudScore.php",
  {PostSTUDENT_ID:student_id,PostGAME_ID:game_id,PostSTUDENT_FNAME:student_fname,PostSTUDENT_LNAME:student_lname,PostSTUDENT_NUMBER:student_number },
  function(result){
    
    //piggy back onto next func

    // alert(period_label)
    getJournalEntries();
  },
  "text"
  );
}

//============= Validate Journal Entry =============
function validateSummary(){

  if (!$("#summaryReport").val()) {
    // textarea is empty
    alert("Please enter a summary report to save.");
    // $("#journalEntry").effect( "shake", { times:3 }, 500);
  }
  else{
    //piggy back onto next func
    // addJournalEntry();
  }
  
}

//============= Validate Journal Entry =============
function validateJournalEntry(){

  if (!$("#journalEntry").val()) {
    // textarea is empty
    alert("Please enter a journal entry to save.");
    // $("#journalEntry").effect( "shake", { times:3 }, 500);
  }
  else{
    //piggy back onto next func
    addJournalEntry();
  }
  
}


//============= Add Journal Entry =============
function addJournalEntry(){

  // alert(period_label)
  
  journal_string = document.getElementById("journalEntry").value;

  // alert(period_id);

  $.post("includes/addJournalEntry.php",
  {PostSTUDENT_ID:student_id,PostJOURNAL_STRING:journal_string,PostPERIOD_ID:period_id,PostGAME_INST_ID:gameInstID},
  function(result){

    document.getElementById("journalEntry").value = ""; // clear the form
    document.getElementById("journal_list").innerHTML = ""; // clear the journal render ready to reload the updated list by the getJournalEntries function
    document.getElementById("leader_board").innerHTML = ""; // clear the leader board render ready to reload the updated list by the getJournalEntries function
    getJournalEntries();
  },
  "text"
  );
  
}

//============= Get All Journal Entries =============
function getJournalEntries(){


  $.post("includes/getJournal_Entries.php",
  {PostSTUDENT_ID:student_id},
  function(result){  
    var factorList=result.split("~");
    journal_total=factorList.length-1;
    document.getElementById('journal-total').innerHTML=journal_total;

    for (var i = 0; i<(factorList.length-1); i++) {
      var resultList=factorList[i].split("*");
      journal_string=resultList[0];

      journal_all = journal_all + " " + journal_string;

      period_label=resultList[1];
      //alert(period_label)
      // add factor string to news list if it has a type of news   
         // alert(period_label)



      $('#journal_list').append('<li class="journal-day">Entry ' + (i+1) + ' <span class="journalDay">(' +  period_label + ')</span></li>');
      $('#journal_list').append('<li>'+ journal_string +'</li>');    

    }

    if ( ! $("#journal_list li").length ){
      $('#journal_list').append("<li class='highlight'>You haven't made any journal entries yet.</li>");  
    }
    

    //piggy back onto next func

    //alert(period_label)
    // countWords();
    setupJournalScore();
    
  },
  "text"
  );
}

//============= Count All Journal Entries Words =============
function countWords(){

  //alert(period_label)
  s = journal_all;
  s = s.replace(/[\.,-\/#!$%\^&\*;:{}=\-_`~()]/g,"");
  s = s.replace(/[ ]{2,}/gi," ");
  s = s.replace(/\n /,"\n");
  document.getElementById("wordCount").innerHTML = s.split(' ').length-1;

  //piggy back onto next func
  // getFactor_dividends();

  setupJournalScore();
    // getTradeHistory();
}


// ADD THE JOUNAL STUFF TO SCORE TABLE HERE!
//============= Basic trade score details =============
function setupJournalScore(){

  //alert(period_label)

   //alert("journal_total is "+journal_total)

  if (journal_total<1) {
    journal_score=0;
  }
  else 
  if (journal_total==1) {
    journal_score=0.5;
  }
  else 
  if (journal_total==2) {
    journal_score=1;
  }
  else 
  if (journal_total==3) {
    journal_score=1.5;
  }
  else 
  if (journal_total==4) {
    journal_score=2;
  }
  else 
  if (journal_total==5) {
    journal_score=2.5;
  }
  else 
  if (journal_total==6) {
    journal_score=3;
  }
  else 
  if (journal_total==7) {
    journal_score=3.5;
  }
  else 
  if (journal_total==8) {
    journal_score=4;
  }
  else 
  if (journal_total==9) {
    journal_score=4.5;
  }
  else 
  if (journal_total>=10) {
    journal_score=5;
  }

   // alert("journal_score val is "+journal_score)
  // alert("student_id is "+student_id)


  $.post("includes/setupJournalScore.php",
  {PostSTUDENT_ID:student_id,PostJOURNAL_SCORE:journal_score},
  function(result){
     // alert(result)
    //piggy back onto next func
    
    // getFactor_dividends();

    // Leapfrog the dividend stuff as it is run externally now!!
    checkStudentinTable();
  },
  "text"
  );
}

//============= Factors dividend details =============
function getFactor_dividends(){
  //find what period we are dealing with
    //alert("getFactor_dividends")

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
        //alert("dividend today")
        //alert("factor_company_id is "+factor_company_id)
        // checkFactor_dividend()
        setTimeout(function(){checkFactor_dividend()},500);


      } 
      else
      {
        // alert("no dividend today");

        checkStudentinTable()
      };

    }
    //piggy back onto next func   
  },
  "text"
  );
}

/* ======================
ALL THIS IS NOT USED! DIVIDENDS ARE ALLOCATED MAUNUALLY BY THE DIVIDENDS PAGE
====================== */

//============= Check factors dividend held =============
function checkFactor_dividend(){
  checkDiv = "portTally"+factor_company_id;

  //alert("checkDiv is "+checkDiv)
  //this just checks 1?

  // if (true) {};


        

   // alert("checkFactor_dividend")
  // alert(checkDiv+"'s innerHTML is "+document.getElementById(checkDiv).innerHTML)
  if (document.getElementById(checkDiv).innerHTML > 0) {

    checkDividendinTable()
    // addDividendtoTable()
    //alert("go to Add func")
  }
  else
  {
    // checkDividendinTable()
    //alert("not greater, skip rest")
    checkStudentinTable()
    
  }

}



//============= Check student revenue table to see if revenue exists =============
function checkDividendinTable(){
  //student details
   //alert("checkDividendinTable")

  $.post("includes/checkDividendinTable.php",
  {PostSTUDENT_ID:student_id,PostFACTOR_ID:factor_id},
  function(result){


    //alert("Is there a rec in the table? Y/N:  "+result)

    if (result=='Y') {
      checkStudentinTable();
    }
    else
    {
      addDividendtoTable(); 
    }
    //piggy back onto next func
    //addDividendtoTable();
  },
  "text"
  );

  
}


//============= Add dividend to student revenue table =============
function addDividendtoTable(){
  //student details
  // alert("addDividendtoTable")

  $.post("includes/addDividendtoTable.php",
  {PostSTUDENT_ID:student_id,PostGAME_INST_ID:game_id,PostFACTOR_ID:factor_id},
  function(result){

    divCheck=result;

    //alert("divCheck is "+divCheck)
    //alert("factor_id is "+factor_id)
    //piggy back onto next func
    addDividend();
  },
  "text"
  );

  
}

//============= Add dividend to revenue table and render =============
function addDividend(){

  // alert("addDividend")
  
  // alert("factor_company_id is "+factor_company_id)
  //alert(game_id);
  //calculate the dividend total
  // alert(factor_string)
  //portPrice1
  // divCalc="portPrice"+factor_company_id;
  // // alert(divCalc);
  // divCalc=document.getElementById(divCalc).innerHTML;
  // // alert(divCalc);
  // divCalc=Number(divCalc*factor_string).toFixed(2);


  // Hard coded as per instructions from academic.
  divCalc=2
  //alert(divCalc);
  // alert(student_id);

  //alert(tradeFee)

  $.post("includes/addDividend.php",
  {PostSTUDENT_ID:student_id,PostGAME_INST_ID:game_id,PostDIVIDEND:divCalc},
  function(result){ 

  //alert("Trade fee will be " + tally_total + " x 5 = " + tally_total*5);
  // tradeFee = ((tally_total*5)/100).toFixed(2);

  //alert("tradeFee is $"+tradeFee);
  //piggy back onto next func 
  // getTradeFeeTotals();  
  //piggy back onto next func
   //alert("DONE?")
    checkStudentinTable()
    
  },
  "text"
  );
}
/* ======================
ALL THIS IS NOT USED! DIVIDENDS ARE ALLOCATED MAUNUALLY BY THE DIVIDENDS PAGE
====================== */


/* NEED TO CHECK IF STUDENT HAS AN ENTRY IN THE REV TABLE
IF SO, BALANCE COMES FROM THERE, ELSE, INIT THE REV */

//============= Check student revenue table to see if revenue exists =============
function checkStudentinTable(){


  //student details
  //alert("checkStudentinTable function")

  $.post("includes/checkStudentinTable.php",
  {PostSTUDENT_ID:student_id,PostGAME_INST_ID:game_id},
  function(result){


     //alert("Is there a row in the revenue table for student_id '"+student_id+"'? Y/N:  "+result)

    if (result=='Y') {
      // there is a record already
      getTradeHistory()
    }
    else
    {
      // there is no record, init
      initStudentRevenue(); 
    }
    //piggy back onto next func
    //addDividendtoTable();
  },
  "text"
  );

  
}


function initStudentRevenue(){

   // alert("initStudentRevenue")


  // alert("student_id "+student_id)
  // alert("game_id "+game_id)
  // alert("init_bank "+init_bank)

  /* CHECK TO SEE IF EXISTS, IF NOT DO THIS, ELSEMORE ALONG... */
  
  $.post("includes/initStudentRevenue.php",
  {PostSTUDENT_ID:student_id,PostGAME_INST_ID:game_id,PostBANK_BALANCE:init_bank},
  function(result){
    //piggy back onto next func
    getTradeHistory();
  },
  "text"
  ); 
  
}




//============= Buy Shares =============

function buyShares(id){

  // alert("period_label: "+period_label)

  // $('#trading').css('display','block');
  if (posting) {
    // alert("Please wait until previous transaction is completed.")
  }
  else
  {
  // alert('start ' +id);
  var thisValid=true;
   var idToCheck='numBuy' +id;
   //alert(document.getElementById(idToCheck).value)
   if(document.getElementById(idToCheck).value==''){
     alert("Please enter the number of Securities to buy.");
     thisValid=false;
   }


   
  //lastChar = thisId.substr(thisId.length - 1);
  if(thisValid){


  // alert("lastChar is "+lastChar);
  //company_id = lastChar;
  company_id = id;

  tally_total = document.getElementById("numBuy"+id).value;
   
   // alert("company_id is "+company_id);
   // alert("tally_total is "+tally_total);
   // alert("student_id is "+student_id);
   // alert("game_id is "+game_id);
   /* tally_rev needs to be subtracted from the rev total in the rev table */
   tally_rev = document.getElementById("totalBuy"+company_id).innerHTML;
   //alert("tally_rev is "+tally_rev)
  // alert("period_label is "+period_label);

  thisCompany = window['company_name'+id];
  // alert("thisCompany is "+thisCompany)

  period_label = document.getElementById("currentDay").innerHTML;

  
  //alert("period_label: "+period_label)

  //$('#trade_history').prepend('<li >'+ period_label + ': BUY ' + tally_total + ' x ' + thisCompany +'</li>');
  history_string = period_label + ': BUY ' + tally_total + ' x ' + thisCompany;




  //alert('history_string is '+history_string)

   posting=true; // DB comm
   $('#trading').css('display','block');
  $.post("includes/buyShares.php",
  {PostCOMPANY_ID:company_id,PostTALLY_ID:tally_total,PostSTUDENT_ID:student_id, PostGAME_INST_ID:game_id},
  function(result){  
    // alert(result)
    var factorList=result.split("~");

    for (var i = 0; i<(factorList.length-1); i++) {
      var resultList=factorList[i].split("*");
      leader_total=resultList[0];

      
      // need to write this to a history DB table
      //$('#trade_history').append('<li >$'+ period_label + ': BUY ' + tally_total + ' x ' + company_id '</li>');  

    }
    
    //piggy back onto next func

    //clear form
    $(':input')
    .not(':button, :submit, :reset, :hidden')
    .val('')
    .removeAttr('checked')
    .removeAttr('selected');

    document.getElementById("totalBuy"+company_id).innerHTML = "total";
    posting=false; //DB comm done
    updateBuyRevenue()
    // addTradeFee();
  },
  "text"
  )  
 } //end thisValid


}

  

}



//============= Add buy to revenue table and render =============
function updateBuyRevenue(){

  document.getElementById('loadText').innerHTML="Loading Company details"


  // alert("addDividend")

  $.post("includes/updateBuyRevenue.php",
  {PostSTUDENT_ID:student_id,PostGAME_INST_ID:game_id,PostTALLY_REV:tally_rev},
  function(result){ 

  //alert("Trade fee will be " + tally_total + " x 5 = " + tally_total*5);
  // tradeFee = ((tally_total*5)/100).toFixed(2);

  //alert("tradeFee is $"+tradeFee);
  //piggy back onto next func 
  // getTradeFeeTotals();  
  //piggy back onto next func
  // alert("DONE")
    addTradeFee()
    
  },
  "text"
  );
}

//============= Sell Shares =============
function sellShares(id){
  
  // $('#trading').css('display','block');
  // alert("sellShares func init");
  if (posting) {
    // alert("Please wait until previous transaction is completed.")
  }
  else
  {

  var thisValid=true;
   var idToCheck='numSell' +id;
   //alert(document.getElementById(idToCheck).value)
   if(document.getElementById(idToCheck).value==''){
     alert("Please enter the number of Securities to sell.");
     thisValid=false;
   }
   
   // alert(thisValid);
  //lastChar = thisId.substr(thisId.length - 1);
  if(thisValid){

  // alert("thisId is "+thisId);
  // lastChar = thisId.substr(thisId.length - 1);

  // alert("lastChar is "+lastChar);
  company_id = id;

  tally_total = document.getElementById("numSell"+id).value;
   
   // alert("company_id is "+company_id);
   // alert("tally_total is "+tally_total);
   // alert("student_id is "+student_id);
   // alert("game_id is "+game_id);
   /* TUESDAY! tally_rev needs to be subtracted from the rev total in the rev table */
   tally_rev = document.getElementById("totalSell"+company_id).innerHTML;
   // alert("tally_rev is "+tally_rev)
  // alert("period_label is "+period_label);

  thisCompany = window['company_name'+id];
  // alert("thisCompany is "+thisCompany)

  period_label = document.getElementById("currentDay").innerHTML;


  //$('#trade_history').prepend('<li >'+ period_label + ': BUY ' + tally_total + ' x ' + thisCompany +'</li>');
  history_string = period_label + ': SELL ' + tally_total + ' x ' + thisCompany;


 // alert('history_string is '+history_string)

posting=true; // DB commm
$('#trading').css('display','block');
  $.post("includes/sellShares.php",
  // {PostCOMPANY_ID:company_id,PostTALLY_ID:tally_total,PostSTUDENT_ID:student_id, PostGAME_INST_ID:game_id},
  {PostCOMPANY_ID:company_id,PostTALLY_ID:tally_total,PostSTUDENT_ID:student_id},

  function(result){  
    // alert("result is "+result)
    var factorList=result.split("~");

    for (var i = 0; i<(factorList.length-1); i++) {
      var resultList=factorList[i].split("*");
      leader_total=resultList[0];

      
      // need to write this to a history DB table
      //$('#trade_history').append('<li >$'+ period_label + ': BUY ' + tally_total + ' x ' + company_id '</li>');  

    }
    
    //piggy back onto next func

    //clear form
    $(':input')
    .not(':button, :submit, :reset, :hidden')
    .val('')
    .removeAttr('checked')
    .removeAttr('selected');

    document.getElementById("totalSell"+company_id).innerHTML = "total";

posting=false; // DB commm done
    updateSellRevenue()
    // addTradeFee();
  },
  "text"
  ); 

  }// end Val 

}
  
}

//============= Sell Shares =============
function sellSharesOLD(id){
  
  // alert("sellShares func init");

  var thisValid=true;
   var idToCheck='numSell' +id;
   //alert(document.getElementById(idToCheck).value)
   if(document.getElementById(idToCheck).value==''){
     alert("Please enter the number of Securities to sell.");
     thisValid=false;
   }
   
   alert(thisValid);
  //lastChar = thisId.substr(thisId.length - 1);
  if(thisValid){

  // alert("thisId is "+thisId);
  // lastChar = thisId.substr(thisId.length - 1);

  // alert("lastChar is "+lastChar);
  company_id = id;

  tally_total = document.getElementById("numSell"+id).value;
   
   // alert("company_id is "+company_id);
   // alert("tally_total is "+tally_total);
   // alert("student_id is "+student_id);
   // alert("game_id is "+game_id);
   /* TUESDAY! tally_rev needs to be subtracted from the rev total in the rev table */
   tally_rev = document.getElementById("totalSell"+company_id).innerHTML;
   // alert("tally_rev is "+tally_rev)
  // alert("period_label is "+period_label);

  thisCompany = window['company_name'+id];
  // alert("thisCompany is "+thisCompany)


  //$('#trade_history').prepend('<li >'+ period_label + ': BUY ' + tally_total + ' x ' + thisCompany +'</li>');
  history_string = period_label + ': SELL ' + tally_total + ' x ' + thisCompany;
  // alert(period_label)


 // alert('history_string is '+history_string)


  $.post("includes/sellShares.php",
  // {PostCOMPANY_ID:company_id,PostTALLY_ID:tally_total,PostSTUDENT_ID:student_id, PostGAME_INST_ID:game_id},
  {PostCOMPANY_ID:company_id,PostTALLY_ID:tally_total,PostSTUDENT_ID:student_id},

  function(result){  
    // alert("result is "+result)
    var factorList=result.split("~");

    for (var i = 0; i<(factorList.length-1); i++) {
      var resultList=factorList[i].split("*");
      leader_total=resultList[0];

      
      // need to write this to a history DB table?
      //$('#trade_history').append('<li >$'+ period_label + ': BUY ' + tally_total + ' x ' + company_id '</li>');  

    }
    
    //piggy back onto next func

    //clear form
    $(':input')
    .not(':button, :submit, :reset, :hidden')
    .val('')
    .removeAttr('checked')
    .removeAttr('selected');

    document.getElementById("totalSell"+company_id).innerHTML = "total";

    updateSellRevenue()
    // addTradeFee();
  },
  "text"
  ); 

  }// end Val 
  
}

//============= Add buy to revenue table and render =============
function updateSellRevenue(){

  // alert("addDividend")

  $.post("includes/updateSellRevenue.php",
  {PostSTUDENT_ID:student_id,PostGAME_INST_ID:game_id,PostTALLY_REV:tally_rev},
  function(result){ 

  //alert("Trade fee will be " + tally_total + " x 5 = " + tally_total*5);
  // tradeFee = ((tally_total*5)/100).toFixed(2);

  //alert("tradeFee is $"+tradeFee);
  //piggy back onto next func 
  // getTradeFeeTotals();  
  //piggy back onto next func
  // alert("DONE?")
    addTradeFee()
    
  },
  "text"
  );
}

//============= Add trade fee =============
function addTradeFee(){
  
  //alert(game_id);
  tradeFee = ((tally_total*5)/100).toFixed(2);
  //alert(tradeFee)
  $.post("includes/addTradeFee.php",
  {PostSTUDENT_ID:student_id,PostGAME_INST_ID:game_id,PostTRADE_FEE:tradeFee},
  function(result){ 

  //alert("Trade fee will be " + tally_total + " x 5 = " + tally_total*5);
  // tradeFee = ((tally_total*5)/100).toFixed(2);

  //alert("tradeFee is $"+tradeFee);
  //piggy back onto next func 
  // getTradeFeeTotals();  
  //piggy back onto next func
    addTradeHistory();
    
  },
  "text"
  );
}

//============= Add Journal Entry =============
function addTradeHistory(){

   //alert(student_id +".."+history_string+".."+gameInstID);

  //alert(tally_total)

  $.post("includes/addTradeHistory.php",
  {PostSTUDENT_ID:student_id,PostHISTORY_STRING:history_string,PostGAME_INST_ID:gameInstID},
  function(result){

    //alert(result)

    document.getElementById("trade_history").innerHTML = ""; // clear the list
    //alert("cc")
    getTradeHistory();
    // addTradeFee();

  },
  "text"
  );
  
}



//============= Get All Journal Entries =============
function getTradeHistory(){

   //alert(period_label)

  $.post("includes/getTradeHistory.php",
  {PostSTUDENT_ID:student_id,PostGAME_INST_ID:gameInstID},
  function(result){  



    //alert(result)

    if (result!='noresult') {
      var factorList=result.split("*");
      //alert(factorList)

      total_trades=factorList.length-1;
      
      document.getElementById("total_trades").innerHTML = total_trades;

      document.getElementById("trade_history").innerHTML = ""; // clear the list

      for (var i = 0; i<(factorList.length-1); i++) {
        //alert("loop")
        journal_string=factorList[i];

        // add factor string to news list if it has a type of news   

        $('#trade_history').prepend('<li>'+ journal_string +'</li>');    

      }
      
    }
    else{
      setParticipationScore();
    }

    // trade_list= document.getElementById("trade_history").innerHTML;

    //piggy back onto next func
     setupBasicTradeScore()
    
  },
  "text"
  );
}

// ADD THE BASIC TRADE SCORE HERE!
//============= Basic trade score details =============
function setupBasicTradeScore(){

   // alert("total_trades are "+total_trades)

  if (total_trades<10) {
    basic_trading=0;
  }else 
  if (total_trades<=14) {
    basic_trading=3;
  }else 
  if (total_trades<=19) {
    basic_trading=5;
  }else 
  if (total_trades>=20) {
    basic_trading=7;
  }

  // alert("basic_trading val is "+basic_trading)
  // alert("student_id is "+student_id)


  $.post("includes/setupBasicTradeScore.php",
  {PostSTUDENT_ID:student_id,PostBASIC_TRADING:basic_trading},
  function(result){
    // alert(result)
    //piggy back onto next func
    setParticipationScore();
  },
  "text"
  );
}

  // ADD THE BASIC TRADE SCORE HERE!
//============= Basic trade score details =============
function setParticipationScore(){

  theList = document.getElementById('trade_history');
  theLastItem = theList.childNodes[theList.childNodes.length - 1].innerHTML;
   // alert(theLastItem);
   if (theLastItem==undefined) {
    //alert('none')
    // alert(firstTrade)
    participation = 0;
    getTradeTotals();
   };
  preLast = theLastItem.split(':')[0];
  // alert(preLast)
  firstTrade = preLast.replace('day ','');
   //alert(firstTrade)

  participation = 0;

  if (firstTrade<=3) {
    participation = 3;
  }else
  if (firstTrade>3) {
    participation= 1;
  }


  // alert("participation score is "+participation)

  // alert("basic_trading val is "+basic_trading)
  // alert("student_id is "+student_id)


  $.post("includes/setParticipationScore.php",
  {PostSTUDENT_ID:student_id,PostPARTICIPATION:participation},
  function(result){
    // alert(result)
    //piggy back onto next func
    getTradeTotals();
  },
  "text"
  );
}

//============= Update Trade History =============
function getTradeTotals(){

  //alert("getTradeTotals")

  $.post("includes/getTradeTotals.php",
  {PostSTUDENT_ID:student_id,PostGAME_INST_ID:game_id},
  function(result){
    // alert(result)
    // alert(revenueFees)
    var resultList=result.split("*");
    var revenueDividends=Number(resultList[0]);
    var revenueFees=Number(resultList[1]);
    var revenueBank=Number(resultList[2]);
    // alert(revenueDividends)

    revenueBank=Number(revenueBank+revenueDividends)
    // alert(revenueBank)

    revenueBank=Number(revenueBank-revenueFees)
    //alert(revenueBank)

    document.getElementById("revenueDividends").innerHTML = Number(revenueDividends).toFixed(2);
    //alert("revenueFees is "+Number(revenueFees).toFixed(2))
    document.getElementById("revenueFees").innerHTML = Number(revenueFees).toFixed(2);
    document.getElementById("revenueBank").innerHTML = Number(revenueBank).toFixed(2);

    // document.getElementById("revenueBank").innerHTML = Number(revenueBank).toFixed(2);

    
    //piggy back onto next func
    getPortfolioInfo();
  },
  "text"
  ); 
  
}

//============= Get Portfolio Information =============
function getPortfolioInfo(){
  $.post("includes/getPortfolioInfo.php",
  {PostSTUDENT_ID:student_id,PostGAME_ID:game_id},

  function(result){  

    var portfolioList=result.split("~");

    for (var i = 0; i<(comp_id_list.length); i++) {

      for (var j = 0; j<(portfolioList.length); j++) {

        var resultList=portfolioList[j].split("*");
        var company_id=resultList[2];

        if (comp_id_list[i]==company_id) {
          window['share_tally' + (i+1)] = resultList[0];
          break;

        }else{

          window['share_tally' + (i+1)] = 0;

        }

      }
      
    } 

    /* Share Numbers */
    document.getElementById('portTally1').innerHTML = share_tally1;
    document.getElementById('portTally2').innerHTML = share_tally2;
    document.getElementById('portTally3').innerHTML = share_tally3;
    document.getElementById('portTally4').innerHTML = share_tally4;
    document.getElementById('portTally5').innerHTML = share_tally5;
  
    /* Share Totals */
    document.getElementById('portTotal1').innerHTML = (Number(document.getElementById('portTally1').innerHTML) * Number(document.getElementById('portPrice1').innerHTML)).toFixed(2);
    document.getElementById('portTotal2').innerHTML = (Number(document.getElementById('portTally2').innerHTML) * Number(document.getElementById('portPrice2').innerHTML)).toFixed(2); 
    document.getElementById('portTotal3').innerHTML = (Number(document.getElementById('portTally3').innerHTML) * Number(document.getElementById('portPrice3').innerHTML)).toFixed(2); 
    document.getElementById('portTotal4').innerHTML = (Number(document.getElementById('portTally4').innerHTML) * Number(document.getElementById('portPrice4').innerHTML)).toFixed(2); 
    document.getElementById('portTotal5').innerHTML = (Number(document.getElementById('portTally5').innerHTML) * Number(document.getElementById('portPrice5').innerHTML)).toFixed(2);

    /* Revenue Shares Total */
    document.getElementById('revenueSecurities').innerHTML = (Number(document.getElementById('portTotal1').innerHTML) + Number(document.getElementById('portTotal2').innerHTML) + Number(document.getElementById('portTotal3').innerHTML) + Number(document.getElementById('portTotal4').innerHTML) + Number(document.getElementById('portTotal5').innerHTML)).toFixed(2); 

    revenue_total = (Number(document.getElementById('revenueSecurities').innerHTML) + Number(document.getElementById('revenueBank').innerHTML)).toFixed(2);
    document.getElementById('revenueTotal').innerHTML = revenue_total;
    
    //piggy back onto next func
    capitalGain();
    
  },
  "text"
  );
}

function capitalGain(){


  cap_gain = Number(((revenue_total-init_bank)/init_bank)*100).toFixed(2);

  // alert("total is "+revenue_total);
  // alert("init is "+init_bank);
  // alert(revenue_total-init_bank);
  // alert((revenue_total-init_bank)/init_bank);
  // alert((((revenue_total-init_bank)/init_bank)*100).toFixed(2));


  document.getElementById('capital_gain').innerHTML = cap_gain +"%";

  // alert("cap_gain is "+cap_gain)

  if (cap_gain<0.1) {
    advanced_trading=0;
  }else 
  if (cap_gain>=0.1 && cap_gain<=2.99) {
    advanced_trading=1;
  }else 
  if (cap_gain>=3 && cap_gain<=4.99) {
    advanced_trading=2;
  }else 
  if (cap_gain>=5 && cap_gain<=8.99) {
    advanced_trading=5;
  }else 
  if (cap_gain>=9 && cap_gain<=11.99) {
    advanced_trading=9;
  }else 
  if (cap_gain>=12 && cap_gain<=14.99) {
    advanced_trading=12;
  }else 
  if (cap_gain>=15) {
    advanced_trading=15;
  }

  // alert("advanced_trading val is "+advanced_trading)
  // alert("student_id is "+student_id)
  // alert("game_id is "+game_id)

  //piggy back onto next func
    // addToLeaderBoard();
    document.getElementById('loadText').innerHTML="Loading Players"
    setupAdvancedTradeScore();

}

// ADD CAPITAL GAIN TO SCORE TABLE HERE!
//============= Captial Gain Score =============
function setupAdvancedTradeScore(){


  $.post("includes/setupAdvancedTradeScore.php",
  {PostSTUDENT_ID:student_id,PostADVANCED_TRADING:advanced_trading},
  function(result){
    // alert(result)
    //piggy back onto next func
    addToLeaderBoard();
  },
  "text"
  );
}

//============= Add To Leader Board =============
function addToLeaderBoard(){
  // alert("addToLeaderBoard")

  $.post("includes/addToLeaderBoard.php",
  {PostSTUDENT_ID:student_id,PostREV_TOTAL:revenue_total,PostGAME_INST_ID:gameInstID},
  function(result){
    getLeaderBoard();
  },
  "text"
  );
  
}

//============= Get Top 10 Revnues for Leader Board =============
function getLeaderBoard(){

  $.post("includes/getLeaders.php",
  {PostSTUDENT_ID:student_id, PostGAME_ID:game_id},
  function(result){  
    var factorList=result.split("~");
    document.getElementById("leader_board").innerHTML = "";  // clear leader board before repopulating it

    for (var i = 0; i<(factorList.length-1); i++) {
      var resultList=factorList[i].split("*");
      leader_total=resultList[0];
      
      // add factor string to news list if it has a type of news 

      $('#leader_board').append('<li >$'+ Number(leader_total).toFixed(2) +'</li>');  

    }
    
    //piggy back onto next func
    getTotalPlayers();
    
  },
  "text"
  );
}

//============= Get Total Players =============
function getTotalPlayers(){

  $.post("includes/getTotalPlayers.php",
  {PostGAME_ID:game_id},
  function(result){

    var totalPlayers=result;
    document.getElementById("totalPlayers").innerHTML = totalPlayers;

    //piggy back onto next func
    getPlayerRanking();
  },
  "text"
  );

}

//============= Get Player Ranking From Leader Board =============
function getPlayerRanking(){

  $.post("includes/getPlayerRanking.php",
  {PostSTUDENT_ID:student_id,PostGAME_ID:game_id},
  function(result){

    var factorList=result.split("*");

    for (var i = 0; i<(factorList.length-1); i++) {
      // loop though the list until we match the student id
      if (factorList[i]==student_id){
        Number.getOrdinalFor = function(intNum, includeNumber){
          return (includeNumber ? intNum : "") + (((intNum = Math.abs(intNum) % 100)
            % 10 == 1 && intNum != 11) ? "st" : (intNum % 10 == 2 && intNum != 12)
            ? "nd" : (intNum % 10 == 3 && intNum != 13) ? "rd" : "th");
        };
        var playerRanking = i+1; // get the ranking number
        var playerRanking = playerRanking + Number.getOrdinalFor(playerRanking); // ranking number and ordinal
  
        document.getElementById("playerRanking").innerHTML = playerRanking;
        break;
        
      };

    }   
    //piggy back onto next func  
    // checkTypeTotals();
     $('#trading').css('display','none');
    pageLoaded();
  },
  "text"
  );
}

function pageLoaded(){

    $('#loaded').css('display','block');
    $('#loading').css('display','none');

    checkTypeTotals();

}

function checkTypeTotals(){
  //reset
  total_shareNum=0;
  total_commonNum=0;
  total_debentureNum=0;
  $('#buy1').css("display", "inline");
  $('#buy2').css("display", "inline");
  $('#buy3').css("display", "inline");
  $('#buy4').css("display", "inline");
  $('#buy5').css("display", "inline");
  $('#nobuy1').css("display", "none");
  $('#nobuy2').css("display", "none");
  $('#nobuy3').css("display", "none");
  $('#nobuy4').css("display", "none");
  $('#nobuy5').css("display", "none");


  // $('#buy1')(function() {

  //   if (!$("#numBuy1").val()) {
  //     alert("empty")
  //     $('#buy1 input[type="submit"]').attr('disabled', 'disabled');
  //   } else {
  //     alert("full")
  //     $('#buy1 input[type="submit"]').attr('disabled', false);
  //   }
  // });

    //

  // loop through and calc the total numer of securites held
  for (var i = 1; i < comp_id_list.length; i++) {
    
    if (document.getElementById("portTally"+i).innerHTML>0) {
      //alert("got some shares")
      total_shareNum = total_shareNum+1;
      //alert(total_shareNum)
    };
  };

  // get total common and debenture shares held
  if (document.getElementById("portTally1").innerHTML>0) {
    total_commonNum = total_commonNum+1;
  }
  if (document.getElementById("portTally2").innerHTML>0) {
    total_commonNum = total_commonNum+1;
  }
  if (document.getElementById("portTally3").innerHTML>0) {
    total_commonNum = total_commonNum+1;
  }
  if (document.getElementById("portTally4").innerHTML >0) {
    total_debentureNum = total_debentureNum+1;
  }
  if (document.getElementById("portTally5").innerHTML>0) {
    total_debentureNum = total_debentureNum+1;
  }

  

  // // Sell empty val
  // $('#sell1').submit(function(){
  //   thisId = $(this).attr('id'); 
  //   if (!$("#numSell1").val()) {
  //     // textarea is empty
  //     alert("Please enter the number of Securities to sell.");
  //     //$("#numSell1").effect( "bounce", { times:3 }, 500);
  //   }
  // });
  // $('#sell2').submit(function(){
  //   thisId = $(this).attr('id'); 
  //   if (!$("#numSell2").val()) {
  //     // textarea is empty
  //     alert("Please enter the number of Securities to sell.");
  //     //$("#numSell1").effect( "bounce", { times:3 }, 500);
  //   }
  // });
  // $('#sell3').submit(function(){
  //   thisId = $(this).attr('id'); 
  //   if (!$("#numSell3").val()) {
  //     // textarea is empty
  //     alert("Please enter the number of Securities to sell.");
  //     //$("#numSell1").effect( "bounce", { times:3 }, 500);
  //   }
  // });
  // $('#sell4').submit(function(){
  //   thisId = $(this).attr('id'); 
  //   if (!$("#numSell4").val()) {
  //     // textarea is empty
  //     alert("Please enter the number of Securities to sell.");
  //     //$("#numSell1").effect( "bounce", { times:3 }, 500);
  //   }
  // });
  // $('#sell5').submit(function(){
  //   thisId = $(this).attr('id'); 
  //   if (!$("#numSell5").val()) {
  //     // textarea is empty
  //     alert("Please enter the number of Securities to sell.");
  //     //$("#numSell1").effect( "bounce", { times:3 }, 500);
  //   }
  // });

  


  //alert("comp_id_list.length is " +comp_id_list.length);
  for (var i = 0; i<(comp_id_list.length); i++) {

    toggleVal = "toggle-buy" +(i+1);
    tallyVal = "portTally" +(i+1);
    buyVal = "#buy" +(i+1);
    nobuyVal = "#nobuy" +(i+1);
    nobuyToggle = "#toggle-buy" +(i+1);
    //alert(nobuyToggle);

    if ((total_shareNum == 3)&&(document.getElementById(tallyVal).innerHTML<1)) {
      // alert("3 share types, "+tallyVal+" action");
      //alert(nobuyToggle+" should be diabled");
      $(buyVal).css("display", "none");
      $(nobuyVal).css("display", "inline");

    };
    
  }

  if ((total_commonNum == 2)&&(document.getElementById("portTally1").innerHTML<1)) {
    // alert("2 common Shares, portTally1 action");
    $("#buy1").css("display", "none");
    $("#nobuy1").css("display", "inline");
  };

  if ((total_commonNum == 2)&&(document.getElementById("portTally2").innerHTML<1)) {
    // alert("2 common Shares, portTally2 action");
    $("#buy2").css("display", "none");
    $("#nobuy2").css("display", "inline");
  };

  if ((total_commonNum == 2)&&(document.getElementById("portTally3").innerHTML<1)) {
    // alert("2 common Shares, portTally3 action");
    $("#buy3").css("display", "none");
    $("#nobuy3").css("display", "inline");
  };

  if ((total_debentureNum == 1)&&(document.getElementById("portTally4").innerHTML<1)) {
    // alert("1 debenture, portTally4 action");
    $("#buy4").css("display", "none");
    $("#nobuy4").css("display", "inline");
  };

  if ((total_debentureNum == 1)&&(document.getElementById("portTally5").innerHTML<1)) {
    // alert("1 debenture, portTally5 action");
    $("#buy5").css("display", "none");
    $("#nobuy5").css("display", "inline");
  };

  /* UNSURE IF THIS NEEDS TO PIGGY BACK YET? */

  // getTradeHistory();

}

//============= Validate Buy Funcs =============
function calcBuy1(){
  // Check if string is a whole number(digits only).
  var numbersOnlyExpression = /^[0-9]+$/;
  var total = 0;
  tradeFee = ((document.getElementById('numBuy1').value*5)/100).toFixed(2);
  
  if ( (document.getElementById('numBuy1').value.match(numbersOnlyExpression)) || (document.getElementById('numBuy1').value="") ){
    var num = document.getElementById("numBuy1").value;
    var value = document.getElementById("portBuy1").innerHTML;
    document.getElementById("totalBuy1").innerHTML=(num*value).toFixed(2);
  }
  else
  {
    alert("Please enter whole numbers only.")
  }

  // $('form#buy1 input#numBuy1').blur(function(){
  //     if( !$(this).val() ) {
  //           alert("empty")
  //     }
  // });



  // alert("bank: "+Number(document.getElementById('revenueBank').innerHTML))
  // alert("fee: "+tradeFee)
  sharetoBuy = Number(document.getElementById("totalBuy1").innerHTML).toFixed(2);
  // alert(sharetoBuy)
  diff = (Number(sharetoBuy)+Number(tradeFee)).toFixed(2);

  bank = Number(document.getElementById('revenueBank').innerHTML).toFixed(2);

  //alert( Number(diff)+" "+ Number(bank))

  if (Number(diff)>Number(bank)) {
    alert("You don't have enough funds to buy this many shares(including trade fees).")
    // Clear the value if
    document.getElementById("numBuy1").value='';
    document.getElementById("totalBuy1").innerHTML='total';
    
  };
}
/**/
function calcBuy2(){
  // Check if string is a whole number(digits only).
  var numbersOnlyExpression = /^[0-9]+$/;
  var total = 0;
  tradeFee = ((document.getElementById('numBuy2').value*5)/100).toFixed(2);
  if ( (document.getElementById('numBuy2').value.match(numbersOnlyExpression)) || (document.getElementById('numBuy2').value="") ){
    var num = document.getElementById("numBuy2").value;
    var value = document.getElementById("portBuy2").innerHTML;
    document.getElementById("totalBuy2").innerHTML=(num*value).toFixed(2);
  }
  else
  {
    alert("Please enter whole numbers only.")
  }
  // alert("bank: "+Number(document.getElementById('revenueBank').innerHTML))
  // alert("fee: "+tradeFee)
  sharetoBuy = Number(document.getElementById("totalBuy2").innerHTML).toFixed(2);
  // alert(sharetoBuy)
  diff = (Number(sharetoBuy)+Number(tradeFee)).toFixed(2);

  bank = Number(document.getElementById('revenueBank').innerHTML).toFixed(2);

  //alert( Number(diff)+" "+ Number(bank))

  if (Number(diff)>Number(bank)) {
    alert("You don't have enough funds to buy this many shares(including trade fees).")
    // Clear the value if
    document.getElementById("numBuy2").value='';
    document.getElementById("totalBuy2").innerHTML='total';
    
  };
}
/**/
function calcBuy3(){
  // Check if string is a whole number(digits only).
  var numbersOnlyExpression = /^[0-9]+$/;
  var total = 0;
  tradeFee = ((document.getElementById('numBuy3').value*5)/100).toFixed(2);
  if ( (document.getElementById('numBuy3').value.match(numbersOnlyExpression)) || (document.getElementById('numBuy3').value="") ){
    var num = document.getElementById("numBuy3").value;
    var value = document.getElementById("portBuy3").innerHTML;
    document.getElementById("totalBuy3").innerHTML=(num*value).toFixed(2);
  }
  else
  {
    alert("Please enter whole numbers only.")
  }
  // alert("bank: "+Number(document.getElementById('revenueBank').innerHTML))
  // alert("fee: "+tradeFee)
  sharetoBuy = Number(document.getElementById("totalBuy3").innerHTML).toFixed(2);
  // alert(sharetoBuy)
  diff = (Number(sharetoBuy)+Number(tradeFee)).toFixed(2);

  bank = Number(document.getElementById('revenueBank').innerHTML).toFixed(2);

  //alert( Number(diff)+" "+ Number(bank))

  if (Number(diff)>Number(bank)) {
    alert("You don't have enough funds to buy this many shares(including trade fees).")
    // Clear the value if
    document.getElementById("numBuy3").value='';
    document.getElementById("totalBuy3").innerHTML='total';
    
  };
}
/**/
function calcBuy4(){
  // Check if string is a whole number(digits only).
  var numbersOnlyExpression = /^[0-9]+$/;
  var total = 0;
  tradeFee = ((document.getElementById('numBuy4').value*5)/100).toFixed(2);
  //alert(tradeFee)
  if ( (document.getElementById('numBuy4').value.match(numbersOnlyExpression)) || (document.getElementById('numBuy4').value="") ){
    var num = document.getElementById("numBuy4").value;
    var value = document.getElementById("portBuy4").innerHTML;
    document.getElementById("totalBuy4").innerHTML=(num*value).toFixed(2);
  }
  else
  {
    alert("Please enter whole numbers only.")
  }
  // alert("bank: "+Number(document.getElementById('revenueBank').innerHTML))
  // alert("fee: "+tradeFee)
  sharetoBuy = Number(document.getElementById("totalBuy4").innerHTML).toFixed(2);
  // alert(sharetoBuy)
  diff = (Number(sharetoBuy)+Number(tradeFee)).toFixed(2);

  bank = Number(document.getElementById('revenueBank').innerHTML).toFixed(2);

  //alert( Number(diff)+" "+ Number(bank))

  if (Number(diff)>Number(bank)) {
    alert("You don't have enough funds to buy this many shares(including trade fees).")
    // Clear the value if
    document.getElementById("numBuy4").value='';
    document.getElementById("totalBuy4").innerHTML='total';
    
  };
}
/**/
function calcBuy5(){
  // Check if string is a whole number(digits only).
  var numbersOnlyExpression = /^[0-9]+$/;
  var total = 0;
  tradeFee = ((document.getElementById('numBuy5').value*5)/100).toFixed(2);
  if ( (document.getElementById('numBuy5').value.match(numbersOnlyExpression)) || (document.getElementById('numBuy5').value="") ){
    var num = document.getElementById("numBuy5").value;
    var value = document.getElementById("portBuy5").innerHTML;
    document.getElementById("totalBuy5").innerHTML=(num*value).toFixed(2);
  }
  else
  {
    alert("Please enter whole numbers only.")
  }
  // alert("bank: "+Number(document.getElementById('revenueBank').innerHTML))
  // alert("fee: "+tradeFee)
  sharetoBuy = Number(document.getElementById("totalBuy5").innerHTML).toFixed(2);
  // alert(sharetoBuy)
  diff = (Number(sharetoBuy)+Number(tradeFee)).toFixed(2);

  bank = Number(document.getElementById('revenueBank').innerHTML).toFixed(2);

  //alert( Number(diff)+" "+ Number(bank))

  if (Number(diff)>Number(bank)) {
    alert("You don't have enough funds to buy this many shares(including trade fees).")
    // Clear the value if
    document.getElementById("numBuy5").value='';
    document.getElementById("totalBuy5").innerHTML='total';
    
  };
}
//============= Validate Sell Funcs =============
function calcSell1(){
  // Check if string is a whole number(digits only).
  var numbersOnlyExpression = /^[0-9]+$/;
  var total = 0;
  if ( (document.getElementById('numSell1').value.match(numbersOnlyExpression)) || (document.getElementById('numSell1').value="") ){
    var num = document.getElementById("numSell1").value;
    var value = document.getElementById("portSell1").innerHTML;
    document.getElementById("totalSell1").innerHTML=(num*value).toFixed(2);
  }
  else
  {
    alert("Please enter whole numbers only.")
  }
  if (Number(document.getElementById("numSell1").value)>Number(document.getElementById('portTally1').innerHTML)) {
    alert("You don't have this many shares to sell.")
    document.getElementById("numSell1").value='';
    document.getElementById("totalSell1").innerHTML='total';
  };
}
/**/
function calcSell2(){
  // Check if string is a whole number(digits only).
  var numbersOnlyExpression = /^[0-9]+$/;
  var total = 0;
  if ( (document.getElementById('numSell2').value.match(numbersOnlyExpression)) || (document.getElementById('numSell2').value="") ){
    var num = document.getElementById("numSell2").value;
    var value = document.getElementById("portSell2").innerHTML;
    document.getElementById("totalSell2").innerHTML=(num*value).toFixed(2);
  }
  else
  {
    alert("Please enter whole numbers only.")
  }
  if (Number(document.getElementById("numSell2").value)>Number(document.getElementById('portTally2').innerHTML)) {
    alert("You don't have this many shares to sell.")
    document.getElementById("numSell2").value='';
    document.getElementById("totalSell2").innerHTML='total';
  };
}
/**/
function calcSell3(){
  // Check if string is a whole number(digits only).
  var numbersOnlyExpression = /^[0-9]+$/;
  var total = 0;
  if ( (document.getElementById('numSell3').value.match(numbersOnlyExpression)) || (document.getElementById('numSell3').value="") ){
    var num = document.getElementById("numSell3").value;
    var value = document.getElementById("portSell3").innerHTML;
    document.getElementById("totalSell3").innerHTML=(num*value).toFixed(2);
  }
  else
  {
    alert("Please enter whole numbers only.")
  }
  if (Number(document.getElementById("numSell3").value)>Number(document.getElementById('portTally3').innerHTML)) {
    alert("You don't have this many shares to sell.")
    document.getElementById("numSell3").value='';
    document.getElementById("totalSell3").innerHTML='total';
  };
}
/**/
function calcSell4(){
  // Check if string is a whole number(digits only).
  var numbersOnlyExpression = /^[0-9]+$/;
  var total = 0;
  if ( (document.getElementById('numSell4').value.match(numbersOnlyExpression)) || (document.getElementById('numSell4').value="") ){
    var num = document.getElementById("numSell4").value;
    var value = document.getElementById("portSell4").innerHTML;
    document.getElementById("totalSell4").innerHTML=(num*value).toFixed(2);
  }
  else
  {
    alert("Please enter whole numbers only.")
  }
  if (Number(document.getElementById("numSell4").value)>Number(document.getElementById('portTally4').innerHTML)) {
    alert("You don't have this many shares to sell.")
    document.getElementById("numSell4").value='';
    document.getElementById("totalSell4").innerHTML='total';
  };
}
/**/
function calcSell5(){
  // Check if string is a whole number(digits only).
  var numbersOnlyExpression = /^[0-9]+$/;
  var total = 0;
  if ( (document.getElementById('numSell5').value.match(numbersOnlyExpression)) || (document.getElementById('numSell5').value="") ){
    var num = document.getElementById("numSell5").value;
    var value = document.getElementById("portSell5").innerHTML;
    document.getElementById("totalSell5").innerHTML=(num*value).toFixed(2);
  }
  else
  {
    alert("Please enter whole numbers only.")
  }
  if (Number(document.getElementById("numSell5").value)>Number(document.getElementById('portTally5').innerHTML)) {
    alert("You don't have this many shares to sell.")
    document.getElementById("numSell5").value='';
    document.getElementById("totalSell5").innerHTML='total';
  };
}

</script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js" type="text/javascript"></script>

<script type="text/javascript">

/* Journalcount */

$(document).ready(function() {

  /**/
$("[class^='Journalcount[']").each(function() {
        var elClass = $(this).attr('class');
        var numWords1;
        // var acceptWords = 1000;
        var minWords1 = 0;
        var maxWords1 = 0;
        var countControl1 = elClass.substring((elClass.indexOf('['))+1, elClass.lastIndexOf(']')).split(',');
        
        if(countControl1.length > 1) {
            minWords1 = countControl1[0];
            maxWords1 = countControl1[1];
        } else {
            maxWords1 = countControl1[0];
        }   
        
        $(this).after('<div class="wordCount"><strong>0</strong> words</div>');
        if(minWords1 > 0) {
            $(this).siblings('.wordCount').addClass('error');
        }   
        
        $(this).bind('keyup click blur focus change paste', function() {
            numWords1 = jQuery.trim($(this).val()).split(' ').length;
            if($(this).val() === '') {
                numWords1 = 0;
            }   
            $(this).siblings('.wordCount').children('strong').text(numWords1);
            
            if(numWords1 < minWords1 || (numWords1 > maxWords1 && maxWords1 != 0)) {
                $(this).siblings('.wordCount').addClass('error');
            } else {
                $(this).siblings('.wordCount').removeClass('error'); 
            }
        });
        /**/
        numWords1 = jQuery.trim($('#journalEntry').val()).split(' ').length;
        $('#trade-journal').submit(function() {
          //Do stuff here
          //alert("Attempting to submit.");
          // alert("You have entered "+numWords1+" words.");
          if (numWords1 < minWords1) {
            alert("Each Journal and Reporting entry must be at least "+minWords1+" words.");
            return false;
          }
          else if (numWords1 > maxWords1) {
            alert("A Journal and Reporting entry cannot excede "+maxWords1+" words.");
            return false;
          }
          else {
            //alert("You entered between "+minWords1+" and "+maxWords1+" words.");
          }
       }); 
        
    });
  /**/

    $("[class^='count[']").each(function() {
        var elClass = $(this).attr('class');
        var numWords;
        // var acceptWords = 1000;
        var minWords = 0;
        var maxWords = 0;
        var countControl = elClass.substring((elClass.indexOf('['))+1, elClass.lastIndexOf(']')).split(',');
        
        if(countControl.length > 1) {
            minWords = countControl[0];
            maxWords = countControl[1];
        } else {
            maxWords = countControl[0];
        }   
        
        $(this).after('<div class="wordCount"><strong>0</strong> words</div>');
        if(minWords > 0) {
            $(this).siblings('.wordCount').addClass('error');
        }   
        
        $(this).bind('keyup click blur focus change paste', function() {
            numWords = jQuery.trim($(this).val()).split(' ').length;
            if($(this).val() === '') {
                numWords = 0;
            }   
            $(this).siblings('.wordCount').children('strong').text(numWords);
            
            if(numWords < minWords || (numWords > maxWords && maxWords != 0)) {
                $(this).siblings('.wordCount').addClass('error');
            } else {
                $(this).siblings('.wordCount').removeClass('error'); 
            }
        });
        /**/
        numWords = jQuery.trim($('#summaryReport').val()).split(' ').length;
        $('#summary-report').submit(function() {
          //Do stuff here
          // alert("Attempting to submit.");
          // alert("You entered "+numWords+" words.");
          if (numWords < minWords) {
            alert("Each entry must be at least "+minWords+".");
            return false;
          }
          else if (numWords > maxWords) {
            alert("Too many words. You cannot enter more than "+maxWords+".");
            return false;
          }
          else {
            alert("You entered between "+minWords+" and "+maxWords+" words.");
          }
       }); 
        
    });
});
</script>


<!-- PRINT DIV -->
<script language="javascript">

function PrintMe(DivID) {
var disp_setting="toolbar=yes,location=no,";
disp_setting+="directories=yes,menubar=yes,";
disp_setting+="scrollbars=yes,width=650,height=600,left=100,top=25";
   var content_value = document.getElementById(DivID).innerHTML;
   var docprint=window.open("","",disp_setting);
   docprint.document.open();
   docprint.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"');
   docprint.document.write('"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
   docprint.document.write('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">');
   docprint.document.write('<head><title>My First Trading</title>');
   docprint.document.write('<style type="text/css">body{ margin:0px;');
   docprint.document.write('padding:20px;');
   docprint.document.write('color:#000;');
   docprint.document.write('font-family:Verdana, Geneva, sans-serif; font-size:13px;}');
   docprint.document.write('li{list-style-type:none;}');
   docprint.document.write('li.journal-day{font-weight:bold;margin-top:18px;}');
   docprint.document.write('a{color:#000;text-decoration:none;} </style>');
   docprint.document.write('</head><body onLoad="self.print()">');
   docprint.document.write(content_value);
   docprint.document.write('</body></html>');
   docprint.document.close();
   docprint.focus();

}

function printTextArea() {
  childWindow = window.open('','childWindow','location=no,directories=yes,menubar=yes,toolbar=yes,scrollbars=yes,width=650,height=600,left=100,top=25');
  childWindow.document.open();
  childWindow.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"');
   childWindow.document.write('"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
   childWindow.document.write('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">');
  childWindow.document.write('<head><title>My First Trading</title>');
  childWindow.document.write('<style type="text/css">body{ margin:0px;');
   childWindow.document.write('padding:20px;');
   childWindow.document.write('color:#000;');
   childWindow.document.write('font-family:Verdana, Geneva, sans-serif; font-size:13px;}');
   childWindow.document.write('li{list-style-type:none;}');
   childWindow.document.write('a{color:#000;text-decoration:none;} </style>');
   childWindow.document.write('</head><body>');
  childWindow.document.write(document.getElementById('summaryReport').value.replace(/\n/gi,'<br>'));
  childWindow.document.write('</body></html>');
  childWindow.print();
  childWindow.document.close();
  childWindow.close();
}

</script>
<!-- -->

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

  <div id="loaded" class="container">
  <!-- Begin header content -->
  <header id="navtop" class="grid-wrap">
    <div class="grid col-two-thirds">
       <h1>My First Trading</h1>
      <h2 id="student-Name">Welcome <span id="firstName"> </span> <span id="lastName"> </span></h2>
    </div>
    <div class="grid col-one-third">
      <p class="alignright">Current Trading Period: <span class="highlight" id="currentDay"> </span></p>
      <p class="alignright">Trading Periods Remaining: <span class="highlight" id="remainingDay"></span></p>
    </div>
  </header>
  <!-- End header content -->

  <section class="grid-wrap">
    <!-- Begin Portfolio content -->
    <article class="grid col-one-half">
      <header>
        <h3>My Portfolio <span class="small"> (Maximum 2 common shares plus 1 Debenture) </span></h3>
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
        <tr class="first-row">
          <td id="portName1"> </td>
          <td id="portTally1"> 0 </td>
          <td id="portPrice1"> </td>
          <td id="portTotal1"> 0 </td>
          <td>
            <div class="buy1">
                <form action="JavaScript:buyShares(1)" method="post" id="buy1" >
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
                <form action="JavaScript:sellShares(1)" method="post" id="sell1">
                  <input name="shares" id="numSell1" class="valEmpty" onkeyup="calcSell1()" type="text" placeholder="shares"/>
                  <p>x <span id="portSell1"> </span></p>
                  <p>= <span id="totalSell1">total</span></p>
                  <input name="confirm" id="sell1" type="submit" value="TRADE" />
                </form>
            </div>
        </td>
        </tr>
        <!-- -->
        <tr class="second-row">
          <td id="portName2"> </td>
          <td id="portTally2"> 0 </td>
          <td id="portPrice2"> </td>
          <td id="portTotal2"> 0 </td>
          <td>
            <div class="buy2">
                <form action="JavaScript:buyShares(2)" method="post" id="buy2">
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
                <form action="JavaScript:sellShares(2)" method="post" id="sell2">
                  <input name="shares" id="numSell2" class="valEmpty" onkeyup="calcSell2()" type="text" placeholder="shares"/>
                  <p>x <span id="portSell2"> </span></p>
                  <p>= <span id="totalSell2">total</span></p>
                  <input name="confirm" id="sell2" type="submit" value="TRADE" />
                </form>
            </div>
        </td>
        </tr>
        <!-- -->
        <tr class="third-row">
          <td id="portName3"> </td>
          <td id="portTally3"> 0 </td>
          <td id="portPrice3"> </td>
          <td id="portTotal3"> 0 </td>
          <td>
            <div class="buy3">
                <form action="JavaScript:buyShares(3)" method="post"  id="buy3">
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
                <form action="JavaScript:sellShares(3)" method="post" id="sell3">
                  <input name="shares" id="numSell3" class="valEmpty" onkeyup="calcSell3()" type="text" placeholder="shares"/>
                  <p>x <span id="portSell3"> </span></p>
                  <p>= <span id="totalSell3">total</span></p>
                  <input name="confirm" id="sell3" type="submit" value="TRADE" />
                </form>
            </div>
        </td>
        </tr>
        <!-- -->
        <tr class="fourth-row">
          <td id="portName4"> </td>
          <td id="portTally4"> 0 </td>
          <td id="portPrice4"> </td>
          <td id="portTotal4"> 0 </td>
          <td>
            <div class="buy4">
                <form action="JavaScript:buyShares(4)" method="post" id="buy4">
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
                <form action="JavaScript:sellShares(4)" method="post" id="sell4">
                  <input name="shares" id="numSell4" class="valEmpty" onkeyup="calcSell4()" type="text" placeholder="shares"/>
                  <p>x <span id="portSell4"> </span></p>
                  <p>= <span id="totalSell4">total</span></p>
                  <input name="confirm" id="sell4" type="submit" value="TRADE" />
                </form>
            </div>
        </td>
        </tr>
        <!-- -->
        <tr class="fifth-row">
          <td id="portName5"> </td>
          <td id="portTally5"> 0 </td>
          <td id="portPrice5"> </td>
          <td id="portTotal5"> 0 </td>
          <td>
            <div class="buy5">
                <form action="JavaScript:buyShares(5)" method="post" id="buy5">
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
                <form action="JavaScript:sellShares(5)" method="post" id="sell5">
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
        <h3>Market Prices <span class="small">(Changes 1 minute after mid-night each day)</span></h3>
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
        <tr class="first-row">
          <td id="marketName1"> </td>
          <td id="marketOrigin1"> </td>
          <td id="marketInd1"> </td>
          <td id="marketType1"> </td>
          <td id="marketPrevious1"> </td>
          <td id="marketPrice1"> </td>
          <td id="marketComp1"> </td>
        </tr>
        <tr class="second-row">
          <td id="marketName2"> </td>
          <td id="marketOrigin2"> </td>
          <td id="marketInd2"> </td>
          <td id="marketType2"> </td>
          <td id="marketPrevious2"> </td>
          <td id="marketPrice2"> </td>
          <td id="marketComp2"> </td>
        </tr>
        <tr class="third-row">
          <td id="marketName3"> </td>
          <td id="marketOrigin3"> </td>
          <td id="marketInd3"> </td>
          <td id="marketType3"> </td>
          <td id="marketPrevious3"> </td>
          <td id="marketPrice3"> </td>
          <td id="marketComp3"> </td>
        </tr>
        <tr class="fourth-row">
          <td id="marketName4"> </td>
          <td id="marketOrigin4"> </td>
          <td id="marketInd4"> </td>
          <td id="marketType4"> </td>
          <td id="marketPrevious4"> </td>
          <td id="marketPrice4"> </td>
          <td id="marketComp4"> </td>
        </tr>
        <tr class="fifth-row">
          <td id="marketName5"> </td>
          <td id="marketOrigin5"> </td>
          <td id="marketInd5"> </td>
          <td id="marketType5"> </td>
          <td id="marketPrevious5"> </td>
          <td id="marketPrice5"> </td>
          <td id="marketComp5"> </td>
        </tr>
      </table>

      <a href="price-archives.php" target="blank" style="text-decoration:none">View price archives</a>

    </article>
    <!-- End Market Prices content -->
  </section>

  <section class="grid-wrap">
    <!-- Begin Revenue content -->
    <article class="grid col-one-quarter">
      <header>
        <h3>My Revenue</h3>
      </header>
      <table class="revenue">
        <tr>
          <td>Securities:</td>
          <td></td>
          <td id="revenueSecurities"> </td>
        </tr>
        <tr>
          <td>Bank Balance:</td>
          <td class="plus">+</td>
          <td id="revenueBank"> </td>
        </tr>
        <tr class="line">
          <td> </td>
          <td></td>
          <td><hr></td>
        </tr>
          <td>Total Revenue:</td>
          <td></td>
          <td id="revenueTotal"> </td>
        </tr>
      </table>

        <p>Total Dividends Received: $<span id="revenueDividends">0.00</span></p>
        <p>Total Trade Fees (5c per security NOT per trade) Paid: $<span id="revenueFees">0.00</span></p>

        
    </article>
    <!-- End Revenue content -->

    <!-- Begin Stats content -->
    <article class="grid col-one-quarter">
      <header>
        <h3>My Game Statistics</h3>
      </header>
      <table class="revenue">
        <tr>
          <td>Total trades:</td>
          <td id="total_trades"> 0</td>
        </tr>
        <tr>
          <td>Capital gain (realised plus expected)<sup>1</sup>:</td>
          <td id="capital_gain"> </td>
        </tr>
        <tr>
          <td>Journal entries:</td>
          <td id="journal-total"> </td>
        </tr>
        <!-- <tr>
          <td>Journal words count:</td>
          <td id="wordCount"> </td>
        </tr> -->
        <tr>
          <td>Leader board ranking:</td>
          <td> <span id="playerRanking"></span> out of <span id="totalPlayers"> </td>
        </tr>
      </table>
    </article>
    <!-- End Stats content -->

    <!-- Begin News content -->
    <article class="grid col-one-half">
      <header>
        <h3>News <span class="small">(Published 7 am in the morning everyday)</span></h3>
      </header>

      <ul id="news" class="news">
        <!-- <li>U.S. Stocks Fall as Optimism Fades After Jobs Report</li>
        <li>The Australian sharemarket looks set to open flat following strong falls in US stocks</li>
        <li>The Australian market was flat at noon despite positive offshore leads, as investors sold off Australian assets</li> -->
      </ul>

      <a href="news.php" target="blank" style="text-decoration:none">View news archives</a>

           <p class="small">The assignment for MAF101, an on-line share trading game, has started. The game will continue for following 28 days. Please follow the assessment requirements in the instruction guide very carefully so that you don't miss out any of its essential parts.<br/>
        The news items linked to this share trading game will be released above every day. So please keep an eye on these news events.</p>

    </article>
    <!-- End News content -->
  </section>

   <section class="grid-wrap">
      <!-- Begin recap -->
      <article class="grid">
        <p class="note"><sup>1</sup>The capital gain (%) figure shown in "My Game Statistics" indicates your current capital gain based on current security prices in the market, it will change as the price of securities in your portfolio changes. So you must understand that such capital gain is not entirely your realised or earned capital gain, it rather indicates the realised capital gain that you have earned PLUS any possible capital gain opportunity prevailing in the market together. So to capture all the capital gain opportunities in the market, whenever there is a favourable price change and such change indicates to a possible capital gain, you must realise such gain by selling your securities in the market. However, you don't have to sell your securities on the final day of the game to realise such capital gain. Your capital gain indicated based on your final holding of securities on the final day would be considered as your earned capital gain.
        </p>
      </article>
    </section>


  <section class="grid-wrap">
    <!-- Begin Journal content -->
    <article class="grid col-one-third">
      <header>
        <h3>Trade Journal and Reporting</h3>
      </header>
      <p>Keep Trade Journal notes and Reporting entries<sup>2</sup></p>
      <p class="note green"><sup>2</sup>Journal and Reporting entries are not editable once saved. Please keep a "Word file" of all your journals, make all corrections and changes in this Word file first, then once you are satisfied with your journals please copy them in the simulation. The word limit for journal entries is 1-90 words.</p>
      <div class="journal">
        <ul id="journal_list" class="journal_list">

        </ul>
      </div>
      
        <form id="trade-journal" class="trade-journal" action="JavaScript:validateJournalEntry()" method="post" name="">
        <ul>
          <li>
            <label for="notes">My Trade Journal</label>
            <textarea name="journalEntry" id="journalEntry" class="Journalcount[1,90]" cols="100" rows="2" placeholder="Enter your journal notes"></textarea>
          </li>
          <li>
            <button type="submit" id="submit" name="submit" class="button fright">SAVE</button>
          </li>
          <li>
            <!-- PRINT TRADE JOURNAL-->

            <button type="button" value="Print History" onclick="javascript:PrintMe('journal_list')" />PRINT JOURNAL</button>
            
            <!-- -->

          </li>
        </ul>
      </form>
    </article>
    <!-- End Journal content -->

    <!-- Begin History content -->
    <article class="grid col-one-third">
      <header>
        <h3>My Trader History</h3>
      </header>
      <ul id="trade_history" class="trade_history">
          <!-- history gets rendered here -->
      </ul>

      <!-- PRINT TRADE HISTORY-->
      
      <button type="button" value="Print History" onclick="javascript:PrintMe('trade_history')" />PRINT HISTORY</button>
      
      <!-- -->

    </article>
    <!-- End History content -->

    <!-- Begin Leader Board content -->
    <article class="grid col-one-third">
      <header>
        <h3>Trader Leader Board</h3>
      </header>
      <p>These are the top 10 revenue totals for this game.</p>
      <ol id="leader_board">

      </ol>
	  <p>&nbsp;</p>
	 <p> Dear students,<br />
Some of you are using foreign language keyboard to participate in the Security Trading Simulation. Entries made by such keyboard is causing continuous glitches into the game.<br />
May I request you NOT to use such keyboard as our system cannot read your action command and creates unnecessary glitches?<br />
Thanks for your cooperation.</p>
<p>Regards,<br />
Annette Nguyen<br />
(Unit Chair)</p>
    </article>
    <!-- End Leader Board content -->
  </section>


</div>

<div id="loading">

  <h2>The 'My First Trading' game is now loading</h2>
  <h3>Please be patient</h3>
  <div id="gif"></div>
  <p id="loadText"></p>
  
</div>

<div id="trading">
  <h2>Please wait while your<br />transaction is being processed.</h2>
  <div id="gif"></div>
  <h2>Do not close or refresh your browser window while this message this displayed.</h2>
</div>

<!-- Begin toTop content -->
<div id="toTop">  &uarr; return to top</div>
<!-- End toTop content -->

<!-- jquery -->
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

<script src="js/scripts.js"></script>

<script src="js/wordcount.js" type="text/javascript"></script>
<!-- easing plugin ( optional ) -->
<script src="js/easing.js" type="text/javascript"></script>

</body>
</html>
