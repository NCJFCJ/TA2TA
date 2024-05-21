<div id="iawp-parent" class="iawp-parent">
    <div class="header">
        <div class="logo">
            <img src="{{iawp_url_to('img/logo.png')}}" data-testid="logo"/>
        </div>
        <a href="https://independentwp.com/knowledgebase/"
           class="iawp-button purple"
           target="_blank">
            <span class="dashicons dashicons-sos"></span>
            <span>{{ __('Get Help', 'independent-analytics') }}</span>
        </a>
    </div>
    @yield('content')
</div>