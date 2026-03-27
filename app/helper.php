<?php

    if(!function_exists('view_data_tb')){
        function view_data_tb($sign, $data){
            if($sign==='klasifikasi_lokasi_anatomi'){
                switch ($data) {
                    case "1":
                        return "Paru";
                    case '2':
                        return "Ekstraparu";
                    default:
                        return "";
                }
            }
            if($sign==='klasifikasi_riwayat_pengobatan'){
                switch ($data) {
                    case "1":
                        return "Baru";
                    case '2':
                        return "Kambuh";
                    case '3':
                        return "Diobati setelah gagal kategori 1";
                    case '4':
                        return "Diobati setelah gagal kategori 2";
                    case '5':
                        return "Diobati setelah putus berobat";
                    case '6':
                        return "Diobati setelah gagal pengobatan lini 2";
                    case '7':
                        return "Pernah diobati tidak diketahui hasilnya";
                    case '8':
                        return "Tidak diketahui";
                    case '9':
                        return "Lain-lain";
                    default:
                        return "";
                }
            }
            if($sign==='tipe_diagnosis'){
                switch ($data) {
                    case "1":
                        return "Terkonfirmasi Bakteriologis";
                    case '2':
                        return "Terdiagnosis Klinis";
                    default:
                        return "";
                }
            }

            if($sign==='hasil_akhir_pengobatan'){
                switch ($data) {
                    case "1":
                        return "Sembuh";
                    case '2':
                        return "Pengobatan lengkap";
                    case "3":
                        return "Putus berobat";
                    case '4':
                        return "Meninggal";
                    case "5":
                        return "Gagal";
                    case '6':
                        return "Tidak dievaluasi/pindah";
                    default:
                        return "";
                }
            }
        }
    }

?>
