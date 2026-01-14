<header class="header-desktop3 d-none d-lg-block">
    <div class="section__content section__content--p35">
        <div class="header3-wrap">
            <div class="header__logo">
                <a href="#">
                    <img width="90px" src="{{ asset('assets/images/icon/DAGAGOO_logo.webp') }}" alt="Dagagoo Logo" />
                </a>
            </div>
            <div class="header__navbar d-flex align-items-center">
 <ul class="list-unstyled d-flex align-items-center m-0">
    <!-- Dine In -->
    <li  style="left: 12%;">
        <a href="#" onclick="showPOS('dineIn')" class="text-white fw-bold">
            <i class="fas fa-chair fa-lg"></i> Dine In
        </a>
    </li>
    <!-- Take Away -->
    <li  style="left: 12%;">
        <a href="#" onclick="showPOS('takeAway')" class="text-white fw-bold">
            <i class="fas fa-shopping-bag fa-lg"></i> Take Away
        </a>
    </li>
    <!-- Delivery -->
    <li style="left: 12%;">
        <a href="#" onclick="showPOS('delivery')" class="text-white fw-bold">
            <i class="fas fa-motorcycle fa-lg"></i> Delivery
        </a>
    </li>
    <!-- Spacer -->
    <li class="text-white fw-bold" style="position: relative;
    left: 168px;
    text-align: center;">
        <!-- التاريخ -->
        <div id="posDate" style="font-size:0.85rem;"></div>
        <div id="posClock" style="font-size:1rem; font-weight:bold;"></div>
    </li>
     <li class="ms-auto text-white fw-bold position-relative"
         style="direction: rtl; left: 19%; text-align: start; width: 200px;">

    <div class="account-item clearfix js-item-menu">

        <!-- اسم المستخدم (يفتح الدروب داون) -->
        <div class="content">
            <a class="js-acc-btn text-white fw-bold" href="#">
                {{ session('user')->name ?? '' }}
                
            </a>
        </div>

        <!-- الدروب داون -->
        <div class="account-dropdown js-dropdown" style="min-width: 200px;top: 74px;">

            <div class="info clearfix text-center p-2">
                <h5 class="name mb-1">
                    {{ session('user')->name ?? 'ضيف' }}
                </h5>
                <h5 class="email fw-bold">
                   شيفت: {{ session('shift_type')->name ?? '' }}
                </h5>
            </div>

            <div class="account-dropdown__footer p-0">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-danger w-100 fw-bold rounded-0">
                        <i class="zmdi zmdi-power"></i> تسجيل الخروج
                    </button>
                </form>
            </div>

        </div>
    </div>

</li>


</ul>
         </div>
        </div>
    </div>
</header>