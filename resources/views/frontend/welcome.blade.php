@extends('easyadmin::frontend.parent')
@push('mtitle')
    {{ $title }}
@endpush
@push('festyles')
<style>
  .item-module:hover {
     transform: scale(1.03);
     transition: all 0.3s ease;
  }
  .item-module{
    -webkit-filter: grayscale(0.8);
    filter: grayscale(0.8);
  }
  .item-module.active{
    -webkit-filter: grayscale(0);
    filter: grayscale(0);
  }
  .text-green-idev{
    color : rgb(80 147 138);
  }
  .bg-whiten {
        background: linear-gradient(#ffffff, #ffffff), url(images/bgtrans.png);
        background-blend-mode: overlay;
    }
</style>
@endpush

@section('contentfrontend')
    {{-- Section: Logo & Brand --}}
    <section class="page-section py-4 bg-whiten">
        <div class="container text-center">
            <img src="{{ asset('images/logo-brand.png') }}" alt="Brand Logo" class="mb-3" style="max-height: 100px;">
            @if(Auth::user())
                <h1 class="fw-bold text-green-idev">Hi, {{Auth::user()->name}}</h1>
                <p>you are logged in as {{Auth::user()->role->name}}</p>
                <button type="button" class="rounded-4 btn btn-outline-danger" data-bs-toggle='modal' data-bs-target='#modalLogout'>Logout</button>
            @else
                <h1 class="fw-bold text-green-idev">{{config('idev.app_name')}}</h1>
                <button type="button" class="rounded-4 btn btn-outline-dark" data-bs-toggle='modal' data-bs-target='#modalLogin'>Login</button>
            @endif
        </div>
    </section>

    {{-- Section: Modules Grid --}}
    <section class="page-section py-5 bg-whiten">
        <div class="container">
            <div class="row justify-content-center">
                @foreach ($modules as $key => $module)
                    <div class="col-lg-2 col-md-3 col-6 d-flex align-items-stretch">
                        <div class="item-module @if ($module['active'] && Auth::user()) active @endif card shadow border w-100 mx-2 my-3 text-center">
                            <a 
                                @if ($module['active']) 
                                    href="{{ $module['link'] }}" 
                                @else 
                                    href="#" class="disabled text-muted"
                                @endif 
                                class="text-decoration-none p-3 d-block"
                            >
                                <img src="{{ $module['icon'] }}" alt="{{ $module['title'] }}" class="mb-3" style="max-height: 80px; object-fit: contain;">
                                <hr>
                                <h6 class="fw-semibold">{{ $module['title'] }}</h6>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <footer>
        <div class="text-center p-2 text-gray-500">
            <small>© Powered O.R HR.SP</small>
        </div>
    </footer>

    <div class="modal fade" tabindex="-1" role="dialog" id="modalLogin">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Login ?</h2>
                </div>
                <div class="modal-body">
                    <form id="form-login" action="{{url('login')}}" method="post">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="create_email" name="email" placeholder="Email address / Username" />
                            <label for="floatingInput">Email address / Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="create_password" name="password" placeholder="Password" />
                            <label for="floatingInput">Password</label>
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="button" class="btn btn-primary-idev" id="btn-for-form-login" onclick="submitAndReload('form-login')">Sign In</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal modal fade" tabindex="-1" role="dialog" id="modalLogout">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Logout ?</h2>
                </div>
                <div class="modal-body">
                    <p>Are you sure want to logged out from your account?</p>
                    <hr>
                    <a href="{{route('logout')}}"
                        class="btn btn-outline-primary"                             
                    >Yes</a>
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    @push('fescripts')
        <script>
            var input = document.getElementById("create_password");

            input.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    submitAndReload('form-login')
                }
            });
        </script>
    @endpush
@endsection
