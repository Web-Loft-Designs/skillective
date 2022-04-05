<footer class="footer">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-sm-8 col-12 copywriting">
                <p>
                    @if (isset($settings['copyright']))
                        {{ str_replace('%Y%', date('Y'), $settings['copyright']) }}
                    @endif
                </p>
                @if (isset($settings['footer-menu']))
                    <p>
                    @foreach ($settings['footer-menu'] as $k=>$link)
                        <a href="{{ $link['link_url'] }}" class="{{ isset($link['link_class'])?$link['link_class']:'' }}" >{{ $link['link_title'] }}</a>@if($k < count($settings['footer-menu'])-1)  |  @endif
                    @endforeach
                    </p>
                @endif
            </div>
            <div class="col-sm-4 col-12 by">
                @if (isset($settings['social-menu']))
                    <p>
                    @foreach ($settings['social-menu'] as $link)
                    <a href="{{ $link['link_url'] }}" class="{{ isset($link['link_class'])?$link['link_class']:'' }}" target="_blank">
                        @if( strpos($link['link_url'], 'twitter.com')!==false )
                            <img src="{{ asset('images/twitter.svg') }}" alt="{{ addslashes($link['link_title']) }}">
                        @elseif( strpos($link['link_url'], 'facebook.com')!==false )
                            <img src="{{ asset('images/facebook.svg') }}" alt="{{ addslashes($link['link_title']) }}">
                        @elseif( strpos($link['link_url'], 'instagram.com')!==false )
                            <img src="{{ asset('images/instagram.svg') }}" alt="{{ addslashes($link['link_title']) }}">
                        @else
                            {{ $link['link_title'] }}
                        @endif
                    </a>
                    @endforeach
                    </p>
                @endif
            </div>
        </div>
    </div>
</footer>