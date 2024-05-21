/**
 * This JS file was auto-generated via Terser.
 *
 * Contributors should avoid editing this file, but instead edit the associated
 * non minified file file. For more information, check out our engineering docs
 * on how we handle JS minification in our engineering docs.
 *
 * @see: https://evnt.is/dev-docs-minification
 */

tribe.filterBar=tribe.filterBar||{},tribe.filterBar.filterClear={},function($,obj){"use strict";var $document=$(document);obj.selectors={clearButton:'[data-js="tribe-filter-bar-c-clear-button"]',selectedFilter:'[data-js="tribe-filter-bar__selected-filter"]'},obj.handleClearClick=function(event){var $container=event.data.container,location=$container.find(obj.selectors.selectedFilter).toArray().reduce((function(loc,filter){var name=$(filter).data("filter-name");return name?tribe.filterBar.filters.removeKeyValueFromQuery(loc,name,!0):loc}),tribe.filterBar.filters.getCurrentUrlAsObject($container));tribe.filterBar.filters.submitRequest($container,location.href)},obj.unbindEvents=function($container){$container.find(obj.selectors.clearButton).off()},obj.bindEvents=function($container){$container.find(obj.selectors.clearButton).each((function(index,clearButton){var $clearButton=$(clearButton);$clearButton.on("click",{target:$clearButton,container:$container},obj.handleClearClick)}))},obj.deinit=function(event){var $container=event.data.container;obj.unbindEvents($container),$container.off("beforeAjaxSuccess.tribeEvents",obj.deinit)},obj.init=function(event,index,$container){obj.bindEvents($container),$container.on("beforeAjaxSuccess.tribeEvents",{container:$container},obj.deinit)},obj.ready=function(){$document.on("afterSetup.tribeEvents",tribe.events.views.manager.selectors.container,obj.init)},$(obj.ready)}(jQuery,tribe.filterBar.filterClear);