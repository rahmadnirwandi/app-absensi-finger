@extends('layouts.master')

@section('title-header', '')

@push('custom-style')
<style>
    .submenu-wrapper {
        display: grid;
        grid-template-columns: repeat(4, 1fr); 
        gap: 20px;
    }

    .submenu-card {
        background: linear-gradient(135deg, #2d75ad, #3f8fd2);
        border-radius: 14px;
        padding: 24px 20px;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 15px;
        min-height: 100px;
    }

    .submenu-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        background: linear-gradient(135deg, #3f8fd2, #5aa7e3);
        color: #fff;
        text-decoration: none;
    }

    .submenu-icon {
        font-size: 32px;
        background: rgba(255,255,255,0.2);
        width: 55px;
        height: 55px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .submenu-title {
        font-size: 18px;
        font-weight: 600;
        line-height: 1.3;
    }

    /* Laptop / tablet landscape */
    @media (max-width: 1199px) {
        .submenu-wrapper {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Tablet */
    @media (max-width: 991px) {
        .submenu-wrapper {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Mobile */
    @media (max-width: 575px) {
        .submenu-wrapper {
            grid-template-columns: 1fr;
        }

        .submenu-card {
            padding: 18px 16px;
        }

        .submenu-title {
            font-size: 16px;
        }

        .submenu-icon {
            font-size: 26px;
            width: 48px;
            height: 48px;
        }
    }
</style>
@endpush


@section('content')
<div class="row">
    <div class="col-12">

        <form action="" method="GET" class="mb-4">
            <input
                type="text"
                class="form-control search-data-table"
                placeholder="Cari Menu Pengajuan..."
            >
        </form>

        <div class="submenu-wrapper">
            @if (!empty($menu))
                @foreach ($menu as $item)
                    @php $item = (object) $item; @endphp

                    <a href="{{ $item->url }}" class="submenu-card">
                        <div class="submenu-icon">
                             {!! $item->icon !!}
                        </div>
                        <div class="submenu-title">
                            {{ $item->title }}
                        </div>
                    </a>

                @endforeach
            @endif
        </div>

    </div>
</div>
@endsection


@push('script-end-1')
<script type="text/javascript" src="{{ asset('libs\datatables\1.10.11\js\jquery.dataTables.min.js' )}}"></script>
@endpush
