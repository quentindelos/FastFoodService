$(document).ready(function(){
    $(".card").hover(function(){
        $(this).addClass("shadow-lg");
    }, function(){
        $(this).removeClass("shadow-lg");
    });
});
