  // Ensure the DOM is fully loaded before running the script
        $(document).ready(function() {
            /**
             * @function updateTime
             * @description Updates the text content of the element with id 'time'
             * to display the current local time.
             */
            function updateTime() {
                const now = new Date();
                // Format the time string based on local conventions
                const timeString = now.toLocaleTimeString();
                // Use jQuery to select the element by ID and update its text content
                $('#time').text(timeString);
            }

            // Call updateTime immediately to display the time as soon as the page loads
            updateTime();

            // Set an interval to call updateTime every 1000 milliseconds (1 second)
            setInterval(updateTime, 1000);
        });

$(function () {
  "use strict";


  /* scrollar */

  new PerfectScrollbar(".notify-list")


  // new PerfectScrollbar(".mega-menu-widgets")



  /* toggle button */

  $(".btn-toggle").click(function () {
    $("body").hasClass("toggled") ? ($("body").removeClass("toggled"), $(".sidebar-wrapper").unbind("hover")) : ($("body").addClass("toggled"), $(".sidebar-wrapper").hover(function () {
      $("body").addClass("sidebar-hovered")
    }, function () {
      $("body").removeClass("sidebar-hovered")
    }))
  })





  /* menu */

  $(function () {
    $('#sidenav').metisMenu();
  });

  $(".sidebar-close").on("click", function () {
    $("body").removeClass("toggled")
  })



  /* dark mode button */

  $(".dark-mode i").click(function () {
    $(this).text(function (i, v) {
      return v === 'dark_mode' ? 'light_mode' : 'dark_mode'
    })
  });


  $(".dark-mode").click(function () {
    $("html").attr("data-bs-theme", function (i, v) {
      return v === 'dark' ? 'light' : 'dark';
    })
  })


  /* sticky header */

  $(document).ready(function () {
    $(window).on("scroll", function () {
      if ($(this).scrollTop() > 60) {
        $('.top-header .navbar').addClass('sticky-header');
      } else {
        $('.top-header .navbar').removeClass('sticky-header');
      }
    });
  });


  /* email */

  $(".email-toggle-btn").on("click", function() {
    $(".email-wrapper").toggleClass("email-toggled")
  }), $(".email-toggle-btn-mobile").on("click", function() {
    $(".email-wrapper").removeClass("email-toggled")
  }), $(".compose-mail-btn").on("click", function() {
    $(".compose-mail-popup").show()
  }), $(".compose-mail-close").on("click", function() {
    $(".compose-mail-popup").hide()
  }), 


  /* chat */

  $(".chat-toggle-btn").on("click", function() {
    $(".chat-wrapper").toggleClass("chat-toggled")
  }), $(".chat-toggle-btn-mobile").on("click", function() {
    $(".chat-wrapper").removeClass("chat-toggled")
  }),



  // Wizard theme selector
    // Select the theme picker dropdown by its ID
    $("#themePicker").on("change", function () {
        // Get the selected value from the dropdown
        var selectedTheme = $(this).val();

        // Set the 'data-bs-theme' attribute of the <html> tag to the selected theme
        $("html").attr("data-bs-theme", selectedTheme);

        // Optional: You might want to save the user's preference to localStorage
         localStorage.setItem("selectedTheme", selectedTheme);
    });

// Optional: Load the saved theme preference when the page loads
  var savedTheme = localStorage.getItem("selectedTheme");
  if (savedTheme) {
      $("html").attr("data-bs-theme", savedTheme);
      $("#themePicker").val(savedTheme); // Set the dropdown to the saved theme
  }



  /* search control */

  $(".search-control").click(function () {
    $(".search-popup").addClass("d-block");
    $(".search-close").addClass("d-block");
  });


  $(".search-close").click(function () {
    $(".search-popup").removeClass("d-block");
    $(".search-close").removeClass("d-block");
  });


  $(".mobile-search-btn").click(function () {
    $(".search-popup").addClass("d-block");
  });


  $(".mobile-search-close").click(function () {
    $(".search-popup").removeClass("d-block");
  });




  /* menu active */

  $(function () {
    for (var e = window.location, o = $(".metismenu li a").filter(function () {
      return this.href == e
    }).addClass("").parent().addClass("mm-active"); o.is("li");) o = o.parent("").addClass("mm-show").parent("").addClass("mm-active")
  });



});










