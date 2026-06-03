(function ($) {
  "use strict";

  function responseBox($form) {
    var $box = $form.find(".form-response").first();

    if (!$box.length) {
      $box = $form.closest(".widget-contact-form").find(".email_server_responce").first();
    }

    if (!$box.length) {
      $box = $('<div class="form-response"></div>');
      $form.prepend($box);
    }

    return $box;
  }

  function showMessage($form, success, message) {
    var $box = responseBox($form);
    var state = success ? "success" : "error";

    $box
      .removeClass("success error")
      .addClass("is-visible " + state)
      .text(message || (success ? "Submitted successfully." : "Please try again."));
  }

  function submitAjax(event) {
    var form = event.currentTarget;
    var $form = $(form);

    if ($form.data("ajax") !== true && $form.attr("data-ajax") !== "true") {
      return;
    }

    event.preventDefault();

    var $submit = $form.find('[type="submit"]').first();
    var submitText = $submit.is("input") ? $submit.val() : $submit.text();

    $form.addClass("is-submitting");
    $submit.prop("disabled", true);

    if ($submit.is("input")) {
      $submit.val("Sending...");
    } else {
      $submit.text("Sending...");
    }

    $.ajax({
      url: $form.attr("action"),
      method: ($form.attr("method") || "POST").toUpperCase(),
      data: new FormData(form),
      processData: false,
      contentType: false,
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest"
      }
    })
      .done(function (data) {
        var success = data && data.success !== false;
        showMessage($form, success, data && data.message);

        if (success) {
          form.reset();
          $form.find("select.select2").trigger("change");
        }
      })
      .fail(function (xhr) {
        var data = xhr.responseJSON || {};
        showMessage($form, false, data.message || "Submission failed. Please try again.");
      })
      .always(function () {
        $form.removeClass("is-submitting");
        $submit.prop("disabled", false);

        if ($submit.is("input")) {
          $submit.val(submitText);
        } else {
          $submit.text(submitText);
        }
      });
  }

  $(function () {
    $(document).on("submit", 'form[data-ajax="true"]', submitAjax);
  });
})(jQuery);
