const urlParam = new URLSearchParams(location.search);

let dataStorage = window.localStorage.getItem("halaman")

// Pengaturan minimize sidebar
function minimize() {

    $e=$('.title');
    var sidebar = document.querySelector(".sidebar")
    var titleMd = $(".md-show")
    var titleSm = $(".sm-show")
    
    if ($e.css('display') === "none") {
        $e.each(function( ) {
            $(this).css('display',"inline-block");
        })
        sidebar.style.width = "15rem"
        titleMd.css("display", "inline-block")
        titleSm.css("display", "none")
    }else{
        $e.each(function( ) {
            $(this).css('display','none');
        })
        sidebar.style.width = "auto"
        titleMd.css("display", "none")
        titleSm.css("display", "inline-block")
    }
}

$(".toggle-sidebar").click(function (e) {
    $(".sidebar").toggleClass('sidebar-show')
    $(".sidebar").after("<div class='overlay-canva'></div>")
})

$(document).on('click', '.overlay-canva', function () {
    if ($('.sidebar').hasClass('sidebar-show')) {
        $(".sidebar").toggleClass('sidebar-show')
    }
    $(".overlay-canva").remove()
})