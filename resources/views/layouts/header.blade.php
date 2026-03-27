<?php
$get_user = (new \App\Http\Traits\AuthFunction)->getUser();

if(!empty($get_user->data_user_sistem)) {
  $user = $get_user->data_user_sistem;
}

?>
<style>
:root { --primary-profile: #094889; }
    .user-profile-wrapper { cursor: pointer; transition: opacity 0.2s; }
    .user-profile-wrapper:hover { opacity: 0.8; }
    .user-icon-box {
        background-color: var(--primary-profile);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .profile-info { line-height: 1.2; }
    .profile-name { color: var(--primary-profile); font-weight: 700; margin-bottom: 0; }
    .title-page {
      text-align: center;
    }
    
    @media (max-width: 576px) {
        .profile-info { display: none !important; }
    }
    

  @media (max-width: 767px) {
      .title-page {
          display: none;
      }

      .header-container {
            position: fixed; 
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background-color: white !important;
            border-bottom: 1.5px solid #dee2e6; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding-left: 10px;
            padding-right: 10px;
            padding-top: 2px !important;
            padding-bottom: 2px !important;
        }

        body {
            padding-top: 60px;
        }
  }


</style>

<div class="pt-3 pb-3 header-container">
    <div class="bg-white py-2 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="{{ asset('') }}icon/hamburger.png" class="toggle-sidebar me-2" width="35" alt="">
            <img src="{{ asset('') }}icon/hamburger.png" class="hover-pointer d-none d-md-block" id="minimize" onclick="minimize()" width="35" alt="">
        </div>
            <h1 class="title-page mb-0">@yield('title-header')</h1>

        <div class="user-profile-wrapper d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#profileModal">
            <div class="profile-info d-flex flex-column align-items-end me-2 text-end">
                <p class="profile-name small">{{ $user->nm_karyawan ?? 'adaabsensi' }}</p>
                <small class="text-muted" style="font-size: 0.75rem;">{{ $user->nama_role ?? 'Super Admin' }}</small>
            </div>
            <div class="user-icon-box">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white py-2 d-flex justify-content-between align-items-center">
    @yield('breadcrumbs')
</div>

@include('profile.index')