<script id="coverimage-form-template" type="text/templates">

  <div class="control-group">

    <?php echo common('neatline/input', array(
        'name'  => 'coverimage-file-id',
        'label' => 'Cover Image File ID',
        'bind'  => 'exhibit:cover_image_file_id',
        'class' => 'integer',
    )); ?>

  </div>

  <?php echo common('neatline/save'); ?>

</script>
