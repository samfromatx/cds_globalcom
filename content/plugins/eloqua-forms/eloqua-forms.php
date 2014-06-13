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

    $progressive1Val = get_post_meta($post->ID, 'eloqua-form-fields-prog-1', true);
    $progressive1 = explode(',', $progressive1Val);
    $progressive1 = array_filter($progressive1);

    $progressive2Val = get_post_meta($post->ID, 'eloqua-form-fields-prog-2', true);
    $progressive2 = explode(',', $progressive2Val);
    $progressive2 = array_filter($progressive2);

    ?>
    <input type="hidden" name="eloqua-form-fields" value="<?php echo esc_attr($existingFieldsVal); ?>">
    <input type="hidden" name="eloqua-form-fields-prog-1" value="<?php echo esc_attr($progressive1Val); ?>">
    <input type="hidden" name="eloqua-form-fields-prog-2" value="<?php echo esc_attr($progressive2Val); ?>">
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
    <h2>Selected fields - First Time Visitor</h2>
    <ul class="nav-menus-php fields-sortable first-time-visitor">
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
    <h2>Progressive Fields - Level 1</h2>
    <ul class="nav-menus-php fields-sortable progressive-level-one">
        <?php foreach ($progressive1 as $fieldName2):
            $field2 = $eloquaForm['fields'][$fieldName2];
            ?>
            <li data-field-name="<?php echo esc_attr($fieldName2); ?>">
                <dl class="menu-item-bar">
                    <dt class="menu-item-handle">
                        <span class="item-title"><?php echo esc_html($field2['displayName']); ?></span>
                    </dt>
                </dl>
            </li>
        <?php endforeach; ?>
    </ul>
    <h2>Progressive Fields - Level 2</h2>
    <ul class="nav-menus-php fields-sortable progressive-level-two">
        <?php foreach ($progressive2 as $fieldName3):
            $field3 = $eloquaForm['fields'][$fieldName3];
            ?>
            <li data-field-name="<?php echo esc_attr($fieldName3); ?>">
                <dl class="menu-item-bar">
                    <dt class="menu-item-handle">
                        <span class="item-title"><?php echo esc_html($field3['displayName']); ?></span>
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

    if (isset($_POST['eloqua-form-fields-prog-1']))
        update_post_meta($post_id, 'eloqua-form-fields-prog-1', sanitize_text_field($_POST['eloqua-form-fields-prog-1']));

    if (isset($_POST['eloqua-form-fields-prog-2']))
        update_post_meta($post_id, 'eloqua-form-fields-prog-2', sanitize_text_field($_POST['eloqua-form-fields-prog-2']));

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

    $fieldNamesProg1 = explode(',', get_post_meta($post_id, 'eloqua-form-fields-prog-1', true));
    $fieldNamesProg1 = array_filter($fieldNamesProg1);

    $fieldNamesProg2 = explode(',', get_post_meta($post_id, 'eloqua-form-fields-prog-2', true));
    $fieldNamesProg2 = array_filter($fieldNamesProg2);
