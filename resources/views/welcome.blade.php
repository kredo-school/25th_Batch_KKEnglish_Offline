<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KK English</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
    </style>
</head>
<body class="bg-white text-slate-900">

    {{-- Header --}}
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow ring-1 ring-slate-200">
                        <span class="text-2xl">🅺</span>
                    </div>
                </div>

                <nav class="hidden lg:flex items-center gap-10 text-[15px] font-semibold text-slate-500">
                    <a href="#features" class="hover:text-slate-900">Features</a>
                    <a href="#courses" class="hover:text-slate-900">Courses</a>
                    <a href="#pricing" class="hover:text-slate-900">Pricing</a>
                    <a href="#teachers" class="hover:text-slate-900">Teachers</a>
                    <a href="#reviews" class="hover:text-slate-900">Reviews</a>
                    <a href="#faq" class="hover:text-slate-900">FAQ</a>
                </nav>

                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="text-slate-600 font-semibold hover:text-slate-900">Login</a>
                    <a href="{{ route('register') }}" class="text-slate-600 font-semibold hover:text-slate-900">Register</a>
                    <a href="#trial" class="rounded-full bg-amber-400 px-6 py-3 font-extrabold text-white shadow hover:bg-amber-500">
                        Free Trial 🎁
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4 pb-16 lg:pb-24">
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-5 py-2 text-amber-500 font-semibold shadow-sm ring-1 ring-amber-100">
                <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                2 Free Trial Lessons — Limited Time!
            </div>

            <div class="mt-10 grid gap-12 lg:grid-cols-2 lg:items-center">
                <div>
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-[0.95] tracking-tight">
                        <span class="block text-slate-950">Speak English</span>
                        <span class="block text-sky-500 mt-3">with confidence.</span>
                        <span class="block text-amber-500 mt-3">Every day.</span>
                    </h1>

                    <p class="mt-8 max-w-xl text-xl text-slate-600 leading-8">
                        1-on-1 lessons with TESOL-certified Filipino teachers.
                        <br class="hidden sm:block" />
                        Just 25 minutes a day — on your schedule.
                    </p>

                    <div class="mt-10 flex flex-col sm:flex-row gap-4">
                        <a href="#trial" class="inline-flex items-center justify-center rounded-3xl bg-amber-400 px-8 py-5 text-lg font-extrabold text-white shadow-lg shadow-amber-200 hover:bg-amber-500">
                            Start Free Trial →
                        </a>
                        <a href="#pricing" class="inline-flex items-center justify-center rounded-3xl border-2 border-sky-400 px-8 py-5 text-lg font-extrabold text-sky-500 hover:bg-sky-50">
                            View Pricing
                        </a>
                    </div>

                    <div class="mt-14 grid grid-cols-3 gap-6 max-w-lg">
                        <div>
                            <div class="text-3xl font-black text-amber-500">30K+</div>
                            <div class="mt-1 text-sm font-semibold text-slate-500">Students</div>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-amber-500">4.9★</div>
                            <div class="mt-1 text-sm font-semibold text-slate-500">Avg Rating</div>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-amber-500">1,500+</div>
                            <div class="mt-1 text-sm font-semibold text-slate-500">Teachers</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="rounded-[2rem] bg-gradient-to-br from-white to-slate-50 p-8 shadow-xl ring-1 ring-slate-100">
                        <div class="flex h-[420px] items-center justify-center rounded-[1.5rem] bg-white shadow-inner ring-1 ring-slate-100">
                            <div class="text-center">
                                <div class="text-8xl font-black tracking-tight">
                                    <span class="text-amber-400">K</span><span class="text-sky-500">K</span>
                                </div>
                                <div class="mt-2 text-4xl font-black text-sky-500">English</div>
                                <div class="mt-6 text-lg text-slate-500">Hero visual placeholder</div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -left-2 bottom-6 rounded-2xl bg-white px-5 py-4 shadow-xl ring-1 ring-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500 text-white text-xl">✓</div>
                            <div>
                                <div class="font-bold text-slate-900">Lesson in progress</div>
                                <div class="text-sm text-slate-500">With Teacher Maria</div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -top-4 -right-2 rounded-2xl bg-white px-5 py-4 shadow-xl ring-1 ring-slate-100">
                        <div class="text-sm text-slate-400">This month</div>
                        <div class="text-3xl font-black text-sky-500">14.5h</div>
                        <div class="text-sm font-bold text-emerald-500">↑ +23%</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="bg-sky-50/70 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="text-sm font-black tracking-[0.25em] text-amber-500">WHY KK ENGLISH?</div>
                <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">
                    Built to keep you <span class="text-sky-500">going</span> 🐒
                </h2>
                <p class="mx-auto mt-5 max-w-2xl text-lg text-slate-500 leading-8">
                    The #1 reason people stop learning English is consistency. KK English is engineered from the ground up to keep you on track.
                </p>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-3">
                @foreach ([
                    ['icon'=>'🎯','title'=>'1-on-1 Lessons','desc'=>'Fully private sessions with certified Filipino native speakers. Learn at your own pace, every day.'],
                    ['icon'=>'⏰','title'=>'24/7 Availability','desc'=>'Early morning or late at night — book whenever suits you. Perfect for busy schedules.'],
                    ['icon'=>'💻','title'=>'100% Online','desc'=>'Study from your phone, PC, or tablet anywhere. No commute, no waiting rooms.'],
                    ['icon'=>'📈','title'=>'Proven Curriculum','desc'=>'Our science-backed method gets you speaking faster. Covers TOEIC, daily conversation, and business English.'],
                    ['icon'=>'🎓','title'=>'TESOL-Certified Teachers','desc'=>'Every teacher passed our rigorous screening. Only the top 3% of applicants make the cut.'],
                    ['icon'=>'🎁','title'=>'2 Free Trial Lessons','desc'=>'Try two full lessons before committing — no credit card required. Seriously.'],
                ] as $item)
                    <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-sky-50 text-3xl">
                            {{ $item['icon'] }}
                        </div>
                        <h3 class="mt-8 text-2xl font-black">{{ $item['title'] }}</h3>
                        <p class="mt-4 text-lg leading-8 text-slate-600">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="text-sm font-black tracking-[0.25em] text-sky-500">HOW IT WORKS</div>
                <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">
                    First lesson in <span class="text-amber-500">under 10 minutes</span>
                </h2>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-4">
                @foreach ([
                    ['step'=>'STEP 01','title'=>'Sign Up Free','desc'=>'Email only — 30 seconds','icon'=>'📝'],
                    ['step'=>'STEP 02','title'=>'Pick a Teacher','desc'=>'Browse & book your time','icon'=>'🗓️'],
                    ['step'=>'STEP 03','title'=>'Take a Lesson','desc'=>'25-min session via Zoom','icon'=>'🎥'],
                    ['step'=>'STEP 04','title'=>'Start Growing','desc'=>'Full access from day one','icon'=>'🚀'],
                ] as $step)
                    <div class="rounded-[2rem] bg-sky-50 p-8 text-center ring-2 ring-dashed ring-sky-200">
                        <div class="text-4xl">{{ $step['icon'] }}</div>
                        <div class="mt-4 text-sm font-black tracking-[0.2em] text-amber-500">{{ $step['step'] }}</div>
                        <h3 class="mt-3 text-2xl font-black">{{ $step['title'] }}</h3>
                        <p class="mt-3 text-slate-500">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section id="pricing" class="bg-amber-50/40 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="text-sm font-black tracking-[0.25em] text-amber-500">PRICING</div>
                <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">
                    Simple, <span class="text-sky-500">transparent</span> pricing
                </h2>
                <p class="mt-4 text-lg text-slate-500">Month-to-month. Cancel anytime. No strings.</p>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-3">
                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                    <p class="font-semibold text-slate-400">For casual learners</p>
                    <h3 class="mt-2 text-4xl font-black">Light</h3>
                    <div class="mt-4 text-6xl font-black text-sky-500">$39<span class="text-2xl text-slate-400">/mo</span></div>
                    <p class="mt-2 text-slate-400">8 lessons/month · 25 min each</p>
                    <ul class="mt-8 space-y-4 text-lg text-slate-700">
                        <li>✓ 1-on-1 lessons</li>
                        <li>✓ Study materials</li>
                        <li>✓ Lesson recordings</li>
                        <li>✓ Chat support</li>
                    </ul>
                    <a href="#trial" class="mt-10 block rounded-2xl bg-sky-500 px-6 py-4 text-center text-lg font-black text-white hover:bg-sky-600">
                        Choose this plan
                    </a>
                </div>

                <div class="relative rounded-[2rem] bg-white p-8 shadow-xl ring-2 ring-amber-400">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 rounded-full bg-amber-400 px-5 py-2 text-sm font-black text-white shadow">
                        🏆 Best Value
                    </div>
                    <p class="font-semibold text-slate-400">Most popular</p>
                    <h3 class="mt-2 text-4xl font-black">Standard</h3>
                    <div class="mt-4 text-6xl font-black text-amber-500">$79<span class="text-2xl text-slate-400">/mo</span></div>
                    <p class="mt-2 text-slate-400">20 lessons/month · 25 min each</p>
                    <ul class="mt-8 space-y-4 text-lg text-slate-700">
                        <li>✓ 1-on-1 lessons</li>
                        <li>✓ Study materials</li>
                        <li>✓ Lesson recordings</li>
                        <li>✓ Chat support</li>
                        <li>✓ Monthly counseling</li>
                    </ul>
                    <a href="#trial" class="mt-10 block rounded-2xl bg-amber-400 px-6 py-4 text-center text-lg font-black text-white hover:bg-amber-500">
                        Choose this plan
                    </a>
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                    <p class="font-semibold text-slate-400">For serious learners</p>
                    <h3 class="mt-2 text-4xl font-black">Premium</h3>
                    <div class="mt-4 text-6xl font-black text-sky-500">$149<span class="text-2xl text-slate-400">/mo</span></div>
                    <p class="mt-2 text-slate-400">40 lessons/month · 50 min each</p>
                    <ul class="mt-8 space-y-4 text-lg text-slate-700">
                        <li>✓ 1-on-1 lessons</li>
                        <li>✓ Study materials</li>
                        <li>✓ Lesson recordings</li>
                        <li>✓ 24/7 chat support</li>
                        <li>✓ Weekly counseling</li>
                        <li>✓ Dedicated trainer</li>
                    </ul>
                    <a href="#trial" class="mt-10 block rounded-2xl bg-sky-500 px-6 py-4 text-center text-lg font-black text-white hover:bg-sky-600">
                        Choose this plan
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Teachers --}}
    <section id="teachers" class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="text-sm font-black tracking-[0.25em] text-sky-500">MEET OUR TEACHERS</div>
                <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">
                    Every teacher is <span class="text-amber-500">TESOL certified</span> 🎓
                </h2>
                <p class="mt-4 text-lg text-slate-500">Handpicked from 1,500+ applicants through our rigorous screening</p>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-4">
                @foreach ([
                    ['name'=>'Maria Santos','place'=>'Manila, Philippines','focus'=>'Business English / TOEIC','exp'=>'8 yrs teaching','rating'=>'4.9','reviews'=>'312'],
                    ['name'=>'John dela Cruz','place'=>'Cebu, Philippines','focus'=>'Daily Conversation','exp'=>'5 yrs teaching','rating'=>'4.8','reviews'=>'245'],
                    ['name'=>'Anna Reyes','place'=>'Davao, Philippines','focus'=>'Kids English / Pronunciation','exp'=>'6 yrs teaching','rating'=>'5','reviews'=>'189'],
                    ['name'=>'Carlos Mendoza','place'=>'Cebu, Philippines','focus'=>'Exam Prep / Academic','exp'=>'10 yrs teaching','rating'=>'4.9','reviews'=>'428'],
                ] as $teacher)
                    <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
                        <div class="h-72 bg-gradient-to-br from-slate-200 to-slate-300"></div>
                        <div class="p-6">
                            <h3 class="text-2xl font-black">{{ $teacher['name'] }}</h3>
                            <p class="mt-1 text-slate-400">{{ $teacher['place'] }}</p>
                            <p class="mt-2 font-bold text-sky-500">{{ $teacher['focus'] }}</p>
                            <p class="mt-2 text-slate-500">{{ $teacher['exp'] }}</p>
                            <div class="mt-4 text-amber-500 font-black">★★★★☆ <span class="text-slate-900">{{ $teacher['rating'] }}</span> <span class="text-slate-400">({{ $teacher['reviews'] }})</span></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Reviews --}}
    <section id="reviews" class="bg-sky-500 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center text-white">
                <div class="text-sm font-black tracking-[0.25em] text-sky-100">STUDENT STORIES</div>
                <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">
                    Real students. Real <span class="text-amber-300">results.</span> 🎉
                </h2>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-3">
                @foreach ([
                    ['quote'=>'My TOEIC score jumped from 680 to 850 in just 3 months! The daily habit system is genius and every teacher was so warm and encouraging.','name'=>'Sayaka T.','meta'=>'29, Office Worker','initial'=>'S'],
                    ['quote'=>'Six months before my overseas posting. I can now run meetings in English with full confidence. Best value for money — period.','name'=>'Kenta S.','meta'=>'35, Software Engineer','initial'=>'K'],
                    ['quote'=>'Used it to prep for English job interviews. After many mock sessions with my teacher, I landed my dream company. So grateful!','name'=>'Aoi Y.','meta'=>'22, University Student','initial'=>'A'],
                ] as $review)
                    <div class="rounded-[2rem] border border-white/20 bg-white/10 p-8 text-white backdrop-blur-sm">
                        <div class="text-2xl text-amber-300">★★★★★</div>
                        <p class="mt-6 text-lg leading-8">"{{ $review['quote'] }}"</p>
                        <div class="mt-8 flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-400 text-white font-black">
                                {{ $review['initial'] }}
                            </div>
                            <div>
                                <div class="font-black">{{ $review['name'] }}</div>
                                <div class="text-sky-100">{{ $review['meta'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="bg-white py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 text-center lg:grid-cols-4">
                @foreach ([
                    ['n'=>'30,000+','l'=>'Total Students'],
                    ['n'=>'97%','l'=>'Retention Rate'],
                    ['n'=>'1,500+','l'=>'Certified Teachers'],
                    ['n'=>'4.9 / 5','l'=>'Average Rating'],
                ] as $stat)
                    <div>
                        <div class="text-4xl sm:text-5xl font-black text-amber-500">{{ $stat['n'] }}</div>
                        <div class="mt-2 text-lg font-semibold text-slate-400">{{ $stat['l'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="bg-sky-50/60 py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="text-sm font-black tracking-[0.25em] text-amber-500">FREQUENTLY ASKED QUESTIONS</div>
                <h2 class="mt-4 text-4xl sm:text-5xl font-black tracking-tight">FAQ 🐒</h2>
            </div>

            <div class="mt-12 space-y-4">
                @foreach ([
                    ['q'=>'Can I take lessons at any time?','a'=>'Yes. In this sample layout, lessons are presented as flexible and online. Confirm exact availability rules before publishing.'],
                    ['q'=>'Are all teachers native speakers?','a'=>'The screenshot suggests Filipino teachers and TESOL-certified instructors, not native speakers. That wording should be checked carefully.'],
                    ['q'=>'Can I cancel anytime?','a'=>'The pricing section suggests month-to-month billing with cancellation anytime. Confirm subscription terms before launch.'],
                    ['q'=>'Can I study on my smartphone?','a'=>'Yes, the page messaging implies phone, PC, and tablet support.'],
                ] as $faq)
                    <details class="group rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-xl font-black">
                            <span>{{ $faq['q'] }}</span>
                            <span class="text-sky-500 transition group-open:rotate-180">▼</span>
                        </summary>
                        <p class="mt-4 text-slate-600 leading-8">{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section id="trial" class="relative overflow-hidden bg-amber-400 py-24 text-center">
        <div class="absolute inset-y-0 right-0 w-1/3 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.25),transparent_60%)]"></div>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 relative">
            <h2 class="text-4xl sm:text-5xl font-black text-white">Try a free lesson today! 🎁</h2>
            <p class="mt-6 text-xl text-white/95">
                No credit card required. Sign up in 2 minutes and start speaking English today.
            </p>
            <a href="#" class="mt-10 inline-flex items-center justify-center rounded-3xl bg-white px-10 py-5 text-xl font-black text-amber-500 shadow-lg hover:bg-amber-50">
                Book Your Free Trial →
            </a>
            <p class="mt-6 text-white/90">Up to 2 lessons free · Cancel anytime · No risk</p>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-950 text-slate-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid gap-12 lg:grid-cols-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-2xl shadow">
                            🅺
                        </div>
                        <div class="text-2xl font-black text-white">KK English</div>
                    </div>
                    <p class="mt-6 max-w-sm text-lg leading-8">
                        The online English school built for consistency. Speak with confidence — starting today.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-black text-white">Services</h3>
                    <ul class="mt-5 space-y-4">
                        <li><a href="#features" class="hover:text-white">Features</a></li>
                        <li><a href="#courses" class="hover:text-white">Courses</a></li>
                        <li><a href="#pricing" class="hover:text-white">Pricing</a></li>
                        <li><a href="#trial" class="hover:text-white">Free Trial</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-black text-white">Support</h3>
                    <ul class="mt-5 space-y-4">
                        <li><a href="#faq" class="hover:text-white">FAQ</a></li>
                        <li><a href="#contact" class="hover:text-white">Contact Us</a></li>
                        <li><a href="#blog" class="hover:text-white">Learning Blog</a></li>
                        <li><a href="#reviews" class="hover:text-white">Reviews</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-black text-white">Company</h3>
                    <ul class="mt-5 space-y-4">
                        <li><a href="#about" class="hover:text-white">About Us</a></li>
                        <li><a href="#privacy" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="#terms" class="hover:text-white">Terms of Service</a></li>
                        <li><a href="#careers" class="hover:text-white">Careers</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-14 border-t border-white/10 pt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between text-sm text-slate-400">
                <p>© 2026 KK English Inc. All rights reserved.</p>
                <p>Operated by KK English Inc. · San Francisco, CA</p>
            </div>
        </div>
    </footer>

</body>
</html>
