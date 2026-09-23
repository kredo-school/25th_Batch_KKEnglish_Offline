@props([
    'message' => 'Welcome back!',
    'name' => null,
])

<div id="welcomeMonkey" class="welcome-monkey">

    {{-- Speech Bubble --}}
    <div class="monkey-message">

        <div class="monkey-message-main">
            {{ $message }}
        </div>

        @if($name)
            <div class="monkey-name">
                {{ $name }}!
            </div>
        @endif

    </div>


    {{-- Monkey --}}
    <img
        src="{{ asset('images/kk-monkey.png') }}"
        alt="KK English Monkey"
        class="monkey-image"
    >

</div>