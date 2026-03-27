$(document).ready(function() {
    $('input[name="jenis_dinas"]').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue === '1') {
            $('#tanggalDalamKota').show();
            $('#tanggalLuarKota').hide();
        } else if (selectedValue === '2') {
            $('#tanggalDalamKota').hide();
            $('#tanggalLuarKota').show();
        }
    });
});