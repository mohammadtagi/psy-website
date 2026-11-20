<div class="inner-page-hero min-h-screen text-white">
            <div class="layout">

                @include('website.partials.header')

                <section
                    class="inner-hero-frame flex min-h-[555px] items-center justify-center text-center"
                    aria-labelledby="page-title"
                >
                    <div class="max-w-[580px]">
                        <nav aria-label="مسیر صفحه" class="mb-6 text-sm text-white/80">
                            <ol class="flex justify-center items-center gap-3">
                                <li><a href="{{ route('home') }}">خانه</a></li>
                                <li aria-hidden="true">/</li>
                                <li aria-current="page">{{ $crumb }}</li>
                            </ol>
                        </nav>
                        <h1
                            id="page-title"
                            class="text-4xl leading-[1.4!important] font-bold tracking-tight md:text-5xl"
                        >{{ $heading }}</h1>

                        <p class="mt-6 text-sm leading-8 text-white/90">{{ $intro }}</p>
                    </div>
                </section>

            </div>
        </div>
