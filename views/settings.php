<div class="wrap">
    <?php if (get_transient('es_objects_message')) { ?>
        <div class="updated">
            <p><?php echo get_transient('es_objects_message'); ?></p>
        </div>
        <?php delete_transient('es_objects_message'); ?>
    <?php } ?>

    <h2>The Museum Platform Collections Configuration</h2>
    <?php $plugin_data = get_plugin_data(__DIR__ . '/../tmp-remote-collections.php'); ?>

    <form method="POST" action="options.php">
        <?php
        settings_fields('tmp_remote_collections_settings');
        do_settings_sections('tmp_remote_collections_settings');
        submit_button();
        ?>
    </form>

</div>