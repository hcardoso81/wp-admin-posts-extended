<?php
if (get_current_screen()->id !== 'edit-post') {
    return;
}
?>
<button
    type="submit"
    name="export_posts"
    value="1"
    id="wpape-export-posts"
    class="button"
>
    Exportar EXCEL
</button>
