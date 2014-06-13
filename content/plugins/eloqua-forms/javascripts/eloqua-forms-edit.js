
(function() {
  var $;

  $ = jQuery;

  $('.fields-sortable').sortable({
    handle: '.menu-item-handle',
    connectWith: '.fields-sortable'
  });

  $('#post').submit(function() {
    var selections, storage, selections2, storage2, selections3, storage3;
    selections = $('.selected .first-time-visitor').sortable('toArray', {
      attribute: 'data-field-name'
    });
    storage = $('input[name=eloqua-form-fields]');

    selections2 = $('.selected .progressive-level-one').sortable('toArray', {
      attribute: 'data-field-name'
    });
    storage2 = $('input[name=eloqua-form-fields-prog-1]');
    

    selections3 = $('.selected .progressive-level-two').sortable('toArray', {
      attribute: 'data-field-name'
    });
    storage3 = $('input[name=eloqua-form-fields-prog-2]'); 

    return [storage.val(selections), storage2.val(selections2), storage3.val(selections3)];
  });

}).call(this);