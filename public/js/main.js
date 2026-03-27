function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + exdays * 1000);
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(";");
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == " ") {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

// Setup daterange picker
$("#daterange").daterangepicker({
    startDate: new Date(),
    endDate: new Date(),
    locale: {
        format: "DD/MM/YYYY",
    },
    showDropdowns: true,
    linkedCalendars: false,
});

function removeLocalStrg(){
    localStorage.removeItem("tesDatas")
    setCookie("parentCheckbox", null, 1)
    setCookie("nomorPermintaan", '', 0)
    setCookie("indikasi", '', 0)
}

// Halaman Data Pasien
// Variabel untuk tampilkan row data
var e = document.getElementById("row-data");

function setValue(item) {
    document.getElementById("poliklinik").value = item;
    document.getElementById("close").click();
}
// --------------------------


$(".btn-back").on("click", function(){
    window.location.href = getCookie("previousLink");
})