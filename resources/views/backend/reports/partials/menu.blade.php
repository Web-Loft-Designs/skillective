<div class="sticky-nav"  v-sticky="true" sticky-offset="{top: 100, bottom: 20}">
    <h2>Reports</h2>
    <ul>
        <li class="{{ Request::is('backend/reports/demographic') ? 'active' : '' }}"><a href="{{ route('backend.reports.demographic') }}">Demographic</a></li>
        <li class="{{ Request::is('backend/reports/geographics') ? 'active' : '' }}"><a href="{{ route('backend.reports.geographics') }}">Geographics</a></li>
        <li class="{{ Request::is('backend/reports/other') ? 'active' : '' }}"><a href="{{ route('backend.reports.other') }}">Other</a></li>
    </ul>
</div>