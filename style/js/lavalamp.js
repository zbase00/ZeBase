// JavaScript Document

$(document).ready(function () {  
  
    //get the current position of the active item  
    var dleft = $('.lavalamp li.active').offset().left - $('.lavalamp').offset().left;  
    var dwidth = $('.lavalamp li.active').width() + "px";  
  
    //apply the current position of active item to our floatr element  
    $('.floatr').css({  
        "left": dleft+"px",  
        "width": dwidth  
    });  
  
    $('.lavalamp li').hover(function(){  
  
        var left = $(this).offset().left - ($(this).parents('.lavalamp').offset().left + 15);  
        var width = $(this).width() + "px";  
        var sictranslate = "translate("+left+"px, 0px)";  
  
        $(this).parent('ul').next('div.floatr').css({  
            "width": width,  
            "-webkit-transform": sictranslate,  
            "-moz-transform": sictranslate  
        });  
  
    },  
  
    function(){  
  
        var left = $(this).siblings('li.active').offset().left - ($(this).parents('.lavalamp').offset().left + 15);  
        var width = $(this).siblings('li.active').width() + "px";  
  
        var sictranslate = "translate("+left+"px, 0px)";  
  
        $(this).parent('ul').next('div.floatr').css({  
            "width": width,  
            "-webkit-transform": sictranslate,  
            "-moz-transform": sictranslate  
  
        });  
  
    }).click(function(){  
  
        $(this).siblings('li').removeClass('active');  
  
        $(this).addClass('active');  
  
        return false;  
  
    });  
  
});  
