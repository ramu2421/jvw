<?php
require_once '../includes/db.php';
require_once '../includes/session.php';

if (!isset($_SESSION['job_id'])) {
    header("Location: create-job.php");
    exit;
}

$jobId = $_SESSION['job_id'];
require_once '../vendor/autoload.php';

\Stripe\Stripe::setApiKey('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'); // Replace with your live key when live

$setupIntent = \Stripe\SetupIntent::create();

?>

<?php include '../templates/header.php'; ?>
<div class="container my-5">
    <h2>Save Card to Post Job</h2>
    <form id="payment-form">
        <div id="card-element" class="form-control mb-3"></div>
        <button id="submit" class="btn btn-primary w-100">Save Card</button>
    </form>
    <div id="card-errors" class="text-danger mt-2"></div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('pk_live_51RD7uuFZRa6Rf08cg427bU9R5ixEHS9DvytetMoQ1w6MZzvC6J8nK18DsPI3gVLYRQ3HOBoHByrFNl0IRxc426lH006eGWJg2B'); // Replace with your live key
const elements = stripe.elements();
const card = elements.create('card');
card.mount('#card-element');

const form = document.getElementById('payment-form');
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const { setupIntent, error } = await stripe.confirmCardSetup("<?= $setupIntent->client_secret ?>", {
        payment_method: { card: card }
    });

    if (error) {
        document.getElementById('card-errors').textContent = error.message;
    } else {
        // Send payment method to server
        fetch('save-payment-method.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ payment_method: setupIntent.payment_method })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Card saved! Job sent for review.");
                window.location.href = "dashboard.php";
            } else {
                alert("Something went wrong.");
            }
        });
    }
});
</script>
<?php include '../templates/footer.php'; ?>