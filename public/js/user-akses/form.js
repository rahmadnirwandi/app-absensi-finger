$("#btn-show-password").on('click',function() {
    $parent = $(this).parents('.form-pass');
    $target=$parent.find('#password');
    $target_icon=$(this).find('i');
    
    if ($target.attr("type") === "password") {
        $target.attr("type", "text");
    } else {
        $target.attr("type", "password");
    }

    if ($target_icon.hasClass("fa-eye")) {
        $target_icon.removeClass("fa-eye");
        $target_icon.addClass("fa-eye-slash");
    }else{
        $target_icon.removeClass("fa-eye-slash");
        $target_icon.addClass("fa-eye");
    }
});