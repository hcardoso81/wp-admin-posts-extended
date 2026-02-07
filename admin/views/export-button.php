<?php
if (get_current_screen()->id !== 'edit-post') {
    return;
}
?>
<button
    type="submit"
    name="export_posts"
    value="1"
    class="button"
>
    Exportar
</button>
