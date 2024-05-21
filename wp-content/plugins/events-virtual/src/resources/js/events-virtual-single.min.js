/**
 * This JS file was auto-generated via Terser.
 *
 * Contributors should avoid editing this file, but instead edit the associated
 * non minified file file. For more information, check out our engineering docs
 * on how we handle JS minification in our engineering docs.
 *
 * @see: https://evnt.is/dev-docs-minification
 */

tribe.events=tribe.events||{},tribe.events.virtualSingle=tribe.events.virtualSingle||{},function($,obj){obj.GraphVersion="v9.0",obj.facebookInit=function(){if("undefined"==typeof FB)return!1;const facebookAppId=tribe_events_virtual_settings.facebookAppId;!facebookAppId||facebookAppId<1||FB.init({appId:facebookAppId,autoLogAppEvents:!0,xfbml:!0,version:obj.GraphVersion})},obj.ready=function(){window.facebookAsyncInit=obj.facebookInit()},$(obj.ready)}(jQuery,tribe.events.virtualSingle);