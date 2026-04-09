@extends('frontend.layouts.app')
@section('title', 'Contact Us')

@section('meta_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset('frontend_assets/images/og-home.jpg'))

    @section('content')
        <section class="bg-body-secondary">
            <div class="container py-5 pt-3">
                <h3>Introduction</h3>
                <p>At Design Dhaga, we believe great things happen when creative people come together. Whether you are a content creator, a wedding planner, a boutique owner, or someone who simply loves handcrafted art - we would love to work with you.</p>
                <p>Every piece we make is hand-painted, original, and one of a kind. And we are looking for people who value that same authenticity in their work.</p>
                <hr>
                <h4>For the ones who create with heart.</h4>
                <p>If you love Indian art, handcrafted fashion, and content that actually means something - we are your people. We work with creators who genuinely connect with what we do, not just promote it.</p>
                <p>What we offer:</p>
                <ul>
                    <li>Custom hand-painted outfits designed just for you</li>
                    <li>Long term brand partnerships</li>
                    <li>Creative freedom - your style, your story</li>
                    <li>Affiliate commissions on every sale you drive</li>
                </ul>
                <p>What we are looking for:</p>
                <ul>
                    <li>Someone who loves handmade and homegrown Indian brands</li>
                    <li>Authentic content - reels, posts, vlogs, anything real</li>
                    <li>Any follower count - we value quality over numbers</li>
                </ul>
                <hr>
                <h4>For the ones who create memories.</h4>
                <p>Weddings are personal. And so is everything we make. If you plan weddings, sangeets, mehendi functions, or any celebration - Design Dhaga can be your go-to for custom hand-painted outfits, gifting, and bridal wear.</p>
                <p>What we offer:</p>
                <ul>
                    <li>Bulk custom orders for wedding families</li>
                    <li>Exclusive twinning and coordinated outfit sets</li>
                    <li>Hand-painted gifting options for guests</li>
                    <li>Priority delivery and dedicated support</li>
                </ul>
                <hr>
                <h4>For the ones who curate with taste.</h4>
                <p>If you run a boutique or a retail store and want to stock something truly different - our hand-painted pieces are unlike anything else on the market. No two pieces are identical. Your customers will not find the same outfit anywhere else.</p>
                <p>What we offer:</p>
                <ul>
                    <li>Wholesale and consignment options</li>
                    <li>Exclusive regional partnerships</li>
                    <li>Custom designs under your store's curation</li>
                    <li>Full brand support and product education</li>
                </ul>
                <hr>
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
                                <div class="col-md-12">
                                    <button type="submit" class="btn" >Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endsection