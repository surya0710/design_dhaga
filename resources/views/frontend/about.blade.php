@extends('frontend.layouts.app')
@section('title', $pageContent->meta_title ?? 'About Us')

@section('meta_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs,
and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', $pageContent->meta_keywords ?? 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade
clothing, made in India')

@section('og_title', $pageContent->meta_title ?? 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs,
and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset($pageContent->meta_image ?? 'og-home.jpg'))
@push('extras')
<style>
  .timeline-sidebar-container {
    position: relative;
    margin-bottom: 50px;
  }

  .timeline-sidebar {
    position: sticky;
    top: 15vh;
    display: flex;
    gap: 20px;
    height: 80vh;
    max-height: auto;
  }

  .timeline-line-track {
    width: 3px;
    background: #e0e0e0;
    border-radius: 4px;
    position: relative;
    margin: 10px 0;
    flex-shrink: 0; /* Prevent line from collapsing on mobile */
  }

  .timeline-progress {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 0%;
    background: black;
    border-radius: 4px;
    transition: height 0.1s linear;
  }

  .timeline-years {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 35px;
  }

  .timeline-years li {
    font-size: 1rem;
    color: #aaa;
    cursor: pointer;
    transition: color 0.3s ease, transform 0.3s ease;
  }

  .timeline-years li.active,
  .timeline-years li:hover {
    color: black;
    transform: scale(1.15);
    transform-origin: left center;
  }

  .timeline-item1 {
    opacity: 1;
    transition: all 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
    pointer-events: none;
  }

  .timeline-item1.active {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
  }

  .timeline-item1 img {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  }

  @media (max-width: 768px) {
    .timeline-sidebar {
      gap: 6px;
      top: 15vh;
      height: 75vh;
    }

    .timeline-line-track {
      width: 2px;
      min-width: 2px; /* Ensure the line never collapses to 0 */
      flex-shrink: 0;
    }

    .timeline-years {
      gap: 0;
      justify-content: space-between;
    }

    .timeline-years li {
      font-size: 0.7rem;
      padding: 1px 0;
      white-space: nowrap;
    }

    .timeline-years li.active,
    .timeline-years li:hover {
      transform: scale(1.1);
    }

    .timeline-item1 {
      margin-bottom: 3.5rem;
    }

    .timeline-item1 h2 {
      font-size: 1.5rem;
      margin-top: 1rem;
    }
  }

  @media (max-width: 400px) {
    .timeline-years li {
      font-size: 0.6rem;
    }
  }
</style>
@endpush
@section('content')
@php
  $aboutValueItems = $about ? $about->display_value_items : \App\Models\AboutSection::defaultValueItems();
@endphp

@if($about)
<section class="container py-4">
  <div class="owners-box">
    <div class="col-md-6 owner-image">
      <img src="{{ asset($about->image) }}" class="img-fluid" alt="{{ $about->heading }}" loading="lazy" />
    </div>
    <div class="col-md-6 content">
      <h1 class="text-center">{{ $about->heading }}</h1>
      {!! $about->description !!}
      <p class="text-center mt-xl-2 font-size-small">
        <strong>{{ $about->signature }}</strong>
      </p>
    </div>
  </div>
</section>
@endif

<section class="py-5 bg-body-primary" id="about-us-icons-with-content">
  <div class="container">
    <div class="box-container">
      @foreach($aboutValueItems as $item)
        <div class="box">
          @if(!empty($item['icon']))
            <img alt="{{ $item['alt'] ?? $item['title'] ?? 'About value' }}" src="{{ asset($item['icon']) }}" loading="lazy" />
          @endif
          <h4>{{ $item['title'] ?? '' }}</h4>
          <p class="px-3 text-justify">
            {!! nl2br(e($item['description'] ?? '')) !!}
          </p>
        </div>
      @endforeach
    </div>
  </div>
</section>

<section class="container pt-5 pb-3">
  <div class="text-center">
    <h3>Where It All Began</h3>
    <p class="text-muted">
      Our past, present, and future—woven together in harmony...
    </p>
  </div>
</section>

<section class="timeline-section container py-4" id="timeline">
    <div class="row position-relative">
        {{-- Sidebar Years --}}
        <div class="col-2 col-md-2 timeline-sidebar-container">
            <div class="timeline-sidebar">
                <div class="timeline-line-track">
                    <div class="timeline-progress" id="progressLine"></div>
                </div>
                <ul class="timeline-years" id="yearList">
                    @foreach($stories as $story)
                        <li data-target="year-{{ $story->year }}" class="{{ $loop->first ? 'active' : '' }}">
                            {{ $story->year }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        {{-- Story Content --}}
        <div class="col-10 col-md-10">
            @foreach($stories as $story)
                <div class="timeline-item1" id="year-{{ $story->year }}" data-year="{{ $story->year }}"> 
                    @if($loop->odd)
                        {{-- Image Left --}}
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <img src="{{ asset($story->image) }}" class="w-100 img-fluid" alt="{{ $story->year }} Journey" loading="lazy" />
                            </div>
                            <div class="col-md-6">
                                <div class="px-xl-4">
                                    <h2 class="fw-bold text-dark">
                                        {{ $story->year }}
                                    </h2>
                                    <p class="text-justify text-muted">
                                        {!! $story->description !!}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Image Right --}}
                        <div class="row align-items-center flex-column-reverse flex-md-row">
                            <div class="col-md-6 mt-3 mt-md-0">
                                <div class="px-xl-4">
                                    <h2 class="fw-bold text-dark">
                                        {{ $story->year }}
                                    </h2>
                                    <p class="text-justify text-muted">
                                        {!! $story->description !!}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <img src="{{ asset($story->image) }}" class="w-100 img-fluid" alt="{{ $story->year }} Journey" loading="lazy" />
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
<script>
    const textData = [];
    @foreach($highlights as $highlight)
        textData.push(`<span>{{ $highlight->title }}</span>
             <img src="{{ Storage::url($highlight->emoji) }}" class="emoji" loading="lazy" alt="{{ $highlight->alt_text ?? $highlight->title }}">`);
    @endforeach
