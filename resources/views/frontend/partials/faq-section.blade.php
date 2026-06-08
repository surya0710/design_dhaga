{{--
    Partial: frontend/partials/faq-section.blade.php

    Variables:
        $faqs           – Collection of FAQ objects (question, answer)
        $containerClass – CSS class for the outer wrapper, e.g. '' or 'container'
--}}
<section class="faq-section py-4">
    <div class="{{ $containerClass ?? '' }}">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-8 col-12">

                <div class="text-center mb-2">
                    <h2 class="mb-0">Frequently Asked Questions</h2>
                </div>

                <div class="faq-wrapper">
                    @foreach($faqs as $key => $faq)
                    <div class="faq-item {{ $key >= 1 ? 'extra-faq d-none' : '' }}">
                        <h4 class="faq-question mb-1 mt-1 text-blue">Que: {{ $faq->question }}</h4>
                        <div class="faq-answer-wrapper">
                            <p class="faq-answer mb-0 text-justify" id="faqAnswer{{ $key }}">
                                <strong>Ans:</strong> {!! $faq->answer !!}
                            </p>
                            <p type="button" class="read-more-btn fw-bold d-none"
                               data-target="faqAnswer{{ $key }}"
                               data-expanded="0">
                                <span class="read-more-span">•••</span>
                            </p>
                        </div>
                    </div>
                    @endforeach

                    <div class="text-center extra-faq d-none">
                        <h4 class="mb-2">Still have questions? We'd love to hear from you.</h4>
                        <a class="btn btn-outline-secondary view-all-btn bg-dark"
                           href="{{ route('contact-us') }}">Contact Us</a>
                    </div>

                    @if(count($faqs) > 1)
                    <div class="text-center mt-4">
                        <button type="button"
                                class="btn btn-outline-secondary see-more text-dark text-decoration-none fw-bold"
                                id="showMoreFaqBtn">
                            See more...
                        </button>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</section>