?>
<script type="text/javascript">
/*
$(document).ready(function() {
    elqVEmail = localStorage.getItem("elqVEmail");
    elqProg2 = localStorage.getItem("elqProg2");
    if ((elqVEmail == "" || elqVEmail == null ) && ( elqProg2 == "false" || elqProg2 == "" || elqProg2 == null )) {
        $('#prog1').hide();
        $('#prog1 input, #prog1 select').each(function() {
            $(this).removeAttr('required');
        });
        $('#prog2').hide();
        $('#prog2 input, #prog2 select').each(function() {
            $(this).removeAttr('required');
        });
    } else if (elqVEmail != "" && elqProg2 == "false") {
        $('#prog2').hide();
        $("#prog2 input, #prog2 select, input[name='emailAddress']").each(function() {
            $(this).removeAttr('required');
        });
        console.log("if 2");
    } else if (elqVEmail != "" && elqProg2 == "true") {
        $('#prog1').hide();
        $("#prog1 input, #prog1 select, input[name='emailAddress']").each(function() {
            $(this).removeAttr('required');
        });
        console.log("if 3");
    }

    $( "#elqForm" ).submit(function( event ) {
        if (elqVEmail != "" && elqProg2 == "false") {
            localStorage.setItem("elqProg2", "true");
        }
    });
});
*/
</script>
<?php

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

        <div id="prog1">
            <?php foreach ($fieldNamesProg1 as $name2):
                $field2 = $eloquaForm['fields'][$name2];

                switch ($field2['type']):
                    case 'select': ?>
                        <select name="<?php echo $name2; ?>" required data-api-name="<?php echo $field2['apiName']; ?>">
                            <option value=""><?php echo $field2['displayName']; ?></option>
                            <?php foreach ($field2['options'] as $name2 => $display): if (!$name2) continue; ?>
                                <option value="<?php echo $name2; ?>"><?php echo $display; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php break; default: ?>
                        <input type="<?php echo $field2['type']; ?>" class="prog1-field" name="<?php echo $name2; ?>" placeholder="<?php echo $field2['displayName']; ?>" required data-api-name="<?php echo $field2['apiName']; ?>">
                <?php endswitch;
            endforeach; ?>
        </div>

        <div id="prog2">
            <?php foreach ($fieldNamesProg2 as $name3):
                $field3 = $eloquaForm['fields'][$name3];

                switch ($field3['type']):
                    case 'select': ?>
                        <select name="<?php echo $name3; ?>" required data-api-name="<?php echo $field3['apiName']; ?>">
                            <option value=""><?php echo $field3['displayName']; ?></option>
                            <?php foreach ($field3['options'] as $name3 => $display): if (!$name3) continue; ?>
                                <option value="<?php echo $name3; ?>"><?php echo $display; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php break; default: ?>
                        <input type="<?php echo $field3['type']; ?>" class="prog1-field" name="<?php echo $name3; ?>" placeholder="<?php echo $field3['displayName']; ?>" required data-api-name="<?php echo $field3['apiName']; ?>">
                <?php endswitch;
            endforeach; ?>
        </div>
        <div id="CA" class="formtext" style="display:none;">
        	<input type="hidden" name="PermissionDate" value="<?php echo date("m/d/Y"); ?>" />
        	<input type="checkbox" name="OptInCheckbox">&nbsp;&nbsp;By checking this box, I authorize CDS Global to contact me via the email address supplied about CDS Global its products and services, including product releases, updates, seminars, events, surveys, trainings and special offers.</div>
        <div id="GB" class="formtext" style="display:none;">
        	<input type="checkbox" name="OptInCheckbox"> &nbsp;&nbsp;By checking this box, I authorize CDS Global to contact me via the email address supplied about CDS Global its products and services, including product releases, updates, seminars, events, surveys, trainings and special offers.  I also authorize CDS Global to store cookies on my browser.
        </div>

        <script>
        /*
        $(document).ready(function() {
	        $('select[name="country"]').change(function() {
	        	if ($(this).val() == 'CA') {
		        	$('#GB').hide();
		        	$('#GB input').prop('required', false);
		        	$('#CA').show();
					$('#CA input').prop('required', true);
	        	} else {
		        	$('#CA').hide();
				    $('#GB').hide();
				    $('#CA input').prop('required', false);
				    $('#GB input').prop('required', false);
	        	}
			});
		});
		*/
        </script>

        </div>
        <div>
            <?php foreach ($adminFields as $name => $value):
                // The standard Eloqua hidden fields may be overridden through the WP admin
                // Fall back to the default if not specified
                if (array_key_exists($name, $eloquaForm['hidden'])) {
                    if (!$value)
                        $value = $eloquaForm['hidden'][$name];
                    unset($eloquaForm['hidden'][$name]);
                }

                // check for utm_campaign query string and if present overwrite LeadSourceName and LSNameMostRecent
                if ($name == "LeadSourceName" || $name == "LSNameMostRecent") {
                    if ($_GET['utm_campaign']) {
                        $value = $_GET['utm_campaign'];
                    } elseif ($_COOKIE["utmcampaign"]) {
                        $value = $_COOKIE["utmcampaign"];
                    }
                }
                 ?>
                <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
            <?php endforeach; ?>

            <?php
            if ($_GET['cn']) {
                $elqCN = $_GET['cn'];
            } elseif ($_COOKIE["utmsource"]) {
                $elqCN = $_COOKIE["utmsource"];
            }
            $qsArray = array(
                'elqCampaignName' => $elqCN,
                'elqMed' => $_GET['utm_medium'],
                 ); ?>
            <?php foreach ($qsArray as $name => $qsarg): ?>
                <input type="hidden" name="<?php echo $name; ?>" data-qsarg="<?php echo $qsarg; ?>" value="<?php echo $qsarg; ?>">
            <?php endforeach; ?>

            <?php foreach ($eloquaForm['hidden'] as $name => $value): ?>
                <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
            <?php endforeach; ?>

            <input type="hidden" name="elqCustomerGUID" value="">

            <input type="submit" value="Submit">
        </div>
    </form>
    <iframe name="eloqua-submit" style="display: none"></iframe>
    <?php
}
