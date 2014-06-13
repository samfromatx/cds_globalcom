<?php
/**
 * Plugin Name: Eloqua Form
 * Description: Allows posts to be gated by an form that submits to Eloqua
 * Author: Alex Luke
 */

function eloqua_forms_add_support($post_type) {
    add_meta_box('eloqua-form', 'Eloqua Form Fields', 'eloqua_forms_fields_metabox', $post_type, 'normal');
    add_meta_box('eloqua-form-hidden', 'Eloqua Hidden Fields', 'eloqua_forms_hidden_metabox', $post_type, 'side');
}

function eloqua_forms_fields_metabox($post) {
    include 'fields.php';
    wp_enqueue_script('eloqua-forms-edit', plugins_url('javascripts/eloqua-forms-edit.js', __FILE__), array('jquery', 'jquery-ui-sortable'));
    wp_enqueue_style('eloqua-forms', plugins_url('stylesheets/eloqua-forms.css', __FILE__));
    $existingFieldsVal = get_post_meta($post->ID, 'eloqua-form-fields', true);
    $existingFields = explode(',', $existingFieldsVal);
    $existingFields = array_filter($existingFields);

    ?>
    <input type="hidden" name="eloqua-form-fields" value="<?php echo esc_attr($existingFieldsVal); ?>">
    <div class="available">
    <h2>Available fields</h2>
    <ul class="nav-menus-php fields-sortable">
        <?php foreach ($eloquaForm['fields'] as $fieldName => $field):
            if (in_array($fieldName, $existingFields))
                continue;
            ?>
            <li data-field-name="<?php echo esc_attr($fieldName); ?>">
                <dl class="menu-item-bar">
                    <dt class="menu-item-handle">
                        <span class="item-title"><?php echo esc_html($field['displayName']); ?></span>
                    </dt>
                </dl>
            </li>
        <?php endforeach; ?>
    </ul>
    </div>
    <div class="selected">
    <h2>Selected fields</h2>
    <ul class="nav-menus-php fields-sortable">
        <?php foreach ($existingFields as $fieldName):
            $field = $eloquaForm['fields'][$fieldName];
            ?>
            <li data-field-name="<?php echo esc_attr($fieldName); ?>">
                <dl class="menu-item-bar">
                    <dt class="menu-item-handle">
                        <span class="item-title"><?php echo esc_html($field['displayName']); ?></span>
                    </dt>
                </dl>
            </li>
        <?php endforeach; ?>
    </ul>
    </div>
    <?php
}

function eloqua_forms_hidden_metabox($post) {
    include 'fields.php';

    $adminFields = $eloquaForm['admin'];
    $adminValues = get_post_meta($post->ID, 'eloqua-form-hidden', true);

    if (!$adminValues)
        $adminValues = array_fill_keys(array_keys($adminFields), '');

    foreach ($adminValues as $name => $value):
        $field = $eloquaForm['admin'][$name];

        switch ($field['type']):
            case 'select': ?>
                <label for="<?php echo $name; ?>"><?php echo $field['displayName']; ?></label>
                <select  class="widefat" name="<?php echo $name; ?>">
                    <option value="" disabled selected>Select your option</option>
                    <?php foreach ($field['options'] as $name => $display): if (!$name) continue; 
                        if ($name == $value):
                        ?> <option value="<?php echo $name; ?>" selected><?php echo $display; ?></option>
                    <?php else : ?>                
                        <option value="<?php echo $name; ?>"><?php echo $display; ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            <?php break; default: ?>
                <label for="<?php echo $name; ?>"><?php echo $field['displayName']; ?></label>
                <?php if (!$value) : 
                    switch ($name): 
                        case 'elqSource': ?>
                            <input  class="widefat" type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" id="<?php echo  $name; ?>" value="<?php echo str_replace(" ","_",get_the_title()); ?>" />
                        <?php break; default: ?>
                            <input  class="widefat" type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" id="<?php echo  $name; ?>" value="<?php echo$field['displayValue']; ?>" />
                        <?php endswitch;
                else : ?>
                    <input  class="widefat" type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" id="<?php echo  $name; ?>" value="<?php echo $value; ?>" />
                <?php endif; ?>
        <?php endswitch;
    endforeach; 
}

