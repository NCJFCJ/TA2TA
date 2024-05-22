<?php
/**
 * Title: Edit Document
 * Slug: twentytwentyfour/edit-document
 * Keywords: document, edit
 * Categories: service
 */

if (isset($_GET['action']) && isset($_GET['post_id'])) {
    if($_GET['action'] === 'update-document'){
        $post_id = $_GET['post_id']; 
        $post = get_post( $post_id, OBJECT, 'row' );
        acfe_form(['id' => 'edit-codument-library-resource','post_id' => $post_id]);
    } else {
        echo 'ECHO => Create new document form';
    }
}

?>

<!-- <script type="text/javascript">
    var selected_grant_project_grant_programs = <?php //echo json_encode(array_shift($selected_grant_programs)); ?>
</script>
<script>
    var grant_project_grant_programs = "<?php //echo json_encode(organization_user_grant_project_grant_programs('','grant_programs')); ?>"
</script> -->