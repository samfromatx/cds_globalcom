
$ = jQuery

$('.fields-sortable').sortable
    handle: '.menu-item-handle'
    connectWith: '.fields-sortable'

$('#post').submit ->
    selections = $('.selected .fields-sortable').sortable 'toArray',
        attribute: 'data-field-name'
    storage = $ 'input[name=eloqua-form-fields]'
    storage.val selections
