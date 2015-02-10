<!DOCTYPE html>
<html>
<head>
	<title>Stripe Demo</title>
</head>
<body>
<h3>Sign Up</h3>
<form action="" method="POST">
  <script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key="pk_test_IMGNNIQc4kJnUibHvSdsXkFN"
    data-amount="{{ $service->price }}"
    data-name="{{ $service->name }}"
    data-description="{{ $service->description }} (${{ $service->price / 100 }})"
    data-image="/128x128.png">
  </script>
</form>
</body>
</html>