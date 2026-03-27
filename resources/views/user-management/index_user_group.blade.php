@extends('layouts.master')

@section('title-header', 'User Group Aplikasi')

<?php
?>

@push('link')
    <script type="text/javascript" src="{{ asset('libs\jquery\latest\jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs\jquery\latest\moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs\daterangepicker\3.1.0\js\daterangepicker.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs\daterangepicker\3.1.0\css\daterangepicker.css') }}" />
@endpush

@push('custom-style')
    <style>
        .table-number {
            counter-reset: numbering;
        }

        .table-number tbody td:first-child:before {
            counter-increment: numbering;
            content: counter(numbering) ".";
        }
    </style>
@endpush

@section('content')
    <div>
        <div class="row d-flex justify-content-between">
            <div class="w-50">
                <label for="pencarianralan" class="form-label">Pencarian Keyword</label>
                <div class="d-flex align-items-center">
                    <input type="text" class="form-control me-2" id="pencarianralan" value="" placeholder="Masukkan "
                        name="search" />
                    <button type="submit" id="submit" class="btn btn-primary">
                        <img src="{{ asset('') }}icon/search.png" alt="">
                    </button>
                </div>
            </div>

            <div class="w-25 d-flex align-items-end justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserGroup">
                    Tambah
                </button>
            </div>
        </div>

        <table class="table table-striped table-number">
            <thead>
                <tr>
                    <th class="text-center ">No.</th>
                    <th class="">Nama</th>
                    <th class="">Keterangan</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user_group_list as $value)
                    <tr>
                        <td class="text-center"></td>
                        <td class="">{{ $value->name }}</td>
                        <td class="">{{ $value->keterangan }}</td>
                        <td class="text-center">
                            <a class="btn btn-primary"
                                href="{{ url('user-group-app/get-user-group?id=' . $value->id . '&alias=' . $value->alias) }}"
                                role="button">Atur</a>
                            <button type="submit" class="btn btn-warning" id="edit" data-bs-toggle="modal"
                                data-bs-target="#editUserGroup" data-whatever="@mdo" data-id="{{ $value->id }}"
                                data-name="{{ $value->name }}" data-keterangan="{{ $value->keterangan }}"
                                data-alias="{{ $value->alias }}">
                                Edit
                            </button>
                            <button type="button" class="btn btn-danger"
                                onclick="openDeleteModal('{{ $value->id }}', '{{ $value->name }}')">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="addUserGroup" tabindex="-1" aria-labelledby="addUserGroupLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ url('/user-group-app/add-user-group') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserGroupLabel">Tambah Level Akses</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="input_akses" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="input_akses" name="name">
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="input_akses" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" aria-label="With textarea"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade" id="deleteUserGroup" tabindex="-1" aria-labelledby="deleteUserGroupLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ url('/user-group-app/delete-user-group') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUserGroupLabel">Hapus Level Akses</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p></p>
                        <input type="text" hidden class="form-control" id="input_akses" name="id">
                        <input type="text" hidden class="form-control" id="input_akses" name="name">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserGroup" tabindex="-1" aria-labelledby="addUserGroupLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                {{-- <form id="form-input"> --}}
                @csrf
                <input type="hidden" id="ida" name="ida">
                <input type="hidden" id="aliasa" name="aliasa">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserGroupLabel">Edit Level Akses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <label for="input_akses" class="form-label">Nama</label>
                        <input type="text" id="nama" name="nama" class="form-control">

                        {{-- <input type="text"  id="name" name="name" class="form-control"> --}}

                    </div>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="input_akses" class="form-label">Keterangan</label>
                        {{-- <input type="text" id="keterangan" name="keterangan" class="form-control"> --}}
                        <textarea class="form-control" id="keterangana" name="keterangana" aria-label="With textarea"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="processedit" class="btn btn-primary">Simpan</button>
                </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>

@endsection

@push('custom-script')
    <script src="{{ asset('js/globalScript.js') }}"></script>
    <script>
        $(document).ready(function() {
            var id = null;
            var nama = null;
            var keterangan = null;
            var alias = null;

            $(document).on('click', '#edit', function() {
                this.id = $(this).data('id');
                this.name = $(this).data('name');
                this.keterangan = $(this).data('keterangan');

                this.alias = $(this).data('alias');

                $('#aliasa').val(this.alias);
                $('#ida').val(this.id);
                $('#nama').val(this.name);
                $('#keterangana').val(this.keterangan);
            })



            $('#processedit').on('click', function() {
                var id = $("input[name='ida']").val();
                var alias = $("input[name='aliasa']").val();

                var nama = $("input[name='nama']").val();
                var keterangan = $("textarea[name='keterangana']").val();

                let updatedData = {
                    "alias_group": alias
                };

                updatedData.authGroup = {
                    "id": id,
                    "name": nama,
                    "keterangan": keterangan,

                };

                updatedData.authPermission = {
                    empty: true
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: `${base_url}/user-group-app/update-user-group`,
                    data: updatedData,
                    dataType: "json",
                    cache: false,
                    timeout: 600000,
                    success: function(data) {
                        console.log(data)
                        window.location.reload()
                    },
                    error: function(e) {
                        console.log(e)
                        window.location.reload()
                        // return
                    }
                });
                // updatedData.authGroup = 'dsad'
            })
        })
        const confirmDeletePermission = new bootstrap.Modal($("#deleteUserGroup"), {
            keyboard: true
        })

        function openDeleteModal(id, name) {
            console.log(id, name)
            $("#deleteUserGroup .modal-body p").html(`Anda Yakin Ingin Menghapus Hak Akses "<b>${name}</b>"`)
            $("#deleteUserGroup .modal-body [name='id']").val(id)
            $("#deleteUserGroup .modal-body [name='name']").val(name)
            confirmDeletePermission.show()
        }
    </script>
@endpush