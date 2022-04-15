<p>
    Regards,<br />
    @if(!empty($sender_first_name) && !empty($sender_last_name))
        {!! $sender_first_name !!} {!! $sender_last_name !!}
        <br/>
        <br/>
    @endif
    <a href="{{ config('app.url') }}" target="_blank">{{ config('app.name') }}</a>
</p>
