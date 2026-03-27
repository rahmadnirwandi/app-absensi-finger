$("#checkedAll").change(function () {
    if (this.checked) {
        $(".checkSingle").each(function () {
            this.checked = true;
        })
    } else {
        $(".checkSingle").each(function () {
            this.checked = false;
        })
    }
});

$(".checkSingle").click(function () {
    if ($(this).is(":checked")) {
        var isAllChecked = 0;
        
        $(".checkSingle").each(function () {
            if (!this.checked)
                isAllChecked = 1;
        })
        if (isAllChecked == 0) {
            $("#checkedAll").prop("checked", true);
        }
    } else {
        $("#checkedAll").prop("checked", false);
    }
});

// $("form").on("submit", function (e) {
//     e.preventDefault()
//     console.clear()
//     if (!FormIsChanged()) {
//         $("#alert_no_changes").removeClass("d-none")
//         return;
//     }
//     let updatedData = {
//         "alias_group": alias
//     };

//     let formData = new FormData(this)

//     if (!arrayIsSame(init_data.routes_ids, getCurrentCheckVal())) {
//         updatedData.authPermission = {}
//         if (getCurrentCheckVal().length === 0) {
//             updatedData.authPermission.empty = true;
//         } else {
//             updatedData.authPermission.empty = false;
//             updatedData.authPermission.routes_data = []
//             for (const [key, value] of formData) {
//                 if (key.includes("routes_")) {
//                     updatedData.authPermission.routes_data.push({
//                         "alias_group": alias,
//                         "url": value
//                     })
//                 }
//             }
//         }
//     }
//     $.ajaxSetup({
//         headers: {
//             'X-CSRF-TOKEN': formData.get("_token")
//         }
//     });
//     $.ajax({
//         type: "PUT",
//         url: `${base_url}/permission-group-app/update`,
//         data: updatedData,
//         dataType: "json",
//         cache: false,
//         timeout: 600000,
//         success: function (data) {
//             window.location.reload()
//         },
//         error: function (e) {
//             window.location.reload() 
//         }
//     });

// })

function arrayIsSame(arr1, arr2) {
    if (arr1.length !== arr2.length) return false
    return arr1.every(function (element) {
        return arr2.indexOf(element) !== -1;
    });
}

function FormIsChanged() {
    let new_keterangan = $("[name='keterangan']").val()
    let new_routes_ids = getCurrentCheckVal()
    let compareArr = arrayIsSame(init_data.routes_ids, new_routes_ids)
    if (compareArr && new_keterangan === init_data.keterangan) return false;
    return true;
}

function getCurrentCheckVal() {
    return $("[id^='routes_']:checkbox:checked").map(function () {
        return this.id.replace("routes_", "")
    }).get()
}