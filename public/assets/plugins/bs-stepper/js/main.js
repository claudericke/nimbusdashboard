var stepper1; // Declare stepper1 globally
// Removed stepper2, stepper3, stepper4 as they are not in the provided HTML

document.addEventListener('DOMContentLoaded', function () {
    // Initialize stepper1, which is the main stepper in your HTML
    stepper1 = new Stepper(document.querySelector('#stepper1'));

    // stepperForm is now just a reference to stepper1
    var stepperFormEl = document.querySelector('#stepper1');
    var stepperForm = stepper1; // Use stepper1 as the main stepper form

    var btnNextList = [].slice.call(document.querySelectorAll('.btn-next-form'));
    var stepperPanList = [].slice.call(stepperFormEl.querySelectorAll('.bs-stepper-pane'));

    // Get references to the specific input elements for validation
    var inputFirstName = document.getElementById('inputFirstName');
    var inputAccountPassword = document.getElementById('inputAccountPassword'); // Corrected ID

    var form = stepperFormEl.querySelector('form'); // Get the form element within the stepper

    // Attach event listeners to all "Next" buttons that have the class 'btn-next-form'
    // Note: Your HTML uses inline onclick="stepper1.next()", so this block might be redundant
    // if you are relying solely on the inline clicks. If you want to use this, ensure
    // your "Next" buttons have the class "btn-next-form".
    btnNextList.forEach(function (btn) {
        btn.addEventListener('click', function () {
            stepperForm.next();
        });
    });

    // Event listener for when a stepper pane is about to be shown
    stepperFormEl.addEventListener('show.bs-stepper', function (event) {
        form.classList.remove('was-validated'); // Remove validation state from the form

        var nextStep = event.detail.indexStep;
        var currentStep = nextStep;

        if (currentStep > 0) {
            currentStep--; // Adjust to get the *current* pane before moving to the next
        }

        var stepperPan = stepperPanList[currentStep]; // Get the current pane element

        // Validation logic:
        // If the current pane is 'test-l-1' (Profile Information) and inputFirstName is empty, prevent navigation
        if (stepperPan.getAttribute('id') === 'test-l-1' && inputFirstName && !inputFirstName.value.length) {
            event.preventDefault(); // Stop the stepper from moving to the next step
            form.classList.add('was-validated'); // Add Bootstrap's validation class
        }
        // If the current pane is 'test-l-2' (Account Details) and inputAccountPassword is empty, prevent navigation
        else if (stepperPan.getAttribute('id') === 'test-l-2' && inputAccountPassword && !inputAccountPassword.value.length) {
            event.preventDefault(); // Stop the stepper from moving to the next step
            form.classList.add('was-validated'); // Add Bootstrap's validation class
        }
    });
});
