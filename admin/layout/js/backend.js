$(function () {
  "use strict";
  $("select").selectBoxIt({
    autoWidth: false,
    defaultText: "must select option",
  });
  $("[placeholder]")
    .focus(function () {
      $(this).attr("data-text", $(this).attr("placeholder"));
      $(this).attr("placeholder", "");
    })
    .blur(function () {
      $(this).attr("placeholder", $(this).attr("data-text"));
    });
  $('[data-toggle="tooltip"]').tooltip();
});

var passField = $(".password");
var icon = $(".show-pass");
$(".show-pass").hover(
  function () {
    passField.attr("type", "text");
    icon.removeClass("fa-eye-slash").addClass("fa-eye");
  },
  function () {
    passField.attr("type", "password");
    icon.removeClass("fa-eye").addClass("fa-eye-slash");
  }
);

$(".confirm").click(function () {
  return confirm("Are you sure?");
});

// category show and hide