function eloqua_forms_save_post($post_id) {
    include 'fields.php';
    $adminFields = $eloquaForm['admin'];

    if (isset($_POST['eloqua-form-fields']))
        update_post_meta($post_id, 'eloqua-form-fields', sanitize_text_field($_POST['eloqua-form-fields']));

    $hiddenFields = array();
    foreach (array_keys($adminFields) as $field)
        $hiddenFields[$field] = $_POST[$field];
    update_post_meta($post_id, 'eloqua-form-hidden', $hiddenFields);
}
add_action('save_post', 'eloqua_forms_save_post');

function eloqua_form($post_id = false) {
    include 'fields.php';

    if (!$post_id)
        $post_id = get_the_ID();
    $fieldNames = explode(',', get_post_meta($post_id, 'eloqua-form-fields', true));
    $fieldNames = array_filter($fieldNames);

    if (!$fieldNames)
        return;

    if (!in_array('emailAddress', $fieldNames))
        array_unshift($fieldNames, 'emailAddress');
    
    $adminFields = get_post_meta($post_id, 'eloqua-form-hidden', true);
    ?>
    
    <form method="post" action="<?php echo $eloquaForm['action']; ?>" class="eloqua-form" target="eloqua-submit" id="elqForm">
        <div>
            <label>Fill out the form to learn more...</label><br>
            <?php foreach ($fieldNames as $name):
                $field = $eloquaForm['fields'][$name];

                switch ($field['type']):
                    case 'select': ?>
                        <select name="<?php echo $name; ?>" required data-api-name="<?php echo $field['apiName']; ?>">
                            <option value=""><?php echo $field['displayName']; ?></option>
                            <?php foreach ($field['options'] as $name => $display): if (!$name) continue; ?>
                                <option value="<?php echo $name; ?>"><?php echo $display; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php break; default: ?>
                        <input type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" placeholder="<?php echo $field['displayName']; ?>" required data-api-name="<?php echo $field['apiName']; ?>">
                <?php endswitch;
            endforeach; ?>
        </div>
        <div>
            <?php foreach ($adminFields as $name => $value):
                // The standard Eloqua hidden fields may be overridden through the WP admin
                // Fall back to the default if not specified
                if (array_key_exists($name, $eloquaForm['hidden'])) {
                    if (!$value)
                        $value = $eloquaForm['hidden'][$name];
                    unset($eloquaForm['hidden'][$name]);
                } ?>
                <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
            <?php endforeach; ?>

            <?php 
            $qsArray = array(
                'elqCampaignName' => $_GET['cn'],
                'elqMed' => $_GET['med'],
                 ); ?>
            <?php foreach ($qsArray as $name => $qsarg): ?>
                <input type="hidden" name="<?php echo $name; ?>" data-qsarg="<?php echo $qsarg; ?>">
            <?php endforeach; ?>
            
            <?php foreach ($eloquaForm['hidden'] as $name => $value): ?>
                <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
            <?php endforeach; ?>

            <input type="hidden" name="elqCustomerGUID" value="">

            <input type="submit" value="Submit">
        </div>
    </form>
    <form  method="post" action="https://s1851.t.eloqua.com/e/f2" class="eloqua-form" target="eloqua-submit" name="elqForm2" id="elqForm2">
  <input type="hidden" name="emailAddress" placeholder="Email Address" required data-api-name="C_EmailAddress" value="">
  <input type="hidden" name="elqSource" value="White-Paper:-Meeting-Customer-Expectations:-Do-utility-billing-and-payment-capabilities-measure-up?">
<input type="hidden" name="LeadSource" value="Marketing_Campaign_White Paper">
<input type="hidden" name="LeadSourceName" value="CDS-Global.com">
<input type="hidden" name="LSMostRecent" value="Marketing_Campaign_White Paper">
<input type="hidden" name="LSNameMostRecent" value="CDS-Global.com">
  <input type="hidden" name="elqFormName" value="cds-global-resources" />
  <input type="hidden" name="elqSiteId" value="1851" />
  <input type="hidden" name="elqCustomerGUID" value="">
  <input type="hidden" name="elqCookieWrite" value="0">
  <input type="submit" value="Submit">
</form>
    <iframe name="eloqua-submit" style="display: none"></iframe>
    <?php
}
