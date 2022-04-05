<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $user->id !!}</p>
</div>

<!-- Instagram Handle Field -->
<div class="form-group">
    {!! Form::label('instagram_handle', 'Instagram Handle:') !!}
    <p>{!! $user->profile->instagram_handle !!}</p>
</div>

<!-- Address Field -->
<div class="form-group">
    {!! Form::label('address', 'Address:') !!}
    <p>{!! $user->profile->address !!}</p>
</div>

<!-- City Field -->
<div class="form-group">
    {!! Form::label('city', 'City:') !!}
    <p>{!! $user->profile->city !!}</p>
</div>

<!-- State Field -->
<div class="form-group">
    {!! Form::label('state', 'State:') !!}
    <p>{!! $user->profile->state !!}</p>
</div>

<!-- Zip Field -->
<div class="form-group">
    {!! Form::label('zip', 'Zip:') !!}
    <p>{!! $user->profile->zip !!}</p>
</div>

<!-- Mobile Phone Field -->
<div class="form-group">
    {!! Form::label('mobile_phone', 'Mobile Phone:') !!}
    <p>{!! $user->profile->mobile_phone !!}</p>
</div>

<!-- Dob Field -->
<div class="form-group">
    {!! Form::label('dob', 'Dob:') !!}
    <p>{!! $user->profile->dob !!}</p>
</div>

<!-- About Me Field -->
<div class="form-group">
    {!! Form::label('about_me', 'About Me:') !!}
    <p>{!! $user->profile->about_me !!}</p>
</div>

<!-- Avatar Field -->
<div class="form-group">
    {!! Form::label('avatar', 'Avatar:') !!}
    <p>{!! $user->profile->avatar !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $user->profile->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $user->profile->updated_at !!}</p>
</div>

