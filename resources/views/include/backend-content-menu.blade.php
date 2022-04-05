<div class="sticky-nav" v-sticky="true" sticky-offset="{top: 100, bottom: 20}">
    <h2>Content</h2>
    <ul>
        <li class="{{ Request::is('backend/page*') ? 'active' : '' }}"><a href="{{ route('backend.pages') }}">Pages</a></li>
        <li class="{{ Request::is('backend/genres*') ? 'active' : '' }}"><a href="{!! route('backend.genres.index') !!}">Genres</a></li>
        <li class="{{ Request::is('backend/genre-categor*') ? 'active' : '' }}"><a href="{!! route('backend.genre-categories.index') !!}">Genre Categories</a></li>
        <li class="{{ Request::is('backend/testimonials*') ? 'active' : '' }}"><a href="{{ route('backend.testimonials.index') }}">Testimonials</a></li>
        <li class="{{ Request::is('backend/faqs*') ? 'active' : '' }}"><a href="{!! route('backend.faqs.index') !!}">FAQs</a></li>
        <li class="{{ Request::is('backend/faq-categor*') ? 'active' : '' }}"><a href="{!! route('backend.faq-categories.index') !!}">FAQ Categories</a></li>
    </ul>
</div>