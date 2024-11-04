jQuery(document).ready(function ($) {
  $(document).on("click", ".notice.is-dismissible", function () {
    $.post(ajaxurl, { action: "clear_new_user_activations" });
  });
});

jQuery(document).ready(function ($) {
  $(".membership-dropdown").change(function () {
    var userId = $(this).data("user-id");
    var membershipId = $(this).val();

    var actionMessage =
      membershipId === "cancel"
        ? "Are you sure you want to cancel this membership?"
        : "Are you sure you want to assign this membership?";

    console.log(actionMessage);

    if (!confirm(actionMessage)) {
      // Reset the dropdown if the admin cancels the confirmation
      $(this).val(membershipId === "cancel" ? "" : "cancel");
      return;
    }

    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "assign_membership",
        user_id: userId,
        membership_id: membershipId,
        nonce: ajax_object.nonce,
      },
      success: function (response) {
        alert(response.data.message);
        setTimeout(reload, 1000);
      },
      error: function () {
        alert("Error assigning or canceling membership");
      },
    });
  });

  function reload() {
    document.location.reload();
  }
});
