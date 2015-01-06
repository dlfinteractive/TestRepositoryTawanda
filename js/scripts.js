$(document).ready(function() {

    $('.buy1').hide().before('<a href="#" id="toggle-buy1" class="button buy">BUY</a>');
    $('a#toggle-buy1').click(function() {
        $('.buy1').slideToggle(1000);
        return false;
    });
    $('.sell1').hide().before('<a href="#" id="toggle-sell1" class="button">SELL</a>');
    $('a#toggle-sell1').click(function() {
        $('.sell1').slideToggle(1000);
        return false;
    });

    $('.buy2').hide().before('<a href="#" id="toggle-buy2" class="button buy">BUY</a>');
    $('a#toggle-buy2').click(function() {
        $('.buy2').slideToggle(1000);
        return false;
    });
    $('.sell2').hide().before('<a href="#" id="toggle-sell2" class="button">SELL</a>');
    $('a#toggle-sell2').click(function() {
        $('.sell2').slideToggle(1000);
        return false;
    });

    $('.buy3').hide().before('<a href="#" id="toggle-buy3" class="button buy">BUY</a>');
    $('a#toggle-buy3').click(function() {
        $('.buy3').slideToggle(1000);
        return false;
    });
    $('.sell3').hide().before('<a href="#" id="toggle-sell3" class="button">SELL</a>');
    $('a#toggle-sell3').click(function() {
        $('.sell3').slideToggle(1000);
        return false;
    });

    $('.buy4').hide().before('<a href="#" id="toggle-buy4" class="button buy">BUY</a>');
    $('a#toggle-buy4').click(function() {
        $('.buy4').slideToggle(1000);
        return false;
    });
    $('.sell4').hide().before('<a href="#" id="toggle-sell4" class="button">SELL</a>');
    $('a#toggle-sell4').click(function() {
        $('.sell4').slideToggle(1000);
        return false;
    });

    $('.buy5').hide().before('<a href="#" id="toggle-buy5" class="button buy">BUY</a>');
    $('a#toggle-buy5').click(function() {
        $('.buy5').slideToggle(1000);
        return false;
    });
    $('.sell5').hide().before('<a href="#" id="toggle-sell5" class="button">SELL</a>');
    $('a#toggle-sell5').click(function() {
        $('.sell5').slideToggle(1000);
        return false;
    });

    $('.journal').hide().before('<a href="#" id="toggle-journal" class="button">OPEN/CLOSE YOUR JOURNAL</a>');
    $('a#toggle-journal').click(function() {
        $('.journal').slideToggle(1000);
        return false;
    });
});