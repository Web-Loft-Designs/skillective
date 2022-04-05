<front-login-form-modal
        {{--v-bind:login-using-instagram-url="'{{ route('social.redirect',['provider' => 'ig']) }}'"--}}
        :login-using-google-url="'{{ route('social.redirect',['provider' => 'google']) }}'"
        :login-using-facebook-url="'{{ route('social.redirect',['provider' => 'facebook']) }}'"
></front-login-form-modal>