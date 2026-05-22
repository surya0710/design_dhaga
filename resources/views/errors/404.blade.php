@extends('frontend.layouts.app')

@section('title', '404 - Page Not Found')

@section('meta_description', 'The page you are looking for could not be found.')

@section('content')

<section class="container-fluid error-page d-flex align-items-center justify-content-center">
    <div class="row w-100 justify-content-center text-center">
        <div class="col-lg-7 col-md-10">

            <div class="error-wrapper">

                <h1 class="error-code">404</h1>

                <h2 class="error-title">
                    Oops! This Thread Went Missing.
                </h2>

                <p class="error-description">
                    The page you’re looking for doesn’t exist, was moved,
                    or stitched somewhere else.
                </p>

                <div class="error-buttons">
                    <a href="{{ route('home') }}" class="btn primary-btn">
                        Back To Home
                    </a>

                    <a href="{{ route('contact-us') }}" class="btn secondary-btn">
                        Contact Us
                    </a>
                </div>

                <div class="error-highlight mt-5">
                    <span>Handcrafted Fashion</span>
                    <span class="dot"></span>
                    <span>Premium Branding</span>
                    <span class="dot"></span>
                    <span>Creative Graphics</span>
                </div>

            </div>

        </div>
    </div>
</section>

@endsection


@push('styles')
<style>

.error-page{
    min-height:100vh;
    background:#f8f5ef;
    padding:60px 20px;
    position:relative;
    overflow:hidden;
}

.error-wrapper{
    position:relative;
    z-index:2;
}

.error-code{
    font-size:160px;
    font-weight:800;
    line-height:1;
    color:#111;
    margin-bottom:10px;
    letter-spacing:5px;
}

.error-title{
    font-size:42px;
    font-weight:700;
    margin-bottom:20px;
    color:#111;
}

.error-description{
    font-size:18px;
    color:#666;
    max-width:650px;
    margin:0 auto 40px;
    line-height:1.8;
}

.error-buttons{
    display:flex;
    gap:20px;
    justify-content:center;
    flex-wrap:wrap;
}

.error-buttons .btn{
    padding:14px 32px;
    border-radius:0;
    font-size:15px;
    font-weight:600;
    transition:all 0.3s ease;
    text-transform:uppercase;
    letter-spacing:1px;
}

.primary-btn{
    background:#111;
    color:#fff;
    border:1px solid #111;
}

.primary-btn:hover{
    background:transparent;
    color:#111;
}

.secondary-btn{
    background:transparent;
    color:#111;
    border:1px solid #111;
}

.secondary-btn:hover{
    background:#111;
    color:#fff;
}

.error-highlight{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
    font-size:14px;
    text-transform:uppercase;
    letter-spacing:2px;
    color:#777;
}

.error-highlight .dot{
    width:6px;
    height:6px;
    background:#111;
    border-radius:50%;
}

.error-page::before{
    content:'';
    position:absolute;
    width:500px;
    height:500px;
    background:rgba(0,0,0,0.03);
    border-radius:50%;
    top:-200px;
    right:-150px;
}

.error-page::after{
    content:'';
    position:absolute;
    width:350px;
    height:350px;
    background:rgba(0,0,0,0.03);
    border-radius:50%;
    bottom:-120px;
    left:-100px;
}

@media(max-width:768px){

    .error-code{
        font-size:100px;
    }

    .error-title{
        font-size:28px;
    }

    .error-description{
        font-size:16px;
    }

    .error-buttons{
        flex-direction:column;
        align-items:center;
    }

    .error-buttons .btn{
        width:100%;
        max-width:280px;
    }
}

</style>
@endpush