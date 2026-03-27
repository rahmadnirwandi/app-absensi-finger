<form action="{{ url('edit-password/update') }}" method="POST">
    <input type="text" hidden name="id_user" value="{{ $model['id_user'] ?? '' }}">
    @csrf

    <div class="row justify-content-center mb-3">
        <div class="col-lg-4">
            <div class="form-group">
                <div class="bagan_form mb-3">
                    <label for="current_password" class="form-label">
                        Password Lama <span class="text-danger">*</span>
                    </label>
                    <input
                            type="password"
                            class="form-control"
                            id="current_password"
                            name="current_password"
                            required
                    >
                    <div class="message"></div>
                </div>

                <div class="bagan_form mb-3">
                    <label for="new_password" class="form-label">
                        Password Baru <span class="text-danger">*</span>
                    </label>
                    <input
                            type="password"
                            class="form-control"
                            id="new_password"
                            name="new_password"
                            required
                    >
                    <div class="message"></div>
                </div>

                <div class="bagan_form mb-4">
                    <label for="new_password_confirmation" class="form-label">
                        Konfirmasi Password <span class="text-danger">*</span>
                    </label>
                    <input
                            type="password"
                            class="form-control"
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                            required
                    >
                    <div class="message"></div>
                </div>
            </div>

            <div class="text-center mt-5">
                <button class="btn btn-primary validate_submit px-5 w-100" type="submit">
                    Ubah Password
                </button>
            </div>

        </div>
    </div>
</form>