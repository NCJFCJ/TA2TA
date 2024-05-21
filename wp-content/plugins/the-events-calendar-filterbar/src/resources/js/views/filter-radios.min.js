/**
 * This JS file was auto-generated via Terser.
 *
 * Contributors should avoid editing this file, but instead edit the associated
 * non minified file file. For more information, check out our engineering docs
 * on how we handle JS minification in our engineering docs.
 *
 * @see: https://evnt.is/dev-docs-minification
 */

tribe.filterBar=tribe.filterBar||{},tribe.filterBar.filterRadios={},function($,obj){"use strict";var $document=$(document);obj.selectors={radio:'[data-js="tribe-filter-bar-c-radio-input"]'},obj.handleRadioChange=function(event){var key=event.target.name,value=event.target.value;if(key&&value){var urlObject=tribe.filterBar.filters.getCurrentUrlAsObject(event.data.container),modifiedLocation=tribe.filterBar.filters.removeKeyValueFromQuery(urlObject,key,!0),newLocation=tribe.filterBar.filters.addKeyValueToQuery(modifiedLocation,key,value);tribe.filterBar.filters.submitRequest(event.data.container,newLocation.href)}},obj.unbindEvents=function($container){$container.find(obj.selectors.radio).off()},obj.bindEvents=function($container){$container.find(obj.selectors.radio).each((function(index,radio){$(radio).on("change",{container:$container},obj.handleRadioChange)}))},obj.deinit=function(event){var $container=event.data.container;obj.unbindEvents($container),$container.off("beforeAjaxSuccess.tribeEvents",obj.deinit)},obj.init=function(event,index,$container){obj.bindEvents($container),$container.on("beforeAjaxSuccess.tribeEvents",{container:$container},obj.deinit)},obj.ready=function(){$document.on("afterSetup.tribeEvents",tribe.events.views.manager.selectors.container,obj.init)},$(obj.ready)}(jQuery,tribe.filterBar.filterRadios);