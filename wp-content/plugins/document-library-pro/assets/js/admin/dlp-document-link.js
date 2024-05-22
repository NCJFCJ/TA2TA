jQuery((function(e){const i=function(){e("#dlp_add_file_button").on("click",this.handleAddFile),e("#dlp_remove_file_button").on("click",!0,this.handleRemoveFile),e("#dlp_document_link_type").on("change",this.handleSelectBox),e(".dlp-version-history-toggle").on("click",this.toggleVersionHistory),e(".dlp-version-history-list").on("click",'input[type="radio"], a.filename',this.selectHistoricalVersion).on("click","a.edit-version",this.editVersionInfo).on("click","a.remove-version",!0,this.removeVersion).on("click",".dlp_version_label_inline_editor a.button",!0,this.exitVersionInfoEdit).on("click",".dlp_version_label_inline_editor a.button-cancel",this.exitVersionInfoEdit),e(window).on("beforeunload",this.checkIfDirty),e("form#post").on("submit",this.clearDirty)};i.wpMedia=null,i.isDirty=!1,i.prototype.checkIfDirty=function(e){if(console.log(i.isDirty),i.isDirty)return dlpAdminObject.i18n.before_unload},i.prototype.clearDirty=function(e){i.isDirty=!1},i.prototype.handleSelectBox=function(i){const t=e(this).find(":selected").val(),l=e("#dlp_file_attachment_details"),n=e("#dlp_link_url_details"),o=e("#dlp_file_size_input");switch(t){case"file":n.removeClass("active"),l.addClass("active"),o.prop("disabled",!0);break;case"url":n.addClass("active"),l.removeClass("active"),o.removeAttr("disabled");break;default:n.removeClass("active"),l.removeClass("active"),o.removeAttr("disabled")}},i.prototype.handleAddFile=function(t){t.preventDefault();const l=e(this),n=e("#dlp_file_name_input"),o=e(".dlp_file_name_text"),d=e("#dlp_file_id"),s=e("#dlp_file_attached");if(null===i.wpMedia)(i.wpMedia=wp.media({title:dlpAdminObject.i18n.select_file,button:{text:dlpAdminObject.i18n.add_file}})).on("select",(function(){i.wpMedia.state().get("selection").map((function(t){t=t.toJSON(),n.val(t.filename),o.text(t.filename),d.val(t.id),s.addClass("active");let a=dlpAdminObject.i18n.replace_file;if("keep"===dlpAdminObject.version_control_mode&&(a=dlpAdminObject.i18n.add_new_file),l.text(a),e("#dlp_file_attachment_details.version-control #dlp_version_history_file_toggle").show().removeClass("hidden"),0===e(`#dlp_version-${t.id}`).length){const l=wp.template("dlp-version-history-item"),n=wp.template("dlp-file-version-info"),o={attachment:t,href:"#dlp_version_history_list",target:"",history_type:"file"},d=e("<li>").html(l(o)).addClass("selected");e("dl.dlp_version_info",d).html(n(o)),"delete"===dlpAdminObject.version_control_mode&&e("#dlp_version_history_file_list ul li").remove(),e("#dlp_version_history_file_list ul").prepend(d),e("dlp_version_history_file").toggle(e("#dlp_version_history_file_list ul li").length>0),i.isDirty=!0}e('#dlp_version_history_list input[type="radio"]').prop("checked",!1),e(`#dlp_version-${t.id}`).trigger("click")}))})),i.prototype.cancelFileReplacement(n.val())||i.wpMedia.open();else{if(i.prototype.cancelFileReplacement(n.val()))return;i.wpMedia.open()}},i.prototype.toggleVersionHistory=function(i){i.preventDefault();const t=e(i.currentTarget).closest(".version-control").find(".dlp-version-history-list");t.hasClass("hidden")&&t.hide().removeClass("hidden");let l=dlpAdminObject.i18n.replace_file;t.is(":visible")||"keep"!==dlpAdminObject.version_control_mode||(l=dlpAdminObject.i18n.add_new_file),e("#dlp_add_file_button").text(l),t.slideToggle("fast")},i.prototype.selectHistoricalVersion=function(i){"A"===i.currentTarget.tagName&&i.preventDefault();const t=e(i.currentTarget).closest("li"),l=e('input[type="radio"]',t);l.length&&(e(".dlp-version-history-list li.selected").removeClass("selected"),t.addClass("selected"),l.prop("checked",!0),e("#dlp_file_attachment_details.active #dlp_file_name_input").val(l.data("filename")),e("#dlp_file_attachment_details.active #dlp_file_id").val(l.val()),e("#dlp_file_attachment_details.active span.dlp_file_name_text").text(l.data("filename")),e("#dlp_file_attachment_details.active #dlp_file_attached").addClass("active"),e("#dlp_link_url_details.active #dlp_direct_link_input").val(l.data("url")))},i.prototype.editVersionInfo=function(i){i.preventDefault();const t=e(i.currentTarget).closest("li").addClass("editing"),l=e('input[type="radio"]',t),n=e("input.version-input",t),o=e("input.size-input",t);n.val(l.data("version")),o.val(l.data("size")),t.addClass("editing")},i.prototype.exitVersionInfoEdit=function(t){t.preventDefault();const l=e(t.currentTarget).closest("li"),n=e('input[type="radio"]',l);if(t.data){let t=e("input.version-input",l).val();e("input.file-version",l).val(t),e("dl.dlp_version_info dd.link-version",l).text(t),n.data("version",t),t=e("input.version-input",l).val(),e("input.url-version",l).val(t),e("dl.dlp_version_info dd.link-version",l).text(t),n.data("size",t),t=e("input.size-input",l).val(),e("input.url-size",l).val(t),e("dl.dlp_version_info dd.link-size",l).text(t),n.data("size",t),i.isDirty=!0}l.removeClass("editing")},i.prototype.handleRemoveFile=function(t){t.preventDefault();const l=e("#dlp_file_name_input"),n=l.val(),o=e("#dlp_file_id"),d=o.val(),s=e("#dlp_file_attached"),a=e("#dlp_add_file_button");s.removeClass("active"),l.val(""),o.val(""),a.text(dlpAdminObject.i18n.add_file),e('#dlp_version_history_list input[type="radio"]').prop("checked",!1),e("#dlp_version_history_list li.selected").removeClass("selected"),e("#dlp_version_history_list").slideUp("fast"),"delete"===dlpAdminObject.version_control_mode&&(e("#dlp_version_history_file_toggle").toggle(!1),e("#dlp_version_history_list li").remove(),e("#dlp_version_history_list").removeClass("active")),i.isDirty=!0,!0===t.data&&(t.data={file_id:d,filename:n},i.prototype.removeVersion(t))},i.prototype.removeVersion=function(t){t.preventDefault();const l=e(t.currentTarget);let n=null,o="";if(l.hasClass("remove-version")?(n=l.closest("li"),o=e("a.filename",n).text().trim()):l.attr("id","dlp_remove_file_button")&&(n=e(`#dlp_version-${t.data.file_id}`).closest("li"),o=t.data.filename),!n)return;let d=!1;if(window.confirm(dlpAdminObject.i18n.shall_remove_version.replace("%s",o))){if(e("#dlp_version_history_file_list li").length<1&&(e("#dlp_version_history_file_list").slideUp("fast"),e("#dlp_version_history_file_toggle").hide(),d=!0),"delete"===dlpAdminObject.version_control_mode&&(d=!0),n.hasClass("selected")){n.remove();const i=e("#dlp_version_history_file_list li").first();if(i.length){const t=e('input[type="radio"]',i);t.prop("checked",!0),e("#dlp_file_name_input").val(t.data("filename")),e("#dlp_file_attached .dlp_file_name_text").text(t.data("filename")),e("#dlp_file_id").val(t.val())}}n.remove(),i.isDirty=!0,!0===t.data&&d&&(t.data=!1,i.prototype.handleRemoveFile(t))}},i.prototype.cancelFileReplacement=function(e){return e&&"delete"===dlpAdminObject.version_control_mode&&!window.confirm(dlpAdminObject.i18n.shall_remove_version.replace("%s",e))},new i}));