@if(!auth()->isLoggedIn())
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <i class="fa fa-share-square-o" aria-hidden="true"></i>
            {{t('oauth.nav.social_media')}}
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li><a href="{{url('oauth/dropbox/login')}}"><i class="fa fa-dropbox" aria-hidden="true"></i> {{t('oauth.nav.login_via', ['Dropbox'])}}</a></li>
            <li><a href="{{url('oauth/facebook/login')}}"><i class="fa fa-facebook" aria-hidden="true"></i> {{t('oauth.nav.login_via', ['Facebook'])}}</a></li>
            <li><a href="{{url('oauth/github/login')}}"><i class="fa fa-github" aria-hidden="true"></i> {{t('oauth.nav.login_via', ['GitHub'])}}</a></li>
            <li><a href="{{url('oauth/spotify/login')}}"><i class="fa fa-spotify" aria-hidden="true"></i> {{t('oauth.nav.login_via', ['Spotify'])}}</a></li>
        </ul>
    </li>
@else
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
            {{auth()->name()}}
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="{{url('oauth/logout')}}" onclick="event.preventDefault(); $(this).next().submit();">
                    <i class="fa fa-sign-out" aria-hidden="true"></i> {{t('oauth.nav.logout')}}
                </a>
                <form action="{{url('oauth/logout')}}" method="POST" style="display:none">
                    <input name="_token" value="{{csrf_token()}}" type="hidden"/>
                </form>
            </li>
        </ul>
    </li>
@endif
