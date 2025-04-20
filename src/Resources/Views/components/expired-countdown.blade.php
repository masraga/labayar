<script>
  // Set the date we're counting down to
  var countDownDate = new Date("{!! $payment['expired_at'] !!}").getTime();

  // Update the count down every 1 second
  var x = setInterval(function() {

    // Get today's date and time
    var now = new Date().getTime();

    // Find the distance between now and the count down date
    var distance = countDownDate - now;

    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Display the result in the element with id="demo"
    document.getElementById("expired-time").innerHTML = "Expired in " + days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

    // If the count down is finished, write some text
    if (distance < 0) {
      clearInterval(x);
      const submitButton = document.getElementById("button-submit");
      document.getElementById("expired-time").innerHTML = `<div class="text-sm text-red-600 text-center">Expired</div>`;
      submitButton.classList.remove("cursor-pointer");
      submitButton.classList.remove("bg-gray-800");
      submitButton.classList.remove("hover:bg-gray-900");
      submitButton.classList.add("cursor-not-allowed");
      submitButton.classList.add("bg-gray-400");
    }
  }, 1000);
</script>