@extends('frontend.layouts.app')
@section('title', 'About Us')

@section('meta_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs,
and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade
clothing, made in India')

@section('og_title', 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs,
and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset('frontend_assets/images/og-home.jpg'))
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
<section class="container py-4">
  <div class="owners-box">
    <div class="col-md-6 owner-image">
      <img src="frontend_assets/images/trio-size.png" class="img-fluid" alt="Design Dhaga Owners" />
    </div>
    <div class="col-md-6 content">
      <h1 class="text-center">Meet the Trio Behind Design Dhaga</h1>
      <p class="text-justify px-xl-5 px-3">
        <strong>Design Dhaga</strong> is built by three partners blending
        art and design. <strong>Preeti</strong> Dhindhoria brings 8+ years
        of hand-painted fabric expertise through
        <strong>Art Factory PD</strong>. <strong>Lalit</strong>, a
        certified designer, and <strong>Gunjan</strong>, an MBA with
        creative vision, lead <strong>Lalit Creatives</strong>, crafting
        strong brand identities. Together, their traditional artistry and
        modern design unite to form <strong>Design Dhaga</strong>.
      </p>
      <p class="text-center mt-xl-2 font-size-small">
        -<strong>Preeti</strong>, <strong>Lalit</strong> &
        <strong>Gunjan</strong>
      </p>
    </div>
  </div>
</section>

<section class="py-5 bg-body-primary" id="about-us-icons-with-content">
  <div class="container">
    <div class="box-container">
      <div class="box">
        <img alt="Timeless" src="frontend_assets/images/icons/TimeLess icon.svg" />
        <h4>सदाबहार | TIMELESS</h4>
        <p class="px-3 text-justify">
          Design Dhaga creates designs that never fade with time — whether
          it's a hand-painted saree, dupatta, kurta, or a digital logo
          crafted for a brand. Our work is rooted in minimal, meaningful
          aesthetics and exceptional quality, ensuring every piece —
          fabric or graphic — stays relevant, elegant, and cherished
          forever.
        </p>
      </div>
      <div class="box">
        <img alt="Honest" src="frontend_assets/images/icons/Honest icon.svg" />
        <h4>सच | HONEST</h4>
        <p class="px-3 text-justify">
          Honesty is woven into everything we create. From the
          authenticity of hand-painted fabrics to transparent pricing and
          clear communication in our graphic design services, we promise
          no hidden surprises — only genuine creativity and fair value you
          can trust.
        </p>
      </div>
      <div class="box">
        <img alt="Easy" src="frontend_assets/images/icons/Easy Icon.svg" />
        <h4>सरल | EASY</h4>
        <p class="px-3 text-justify">
          At Design Dhaga, simplicity is our strength. We make the process
          easy — whether you're customizing a hand-painted outfit or
          building your visual identity through graphic design. Smooth
          workflow, clear guidance, and effortless experience — just like
          the comfort of a warm cup of chai.
        </p>
      </div>
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

<section class="timeline-section container py-4">
  <div class="row position-relative">
    <div class="col-2 col-md-2 timeline-sidebar-container">
      <div class="timeline-sidebar">
        <div class="timeline-line-track">
          <div class="timeline-progress" id="progressLine"></div>
        </div>

        <ul class="timeline-years" id="yearList">
          <li data-target="year-2019" class="active">2019</li>
          <li data-target="year-2020">2020</li>
          <li data-target="year-2021">2021</li>
          <li data-target="year-2022">2022</li>
          <li data-target="year-2023">2023</li>
          <li data-target="year-2024">2024</li>
          <li data-target="year-2025">2025</li>
          <li data-target="year-2026">2026</li>
        </ul>
      </div>
    </div>

    <div class="col-10 col-md-10">
      <div class="timeline-item1" id="year-2019" data-year="2019">
        <div class="row align-items-center">
          <div class="col-md-6 mb-3 mb-md-0">
            <img src="frontend_assets/images/journey/2019.jpg" class="w-100 img-fluid" alt="2019 Journey" />
          </div>
          <div class="col-md-6">
            <div class="px-xl-4">
              <h2 class="fw-bold text-dark">2019</h2>
              <p class="text-justify text-muted">
                ArtFactory PD began with Preeti Dhindhoria hand-painting
                fabrics at home, while Lalit and Gunjan launched Lalit
                Creatives, taking early Designing projects.
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="timeline-item1" id="year-2020" data-year="2020">
        <div class="row align-items-center flex-column-reverse flex-md-row">
          <div class="col-md-6 mt-3 mt-md-0">
            <div class="px-xl-4">
              <h2 class="fw-bold text-dark">2020</h2>
              <p class="text-justify text-muted">
                Preeti started the ArtFactory PD YouTube channel and
                received growing orders. Lalit Creatives expanded from
                basic designs to full branding Skills.
              </p>
            </div>
          </div>
          <div class="col-md-6">
            <img src="frontend_assets/images/journey/2020.jpg" class="w-100 img-fluid" alt="2020 Journey" />
          </div>
        </div>
      </div>

      <div class="timeline-item1" id="year-2021" data-year="2021">
        <div class="row align-items-center">
          <div class="col-md-6 mb-3 mb-md-0">
            <img src="frontend_assets/images/journey/2021.jpg" class="w-100 img-fluid" alt="2021 Journey" />
          </div>
          <div class="col-md-6">
            <div class="px-xl-4">
              <h2 class="fw-bold text-dark">2021</h2>
              <p class="text-justify text-muted">
                ArtFactory PD explored new painting styles and custom
                clothing. Lalit Creatives strengthened workflows and
                worked with brands like Pind Balluchi and Aieraa Overseas.
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="timeline-item1" id="year-2022" data-year="2022">
        <div class="row align-items-center flex-column-reverse flex-md-row">
          <div class="col-md-6 mt-3 mt-md-0">
            <div class="px-xl-4">
              <h2 class="fw-bold text-dark">2022</h2>
              <p class="text-justify text-muted">
                ArtFactory PD gained recognition for detailed handcrafted
                work. Lalit Creatives handled more branding and packaging
                projects, building strong client relationships.
              </p>
            </div>
          </div>
          <div class="col-md-6">
            <img src="frontend_assets/images/journey/2022.jpg" class="w-100 img-fluid" alt="2022 Journey" />
          </div>
        </div>
      </div>

      <div class="timeline-item1" id="year-2023" data-year="2023">
        <div class="row align-items-center">
          <div class="col-md-6 mb-3 mb-md-0">
            <img src="frontend_assets/images/journey/2023.jpg" class="w-100 img-fluid" alt="2023 Journey" />
          </div>
          <div class="col-md-6">
            <div class="px-xl-4">
              <h2 class="fw-bold text-dark">2023</h2>
              <p class="text-justify text-muted">
                ArtFactory PD introduced new collections and higher
                customer engagement. Lalit Creatives focused on identity
                design, digital ads, and social media branding.
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="timeline-item1" id="year-2024" data-year="2024">
        <div class="row align-items-center flex-column-reverse flex-md-row">
          <div class="col-md-6 mt-3 mt-md-0">
            <div class="px-xl-4">
              <h2 class="fw-bold text-dark">2024</h2>
              <p class="text-justify text-muted">
                Both brands became well-established independently —
                ArtFactory PD for handcrafted artistry and Lalit Creatives
                for modern, reliable design solutions.
              </p>
            </div>
          </div>
          <div class="col-md-6">
            <img src="frontend_assets/images/journey/2024.jpg" class="w-100 img-fluid" alt="2024 Journey" />
          </div>
        </div>
      </div>

      <div class="timeline-item1" id="year-2025" data-year="2025">
        <div class="row align-items-center">
          <div class="col-md-6 mb-3 mb-md-0">
            <img src="frontend_assets/images/journey/2025.jpg" class="w-100 img-fluid" alt="2025 Journey" />
          </div>
          <div class="col-md-6">
            <div class="px-xl-4">
              <h2 class="fw-bold text-dark">2025</h2>
              <p class="text-justify text-muted">
                Customer trust and creative clarity strengthened on both
                sides, with a clear vision for larger & more refined work.
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="timeline-item1" id="year-2026" data-year="2026">
        <div class="row align-items-center flex-column-reverse flex-md-row">
          <div class="col-md-6 mt-3 mt-md-0">
            <div class="px-xl-4">
              <h2 class="fw-bold text-dark">2026</h2>
              <p class="text-justify text-muted">
                Timeless designs. Honest work. Easy experiences.<br /><br />
                A future woven together with creativity in every thread
                and every pixel.
              </p>
            </div>
          </div>
          <div class="col-md-6">
            <img src="frontend_assets/images/journey/2026.jpg" class="w-100 img-fluid" alt="2026 Journey" />
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
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