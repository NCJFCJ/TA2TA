jQuery(document).ready(function(){jQuery(".wpse-duplicate-trigger").on("click",function(e){e.preventDefault();var s=jQuery(this).parents(".remodal");if(!s.find(".duplicate-modal-post-selector").val())return s.find(".duplicate-modal-post-selector")[0].reportValidity(),!0;window.wpseAddRowExtraData=s.find("input,select,textarea").serialize();var r=parseInt(s.find('[name="number_of_copies"]').val()),n=vgse_editor_settings.duplicate_batch_size,t=Math.ceil(r/n),a=jQuery(".remodal-bg").data("nonce"),i=jQuery("#post_type_new_row").val(),o=window.wpseAddRowExtraData;loading_ajax({estado:!1});var d=s.find(".response");d.before('<div id="be-duplicate-nanobar-container" />');var l={classname:"be-progress-bar",target:document.getElementById("be-duplicate-nanobar-container")},p=new Nanobar(l);p.go(2);var c=r,u=n;beAjaxLoop({totalCalls:t,url:vgse_global_data.ajax_url,method:"POST",data:{action:"vgse_insert_individual_post",nonce:a,post_type:i,rows:0,extra_data:o,dont_return_new_rows:5<c?"yes":null},prepareData:function(e,t){var a=(t.current-1)*n,o=Math.min(a+n,r)-a;return e.rows=o,e},onSuccess:function(e,t,a){if(loading_ajax({estado:!1}),!0===e.success||e.data||(e={data:{message:vgse_editor_settings.texts.http_error_try_now},success:!1}),!e.success)return jQuery(d).append("<p>"+e.data.message+"</p>"),confirm(e.data.message)?(t.current=0,d.scrollTop(d[0].scrollHeight),!0):(jQuery(d).append(vgse_editor_settings.texts.saving_stop_error),d.scrollTop(d[0].scrollHeight),!1);vgseAddFoundRowsCount(window.beFoundRows+parseInt(a.rows)),vgAddRowsToSheet(e.data.message,"prepend"),jQuery("body").trigger("vgSheetEditor:afterNewRowsInsert",[e,i,a.rows]);var o=t.current/t.totalCalls*100;o<1&&(o=1),p.go(o);var r=parseInt(u)*t.current>c?c:parseInt(u)*t.current,n=(n=vgse_editor_settings.texts.paged_batch_saved.replace("{updated}",r)).replace("{total}",c);return jQuery(d).empty().append("<p>"+n+"</p>"),e.data.force_complete&&(t.current=t.totalCalls),t.current===t.totalCalls&&(jQuery(d).append("<p>"+vgse_editor_settings.texts.everything_saved+"</p>"),loading_ajax({estado:!1}),p.go(100),notification({mensaje:vgse_editor_settings.texts.process_finished}),d.find(".remodal-cancel").removeClass("hidden"),s.find("#be-duplicate-nanobar-container").remove(),s.find('[name="number_of_copies"]').val("").trigger("change"),5<c&&vgseReloadSpreadsheet(!0)),setTimeout(function(){d.scrollTop(d[0].scrollHeight)},600),!0}})}),jQuery("body").on("vgSheetEditor:afterNewRowsInsert",function(){"undefined"!=typeof wpseAddRowExtraData&&"string"==typeof wpseAddRowExtraData&&-1<wpseAddRowExtraData.indexOf("duplicate_this")&&(wpseAddRowExtraData=null)}),jQuery(document).on("closed",".remodal-duplicate",function(){jQuery(".remodal-duplicate").find(".response").empty()}),jQuery(document).on("opened",".remodal-duplicate",function(){var e=vgseGetSelectedIds(),t=jQuery(".remodal-duplicate");if(t.find('[name="number_of_copies"]').val("1").trigger("change"),e.length){var a=t.find(".duplicate-modal-post-selector"),o=[];e.forEach(function(e){var t=vgse_editor_settings.post_type+"--"+e;a.find('option[value="'+t+'"]').length||a.append('<option value="'+t+'">'+vgse_editor_settings.post_type+" ID "+e+"</option>"),o.push(t)}),a.val(o).trigger("change")}})}),jQuery(document).ready(function(){if("undefined"==typeof hot||!jQuery(".remodal-duplicate").length)return!0;var e=hot.getSettings().contextMenu;void 0===e.items&&(e.items={}),e.items.wpse_duplicate_row={name:vgse_editor_settings.texts.duplicate_row,callback:function(e,t,a){if(beGetModifiedItems().length)alert(vgse_editor_settings.texts.save_changes_before_we_reload);else{var o=[],r=[];t.forEach(function(e){e.start.row<e.end.row?vgseRange(e.start.row,e.end.row).forEach(function(e){r.push(e)}):r.push(e.start.row)}),r.forEach(function(e){o[hot.getDataAtRowProp(e,"ID")]=vgseGetRowTitle(e)});var n=jQuery(".remodal-duplicate").find(".duplicate-modal-post-selector"),s=[];o.forEach(function(e,t){var a=vgse_editor_settings.post_type+"--"+t;n.find('option[value="'+a+'"]').length||n.append('<option value="'+a+'">'+e+" ("+t+")</option>"),s.push(a)}),n.val(s).trigger("change"),jQuery(".duplicate-container  button").click()}}},hot.updateSettings({contextMenu:e})});