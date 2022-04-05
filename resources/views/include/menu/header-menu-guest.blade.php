<nav-links>
    @if (isset($settings['header-menu']))
        @foreach ($settings['header-menu'] as $link)
            <li><a href="{{ $link['link_url'] }}" class="{{ isset($link['link_class'])?$link['link_class']:'' }}">{{ $link['link_title'] }}</a></li>
        @endforeach
    @endif

    @if (Auth::guest())
        @if (isset($settings['free_instructor_registration_enabled']) && $settings['free_instructor_registration_enabled']==1)
            <li class="{{ (request()->is('instructor/register')) ? 'current-menu' : '' }}"><a href="{!! route('instructor.register') !!}">Become an Instructor</a></li>
        @endif

        <nav-drop-down>
            <template v-slot:button>Sign up</template>
            <template v-slot:default>
                <li><a href="{!! route('student.register') !!}">Sign up as a client</a></li>
                <li><a href="/#become-an-instructor">Sign up as an instructor</a></li>
            </template>
        </nav-drop-down>
        
        <li><a href="#" class="open-login-popup-link">Login</a></li>
    @endif
</nav-links>