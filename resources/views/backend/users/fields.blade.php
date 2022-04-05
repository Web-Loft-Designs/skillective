<div class="form-group col-sm-6">
    {!! Form::label('first_name', 'First Name:') !!}
    {!! Form::text('first_name', $user?$user->first_name:old('first_name'), ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('last_name', 'Last Name:') !!}
    {!! Form::text('last_name', $user?$user->last_name:old('last_name'), ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('email', 'Email:') !!}
    {!! Form::text('email', $user?$user->profile->email:old('email'), ['class' => 'form-control']) !!}
</div>

<!-- Instagram Handle Field -->
<div class="form-group col-sm-6">
    {!! Form::label('instagram_handle', 'Instagram Handle:') !!}
    {!! Form::text('instagram_handle', $user?$user->profile->instagram_handle:old('instagram_handle'), ['class' => 'form-control']) !!}
</div>

<!-- Address Field -->
<div class="form-group col-sm-6">
    {!! Form::label('address', 'Address:') !!}
    {!! Form::text('address', $user?$user->profile->address:old('address'), ['class' => 'form-control']) !!}
</div>

<!-- City Field -->
<div class="form-group col-sm-6">
    {!! Form::label('city', 'City:') !!}
    {!! Form::text('city', $user?$user->profile->city:old('city'), ['class' => 'form-control']) !!}
</div>

<!-- State Field -->
<div class="form-group col-sm-6">
    {!! Form::label('state', 'State:') !!}
    {!! Form::text('state', $user?$user->profile->state:old('state'), ['class' => 'form-control']) !!}
</div>

<!-- Zip Field -->
<div class="form-group col-sm-6">
    {!! Form::label('zip', 'Zip:') !!}
    {!! Form::text('zip', $user?$user->profile->zip:old('zip'), ['class' => 'form-control']) !!}
</div>

<!-- Mobile Phone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('mobile_phone', 'Mobile Phone:') !!}
    {!! Form::text('mobile_phone', $user?$user->profile->mobile_phone:old('mobile_phone'), ['class' => 'form-control']) !!}
</div>

<!-- Dob Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dob', 'Dob:') !!}
    {!! Form::text('dob', $user?$user->profile->dob:old('dob'), ['class' => 'form-control']) !!}
</div>

<!-- About Me Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('about_me', 'About Me:') !!}
    {!! Form::textarea('about_me', $user?$user->profile->about_me:old('about_me'), ['class' => 'form-control']) !!}
</div>

<!-- Avatar Field -->
<div class="form-group col-sm-6">
    {!! Form::label('avatar', 'Avatar:') !!}
    {!! Form::text('avatar', $user?$user->profile->avatar:old('avatar'), ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('backend.users.index') !!}" class="btn btn-default">Cancel</a>
</div>
