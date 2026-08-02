@extends('frontend.layouts.app')
@section('title', $pageContent->meta_title  ?? 'Terms & Conditions')

@section('meta_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', $pageContent->meta_keywords ?? 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', $pageContent->meta_title ?? 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset($pageContent->meta_image ?? 'og-home.jpg'))

    @section('content')
        <section class="bg-body-secondary">
            <div class="container py-5 pt-3">
                {!! $pageContent->content !!}
                <div class="text-center">
                    <h3>Ready to collaborate?</h3>
                    <div class="row justify-content-center">
                        <div class="col-md-8 contact-form col-sm-12">
                            @if(session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session()->get('error') }}
                            </div>
                            @endif
                            <form class="form-group" action="{{ route('sendmail') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="terms" value="1" />
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
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            @if ($errors->has('instagram'))
                                                <span class="text-danger">{{ $errors->first('instagram') }}</span>
                                            @endif
                                            <input type="text" class="form-control" name="instagram" id="instagram" placeholder="Enter Instagram Link" value="{{ old('instagram') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    @if ($errors->has('message'))
                                        <span class="text-danger">{{ $errors->first('message') }}</span>
                                    @endif
                                    <textarea class="form-control" rows="4" placeholder="Message  *" name="message">{{ old('message') }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn" >Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script>
            const textData = [];
            @foreach($highlights as $highlight)
                textData.push(`<span>{{ $highlight->title }}</span>
                    <img src="{{ Storage::url($highlight->emoji) }}"
                        class="emoji"
                        alt="{{ $highlight->alt_text ?? $highlight->title }}">`);
            @endforeach
        </script>
    @endsection
    @push('scripts')
    @if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            iconHtml: '<i class="fa-regular fa-circle-check" style="color:#198754;"></i>',
            title: 'Success!',
            text: @json(session('success')),
            showConfirmButton: true,
            confirmButtonText: 'Visit Portfolio',
            showCancelButton: true,
            cancelButtonText: 'Close',
            confirmButtonColor: '#000',
            customClass: {
                icon: 'swal-fa-icon',
            },
            didOpen: (popup) => {
                const iconEl = popup.querySelector('.swal2-icon.swal-fa-icon');
                if (iconEl) {
                    iconEl.style.border = 'none';
                    iconEl.style.color = '#198754';
                }
            },
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('portfolio') }}";
            }
        });
    </script>
    @endif
    @endpush