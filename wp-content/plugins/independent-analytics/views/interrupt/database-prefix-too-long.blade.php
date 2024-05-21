@php /** @var string $prefix */ @endphp
@php /** @var int $length */ @endphp

<div class="settings-container interrupt-message"
     data-controller="migration-redirect"
>
    <h2>{{__("Your database prefix is longer than 25 characters", "independent-analytics")}}</h2>
    <p>
        {{__("Your current database prefix ", "independent-analytics")}} "{{$prefix}}" {{__(" is longer than the allowed 25 characters.", "independent-analytics")}}
    </p>
    <p>
        {{__("Please use ", "independent-analytics")}} <a target="_blank" href="https://wordpress.org/plugins/brozzme-db-prefix-change/">{{__("Brozzme Database Prefix Changer", "independent-analytics")}}</a> {{__(" to update your prefix to be 25 characters or less.")}}
    </p>
</div>
