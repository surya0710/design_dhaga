@extends('frontend.layouts.app')
@section('title', 'Our Store')

@section('meta_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset('frontend_assets/images/og-home.jpg'))

    @section('content')
        <section class="container-fluid bg-body-secondary py-5 pb-2">
            <div class="row">
                <h1 class="text-center">Our Store is Opening soon<br> We are accepting customize orders now</h1>
            </div>
        </section>
        <section class="bg-body-secondary py-2 pb-5">
            <div class="container">
                <div class="row pt-5 justify-content-center">
                    <div class="col-md-8 contact-form col-sm-12">
                        @if(session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session()->get('error') }}
                        </div>
                        @endif
                        <form class="form-group" action="{{ route('sendmail') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    @if ($errors->has('name'))
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    @endif
                                    <input type="text" class="form-control" placeholder="Name *" name="name" value="{{ old('name') }}">
                                </div>
                                <div class="col-md-6">
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                    <input type="email" class="form-control" placeholder="E-mail  *" name="email" value="{{ old('email') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    @if ($errors->has('phone'))
                                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                                    @endif
                                    <input type="tel" class="form-control" placeholder="Phone No  *" name="phone" value="{{ old('phone') }}">
                                </div>
                                <div class="col-md-6">
                                    <select class="form-control" name="category">
                                        <option value="">Select Category</option>
                                        <option value="fabric" {{ old('category') == 'fabric' ? 'selected' : '' }}>Fabric Printing</option>
                                        <option value="design" {{ old('category') == 'design' ? 'selected' : '' }}>Graphics</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                @if ($errors->has('design'))
                                    <span class="text-danger">{{ $errors->first('design') }}</span>
                                @endif
                                <input type="file" class="form-control" name="design" placeholder="Upload Your Design Ideas">
                            </div>
                            <div class="col-md-12">
                                @if ($errors->has('message'))
                                    <span class="text-danger">{{ $errors->first('message') }}</span>
                                @endif
                                <textarea class="form-control" rows="4" placeholder="Message  *" name="message">{{ old('message') }}</textarea>
                            </div>
                            <div class="flex">
                                <input type="checkbox" name="terms" id="terms" checked="">
                                <label for="terms">
                                    By submitting this form, you hereby grant us permission to contact you via SMS, WhatsApp, RCS, Email, and any other channel.
                                </label>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn" >Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    @endsection
    @push('scripts')
    @if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            showConfirmButton: true,
            confirmButtonText: 'Visit Portfolio',
            showCancelButton: true,
            cancelButtonText: 'Close',
            confirmButtonColor: '#000',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('portfolio') }}";
            }
        });
    </script>
    @endif
    @endpush
