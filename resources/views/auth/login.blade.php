@extends('layouts.master')
@section('content')
<div class="login-box " style="
    background:#e5e5e5;
    padding:40px;
    width:500px;
    margin:40px auto;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.65);
    text-align:center;">

    <div><img width="200px" src="{{ asset('assets/images/icon/DAGAGOO_logo.webp') }}" alt="Dagagoo Logo" /> </div>
    

    {{-- هنا مكان عرض رسالة الخطأ --}}
    @if (session('error'))
    <div style="color:red; margin-bottom:15px; text-align:center;">
        {{ session('error') }}
    </div>
    @endif
    {{-- لو فيه أخطاء Validation --}}
    @if ($errors->any())
    <div style="color:red; margin-bottom:15px; text-align:center;">
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
    @endif
    <form  autocomplete="off" method="POST" action="{{ route('login.submit') }}">
        @csrf
        <input type="text" name="user_code" placeholder="User ID"
            style="width:100%;padding:12px;margin:10px 0;
                border:1px solid #ccc;border-radius:8px;font-size:16px;" autocomplete="off">
        <input type="password" name="password" placeholder="Password"
            style="width:100%;padding:12px;margin:10px 0;
                border:1px solid #ccc;border-radius:8px;font-size:16px;" autocomplete="new-password">
        <select name="shift_type_id"
    style="width:100%;padding:12px;margin:10px 0;
           border:1px solid #ccc;border-radius:8px;font-size:16px;">
    <option value="">اختر الشيفت</option>

    @foreach ($shiftTypes as $type)
        <option value="{{ $type->id }}">{{ $type->name }}</option>
    @endforeach
</select>

        
        <button type="submit"
            style="width:100%;padding:14px;margin-top:15px;
                background:#1e73ff;color:#fff;border:none;
                border-radius:8px;font-size:18px;cursor:pointer;">
            Log In
        </button>
    </form>
</div>
@endsection