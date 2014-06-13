<ul class="breadcrumbs">
    <?php foreach(cds_breadcrumbs() as $permalink => $title): ?>
        <li><a href="<?php print $permalink; ?>"><?php print $title; ?></a></li>
    <?php endforeach; ?>
</ul>
