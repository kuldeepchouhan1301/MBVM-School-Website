(function ($) {
  "use strict";

  // Create or get success modal popup
  function getSuccessModal() {
    var $modal = $("#contactSuccessModal");
    if (!$modal.length) {
      console.log("Creating #contactSuccessModal element in DOM");
      $modal = $(
        '<div id="contactSuccessModal" class="mbvm-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="contactSuccessTitle">' +
          '<div class="mbvm-modal-card">' +
            '<div class="mbvm-modal-icon">' +
              '<i class="fa fa-check-circle"></i>' +
            '</div>' +
            '<h3 id="contactSuccessTitle" class="mbvm-modal-title">Message Sent Successfully!</h3>' +
            '<p class="mbvm-modal-sub">Your message has been sent successfully to the school office.</p>' +
            '<p class="mbvm-modal-msg">Thank you for contacting Madhusudan Bal Vidya Mandir. We have received your message and will get back to you soon.</p>' +
            '<button type="button" class="mbvm-modal-btn" id="contactSuccessOkBtn">OK</button>' +
          '</div>' +
        '</div>'
      );
      $("body").append($modal);

      // Close handlers
      $modal.find("#contactSuccessOkBtn").on("click", function () {
        closeModal($modal);
      });

      $modal.on("click", function (e) {
        if ($(e.target).hasClass("mbvm-modal-overlay")) {
          closeModal($modal);
        }
      });

      $(document).on("keydown", function (e) {
        if (e.key === "Escape" && $modal.hasClass("is-active")) {
          closeModal($modal);
        }
      });
    }
    return $modal;
  }

  function showSuccessModal(message) {
    console.log("showSuccessModal called with message:", message);
    var $modal = getSuccessModal();
    if (message) {
      $modal.find(".mbvm-modal-msg").text(message);
    }
    $modal.addClass("is-active");
    $("body").css("overflow", "hidden");
    $modal.find(".mbvm-modal-btn").focus();
  }

  function closeModal($modal) {
    $modal.removeClass("is-active");
    $("body").css("overflow", "");
  }

  function getResponseBox($form) {
    var $box = $form.find(".contact-form-response").first();
    if (!$box.length) {
      $box = $form.prev(".email_server_responce").first();
    }
    if (!$box.length) {
      $box = $form.find(".email_server_responce").first();
    }
    if (!$box.length) {
      $box = $('<div class="contact-form-response"></div>');
      $form.prepend($box);
    }
    return $box;
  }

  function showErrorMsg($form, message) {
    var $box = getResponseBox($form);
    $box
      .removeClass("is-success")
      .addClass("is-error contact-form-response")
      .html('<i class="fa fa-exclamation-circle"></i> ' + (message || "Unable to send your message right now. Please try again."))
      .show();
  }

  function clearErrorMsg($form) {
    var $box = getResponseBox($form);
    $box.removeClass("is-error").hide().empty();
  }

  function submitAjax(event) {
    console.log("submitAjax triggered on form!");
    var form = event.currentTarget || event.target;
    var $form = $(form);

    event.preventDefault();
    event.stopImmediatePropagation();
    clearErrorMsg($form);

    var $submit = $form.find('[type="submit"]').first();
    var originalHtml = $submit.html();

    $form.addClass("is-submitting");
    $submit.prop("disabled", true);
    $submit.html('<i class="fa fa-spinner fa-spin"></i> Sending...');

    var formData = new FormData(form);
    formData.append("ajax", "1");

    $.ajax({
      url: $form.attr("action"),
      method: ($form.attr("method") || "POST").toUpperCase(),
      data: formData,
      dataType: "json",
      processData: false,
      contentType: false,
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest"
      }
    })
      .done(function (data) {
        console.log("AJAX done received data:", data);
        if (typeof data === "string") {
          try {
            data = JSON.parse(data);
          } catch (e) {}
        }
        if (data && (data.success === true || data.status === "success" || data.status === 1 || data.success !== false)) {
          // SUCCESS PATH: Reset form and show popup modal
          form.reset();
          var successMsg = (data && data.message) ? data.message : "We have received your message and will get back to you soon.";
          showSuccessModal(successMsg);
        } else {
          // FAILURE PATH: DO NOT RESET FORM, KEEP USER DATA, SHOW ERROR
          var errorText = (data && data.message) || "Unable to send your message right now. Please try again.";
          showErrorMsg($form, errorText);
        }
      })
      .fail(function (xhr) {
        console.log("AJAX fail received xhr:", xhr.status, xhr.responseText);
        var data = xhr.responseJSON || {};
        if (typeof data === "string") {
          try { data = JSON.parse(data); } catch(e){}
        }
        var errorText = data.message || "Unable to send your message right now. Please try again.";
        showErrorMsg($form, errorText);
      })
      .always(function () {
        $form.removeClass("is-submitting");
        $submit.prop("disabled", false);
        $submit.html(originalHtml);
      });
  }

  console.log("Binding submitAjax to contact forms immediately...");
  $(document).on("submit", 'form[data-ajax="true"], form.contact-form', submitAjax);
})(jQuery);
