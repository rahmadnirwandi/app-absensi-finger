function bg_color_view(){
    $value_bg_color=$(document).find("#bg_color");
    $value_bg_color=$value_bg_color.val();

    $bagan_bg_color=$(document).find("#color_view");
    $bagan_bg_color.css("background",$value_bg_color);
}

$(document).find("#bg_color").on("change", function () {
    bg_color_view();
});

$(document).ready(function(){
    bg_color_view();
});