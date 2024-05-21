/**
 * This JS file was auto-generated via Terser.
 *
 * Contributors should avoid editing this file, but instead edit the associated
 * non minified file file. For more information, check out our engineering docs
 * on how we handle JS minification in our engineering docs.
 *
 * @see: https://evnt.is/dev-docs-minification
 */

tribe.filterBar=tribe.filterBar||{},tribe.filterBar.filterRemove={},function($,obj){"use strict";var $document=$(document);obj.selectors={removeButton:'[data-js="tribe-filter-bar-c-pill__remove-button"]',pillFilterName:"[data-filter-name]"},obj.handleRemoveClick=function(event){var $removeButton=event.data.target,$container=event.data.container,$pill=$removeButton.closest("[data-filter-name]");if($pill.length){var name=$pill.data("filter-name");if(name){var urlObject=tribe.filterBar.filters.getCurrentUrlAsObject(event.data.container),location=tribe.filterBar.filters.removeKeyValueFromQuery(urlObject,name,!0);tribe.filterBar.filters.submitRequest($container,location.href)}}},obj.unbindEvents=function($container){$container.find(obj.selectors.removeButton).off()},obj.bindEvents=function($container){$container.find(obj.selectors.removeButton).each((function(index,removeButton){var $removeButton=$(removeButton);$removeButton.on("click",{target:$removeButton,container:$container},obj.handleRemoveClick)}))},obj.deinit=function(event){var $container=event.data.container;obj.unbindEvents($container),$container.off("beforeAjaxSuccess.tribeEvents",obj.deinit)},obj.init=function(event,index,$container){obj.bindEvents($container),$container.on("beforeAjaxSuccess.tribeEvents",{container:$container},obj.deinit)},obj.ready=function(){$document.on("afterSetup.tribeEvents",tribe.events.views.manager.selectors.container,obj.init)},$(obj.ready)}(jQuery,tribe.filterBar.filterRemove);