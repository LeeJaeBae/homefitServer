$(".fa fa-chevron-down").click(function(e) {
    e.preventDefault(); // 클릭했을때 샵이 이동하는 값을 차단
    //$("#cont_nav").css("display","block");
    //$("#cont_nav").show();                     //display를 none에서 나타나게 해줌
    //$("#cont_nav").fadeIn();                     // 서서히 나타나는 효과
    //$("#cont_nav").slideDown();              // 미끄러 지듯이 나타나는 효과
    //$("#cont_nav").toggle();                   // 나왔다가 들어가는 효과
    //$("#cont_nav").fadeToggle();             // 서서히 나왔다가 들어가는 효과
    $(".text").slideToggle(200);
    $(this).addClass("on");
});
