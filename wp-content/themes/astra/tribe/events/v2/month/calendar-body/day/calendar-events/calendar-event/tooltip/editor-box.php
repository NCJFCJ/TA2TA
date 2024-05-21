
<?php 






?>

<div class="row-actions event-editor-links">
    <span class="edit">
        <a href="/events/ta2ta-providers/edit/event/<?php echo $event->ID;?>/" aria-label="Edit “<?php echo $event->title; ?>”">
            Edit
        </a>
        | 
    </span>
    <span class="view">
        <a href="<?php echo esc_url( $event->permalink ); ?>" rel="bookmark" aria-label="View “<?php echo $event->title; ?>”">
           View
        </a>
        |
    </span>
    <span class="archive_link">
        <a href="/events/ta2ta-providers/delete/event/<?php echo $event->ID;?>/" aria-label="archive" title="Archive this post">
            Archive
        </a>
    </span>
</div>