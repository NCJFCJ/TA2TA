/**
 * This JS file was auto-generated via Terser.
 *
 * Contributors should avoid editing this file, but instead edit the associated
 * non minified file file. For more information, check out our engineering docs
 * on how we handle JS minification in our engineering docs.
 *
 * @see: https://evnt.is/dev-docs-minification
 */

tribe.filterBar=tribe.filterBar||{},tribe.filterBar.filterCheckboxes={},function($,obj){"use strict";var $document=$(document);obj.selectors={checkbox:'[data-js="tribe-filter-bar-c-checkbox-input"]'},obj.handleCheckboxChange=function(event){var key=event.target.name,value=event.target.value;if(key&&value){var urlObject=tribe.filterBar.filters.getCurrentUrlAsObject(event.data.container),location=event.target.checked?tribe.filterBar.filters.addKeyValueToQuery(urlObject,key,value):tribe.filterBar.filters.removeKeyValueFromQuery(urlObject,key,value);tribe.filterBar.filters.submitRequest(event.data.container,location.href)}},obj.unbindEvents=function($container){$container.find(obj.selectors.checkbox).off()},obj.bindEvents=function($container){$container.find(obj.selectors.checkbox).each((function(index,checkbox){$(checkbox).on("change",{container:$container},obj.handleCheckboxChange)}))},obj.deinit=function(event){var $container=event.data.container;obj.unbindEvents($container),$container.off("beforeAjaxSuccess.tribeEvents",obj.deinit)},obj.init=function(event,index,$container){obj.bindEvents($container),$container.on("beforeAjaxSuccess.tribeEvents",{container:$container},obj.deinit)},obj.ready=function(){$document.on("afterSetup.tribeEvents",tribe.events.views.manager.selectors.container,obj.init)},$(obj.ready)}(jQuery,tribe.filterBar.filterCheckboxes);