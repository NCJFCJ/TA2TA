var idt = '';
function show_org_details(id){
    idt = id;
    document.getElementById('org_details_'+id).classList.add('show');
    document.getElementById('org_overlay').style.display='block';
    document.getElementById("org_overlay").addEventListener("click", org_overlay_off);
}
function hide_org_details(id){
    document.getElementById('org_details_'+id).classList.remove('show');
    document.getElementById('org_overlay').style.display='none';
}

function org_overlay_off() {
    document.getElementById("org_overlay").style.display = "none";
    document.getElementById('org_details_'+idt).classList.remove('show');
}

/* Accrodeon project page */

function accordion_project_toggle_me(me){
  me.classList.toggle("active");
  var panel = me.nextElementSibling;
  if (panel.style.display === "block") {
    panel.style.display = "none";
  } else {
    panel.style.display = "block";
  }
}

// function grab_selected_event_id(selected_id){

//     alert(document.URL + 'edit-event/?post_id=' + selected_id);

//     //document.getElementById('selected_event_to_edit').innerHTML = event_name;
// }