</script>
@endsection
@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const progressLine = document.getElementById("progressLine");
    const timelineSection = document.querySelector(".timeline-section");
    const timelineItems = document.querySelectorAll(".timeline-item1");
    const yearItems = document.querySelectorAll("#yearList li");

    // Set Initial Active States
    if (timelineItems.length > 0) timelineItems[0].classList.add("active");
    if (yearItems.length > 0) yearItems[0].classList.add("active");

    /* SMOOTH PROGRESS BAR SCROLL CALCULATION */
    window.addEventListener("scroll", () => {
      if (!timelineSection) return;

      const rect = timelineSection.getBoundingClientRect();
      const windowHeight = window.innerHeight;

      // Start progressing when section hits vertical center, finish when it leaves
      const triggerPoint = windowHeight / 2;
      let scrolled = triggerPoint - rect.top;
      let total = rect.height - windowHeight / 3; // Fine tune so it reaches 100% on last item

      let percentage = (scrolled / total) * 100;

      // Clamp value
      if (percentage < 0) percentage = 0;
      if (percentage > 100) percentage = 100;

      progressLine.style.height = percentage + "%";
    });

    /* CLICK → SMOOTH SCROLL TO YEAR */
    yearItems.forEach((year) => {
      year.addEventListener("click", function () {
        const targetId = this.dataset.target;
        const targetEl = document.getElementById(targetId);
        if (!targetEl) return;

        // Offset to prevent the sticky header from blocking content
        const headerOffset =
          document.querySelector("nav")?.offsetHeight || 80;
        const top =
          targetEl.getBoundingClientRect().top +
          window.scrollY -
          headerOffset -
          40;

        window.scrollTo({
          top: top,
          behavior: "smooth",
        });
      });
    });

    /* INTERSECTION OBSERVER FOR HIGHLIGHTING */
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const year = entry.target.dataset.year;

            // Toggle Item Fading
            timelineItems.forEach((item) => {
              item.classList.toggle("active", item.dataset.year === year);
            });

            // Highlight Sidebar Year
            yearItems.forEach((li) => {
              li.classList.toggle("active", li.innerText.trim() === year);
            });
          }
        });
      },
      {
        threshold: 0.6, // Fire when 60% of the item is in view
        rootMargin: "-20% 0px -40% 0px", // Trigger slightly offset from exact center
      },
    );

    // Initialize Observer
    timelineItems.forEach((item) => observer.observe(item));
  });
</script>
@endpush
