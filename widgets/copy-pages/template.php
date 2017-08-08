<div class="copy-pages-widget">

<?php
  $options = array();
  foreach(site()->index() as $p) {
    if ($p->title() == "_modules" OR str::contains($p->intendedTemplate(), 'module.')) continue;
    $options[$p->uri()] = $p->title();
  }
  $firstUid = key($options);
  
  $optionsPlusSite = array();
  $optionsPlusSite['/'] = site()->title();
  $optionsPlusSite = array_merge($optionsPlusSite, $options);
  
  $form = new Kirby\Panel\Form([
    'source' => [
      'type' => 'select',
      'options' => $options,
      'required' => true,
      'label' => 'Zu kopierende Seite',
      'placeholder' => 'Seite auswählen...'
    ],
    'dest' => [
      'type' => 'select',
      'options' => $optionsPlusSite,
      'required' => true,
      'label' => 'Kopieren nach',
      'placeholder' => 'Seite auswählen...'
    ],
    'uid' => [
      'type' => 'text',
      'label' => 'Neuer URL-Anhang (optional)',
      'placeholder' => $firstUid
    ]
  ], []);
  $form->on('post', function() {}); // append csrf
  $form->action('copy-pages/api/copy');
  $form->attr('data-autosubmit', 'native');
  $form->buttons->submit->val('Seite kopieren');
  $form->buttons->cancel = '';
  echo $form;
?>
</div>

<script>
  
  $('.copy-pages-widget #form-field-source').on("change", function(evt) {
    uid = $(this).find("option:selected").val().split("/").pop();    
    $('.copy-pages-widget #form-field-uid').attr("placeholder", uid);
  });
  
  $('.copy-pages-widget form').submit(function(evt) {
    evt.preventDefault()
    var $form = $(this)
    var $buttons = $form.find('.fieldset.buttons')
    $buttons.find('.outcome').remove()
    function showMessage(msg, success) {
      var $msg = $('<p class="outcome" />').text(msg)
      $msg.css({color: success ? '#8dae28' : '#b3000a', marginBottom: '1.5em'})
      $msg.prependTo($buttons)
    }
    $.ajax($form.attr('action'), {
      method: ($form.attr('method') || 'post').toLowerCase(),
      data: $form.serialize(),
      success: function(data) {
        if (data && data.data && data.data.url) {
          window.location.href = data.data.url
        } else {
          $form[0].reset()
          $form.find('input[name="source"]').focus()
          showMessage(data.message, true)
        }
      },
      error: function(res) {
        showMessage(res.responseJSON ? res.responseJSON.message : res.responseText, false)
      }
    })
  })
</script>

