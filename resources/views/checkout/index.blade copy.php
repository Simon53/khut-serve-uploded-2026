@extends('layout.app')

@section('title', 'My CheckOut')
@section('content')
<style>
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }
</style>
<!-- ===== Checkout Section ===== -->
<div class="container">
    <div class="row">
        <div class="container py-5">
            <!-- Tabs -->
            <ul class="nav new-nav-tabs" id="checkoutTabs" role="tablist">
                <li><a class="nav-link active" id="billing-tab" data-toggle="tab" href="#billing" role="tab" data-step="1">Billing</a></li>
                <li><a class="nav-link disabled" id="review-tab" data-toggle="tab" href="#review" role="tab" data-step="3" style="pointer-events: none; opacity: 0.6;">Order Review</a></li>
                <li><a class="nav-link disabled" id="payment-tab" data-toggle="tab" href="#payment" role="tab" data-step="4" style="pointer-events: none; opacity: 0.6;">Payment</a></li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content checkoutTabLebel pt-3" id="checkoutTabContent">
                <!-- Billing Tab -->
                <div class="tab-pane fade show active" id="billing" role="tabpanel">
                    <div class="titel"><h2>Billing details</h2></div>
                    <form id="billingForm">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>First Name *</label>
                                <input type="text" class="form-control billing-field" id="billingFirstName" placeholder="First Name" required>
                                <div class="invalid-feedback" id="errorFirstName" style="display: none;"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Last Name *</label>
                                <input type="text" class="form-control billing-field" id="billingLastName" placeholder="Last Name" required>
                                <div class="invalid-feedback" id="errorLastName" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Country *</label>
                            <select class="form-control billing-field" id="billingCountry" required readonly>
                                <option value="Bangladesh" selected>Bangladesh</option>
                               
                            </select>
                            <div class="invalid-feedback" id="errorCountry" style="display: none; color: #dc3545; font-size: 0.875rem;"></div>
                        </div>
                        <div class="form-group">
                            <label>Apartment address (optional)</label>
                            <input type="text" class="form-control" id="billingApartment" placeholder="Apartment, suite, unit etc. (optional)">
                        </div>
                        <div class="form-group">
                            <label>Street address *</label>
                            <input type="text" class="form-control billing-field" id="billingAddress" placeholder="Building No. Flat No. Street No. etc." required>
                            <div class="invalid-feedback" id="errorAddress" style="display: none; color: #dc3545; font-size: 0.875rem;"></div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Town / City *</label>
                                <input type="text" class="form-control billing-field" id="billingCity" placeholder="Town / City" required>
                                <div class="invalid-feedback" id="errorCity" style="display: none; color: #dc3545; font-size: 0.875rem;"></div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>District *</label>
                                <select class="form-control billing-field" id="billingDistrict" required>
                                    <option value="">Select your district...</option>
                                    <option value="Bagerhat">Bagerhat</option>
                                    <option value="Bandarban">Bandarban</option>
                                    <option value="Barguna">Barguna</option>
                                    <option value="Barisal">Barisal</option>
                                    <option value="Bhola">Bhola</option>
                                    <option value="Bogra">Bogra</option>
                                    <option value="Brahmanbaria">Brahmanbaria</option>
                                    <option value="Chandpur">Chandpur</option>
                                    <option value="Chapainawabganj">Chapainawabganj</option>
                                    <option value="Chittagong">Chittagong</option>
                                    <option value="Chuadanga">Chuadanga</option>
                                    <option value="Comilla">Comilla</option>
                                    <option value="Cox's Bazar">Cox's Bazar</option>
                                    <option value="Dhaka" selected>Dhaka</option>
                                    <option value="Savar">Savar</option>
                                    <option value="Dinajpur">Dinajpur</option>
                                    <option value="Faridpur">Faridpur</option>
                                    <option value="Feni">Feni</option>
                                    <option value="Gaibandha">Gaibandha</option>
                                    <option value="Gazipur">Gazipur</option>
                                    <option value="Gopalganj">Gopalganj</option>
                                    <option value="Habiganj">Habiganj</option>
                                    <option value="Jamalpur">Jamalpur</option>
                                    <option value="Jessore">Jessore</option>
                                    <option value="Jhalokati">Jhalokati</option>
                                    <option value="Jhenaidah">Jhenaidah</option>
                                    <option value="Joypurhat">Joypurhat</option>
                                    <option value="Khagrachhari">Khagrachhari</option>
                                    <option value="Khulna">Khulna</option>
                                    <option value="Kishoreganj">Kishoreganj</option>
                                    <option value="Kurigram">Kurigram</option>
                                    <option value="Kushtia">Kushtia</option>
                                    <option value="Lakshmipur">Lakshmipur</option>
                                    <option value="Lalmonirhat">Lalmonirhat</option>
                                    <option value="Madaripur">Madaripur</option>
                                    <option value="Magura">Magura</option>
                                    <option value="Manikganj">Manikganj</option>
                                    <option value="Meherpur">Meherpur</option>
                                    <option value="Moulvibazar">Moulvibazar</option>
                                    <option value="Munshiganj">Munshiganj</option>
                                    <option value="Mymensingh">Mymensingh</option>
                                    <option value="Naogaon">Naogaon</option>
                                    <option value="Narail">Narail</option>
                                    <option value="Narayanganj">Narayanganj</option>
                                    <option value="Narsingdi">Narsingdi</option>
                                    <option value="Natore">Natore</option>
                                    <option value="Netrokona">Netrokona</option>
                                    <option value="Nilphamari">Nilphamari</option>
                                    <option value="Noakhali">Noakhali</option>
                                    <option value="Pabna">Pabna</option>
                                    <option value="Panchagarh">Panchagarh</option>
                                    <option value="Patuakhali">Patuakhali</option>
                                    <option value="Pirojpur">Pirojpur</option>
                                    <option value="Rajbari">Rajbari</option>
                                    <option value="Rajshahi">Rajshahi</option>
                                    <option value="Rangamati">Rangamati</option>
                                    <option value="Rangpur">Rangpur</option>
                                    <option value="Satkhira">Satkhira</option>
                                    <option value="Shariatpur">Shariatpur</option>
                                    <option value="Sherpur">Sherpur</option>
                                    <option value="Sirajganj">Sirajganj</option>
                                    <option value="Sunamganj">Sunamganj</option>
                                    <option value="Sylhet">Sylhet</option>
                                    <option value="Tangail">Tangail</option>
                                        <option value="Thakurgaon">Thakurgaon</option>
                                </select>
                                <div class="invalid-feedback" id="errorDistrict" style="display: none;"></div>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Postcode/ZIP *</label>
                                <input type="text" class="form-control billing-field" id="billingPostcode" placeholder="4-digit Number" required>
                                <div class="invalid-feedback" id="errorPostcode" style="display: none; color: #dc3545; font-size: 0.875rem;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Contact No. *</label>
                                <input type="text" class="form-control billing-field" id="billingPhone" placeholder="11-digit Number ei. 01xxxxxxxxx" required>
                                <div class="invalid-feedback" id="errorPhone" style="display: none; color: #dc3545; font-size: 0.875rem;"></div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Alternative Contact No </label>
                                <input type="text" class="form-control billing-field" id="alternativePhone" placeholder="11-digit Number ei. 01xxxxxxxxx" >
                                <div class="invalid-feedback" id="errorAlternativePhone" style="display: none; color: #dc3545; font-size: 0.875rem;"></div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Email address *</label>
                                <input type="email" class="form-control billing-field" id="billingEmail" placeholder="example@email.com" required>
                                <div class="invalid-feedback" id="errorEmail" style="display: none; color: #dc3545; font-size: 0.875rem;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Additional Note (Optional)</label>
                            <textarea class="form-control" rows="5" placeholder="Additional Note (Optional)"></textarea>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="gridCheck">
                            <label class="form-check-label" for="gridCheck">Create an account?</label>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('customer.login') }}" class="btn btn-outline-primary mr-2">Login</a>
                            <!--button type="button" class="btn btn-dark" onclick="validateAndNext('billing', 'shipping-tab')">Next</button-->
                            <button type="button" class="btn btn-dark" onclick="validateAndNext('billing', 'review-tab')">Next</button>
                        </div>
                    </form>
                </div>

                

                <!-- Order Review Tab -->
                <div class="tab-pane fade" id="review" role="tabpanel">
                    <div id="orderSummary"></div>
                    <div class="text-right mt-3">
                    <!-- <button class="btn btn-secondary" type="button" onclick="nextTab('shipping-tab')">Back</button> -->
                     <button class="btn btn-secondary" type="button" onclick="nextTab('billing-tab')">Back</button>
                        <button class="btn btn-dark" type="button" onclick="validateAndNext('review', 'payment-tab')">Next</button>
                    </div>
                </div>

                <!-- Payment Tab -->
                <div class="tab-pane fade" id="payment" role="tabpanel">
                    <div class="form-group">
                        <label><strong>Select Payment Method:</strong></label><br>
                        <div class="form-check mt-3" id="codPaymentOption" style="display: none;">
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="paymentMethod" value="cod">
                            Cash on Delivery</label>
                        </div>
                        <div class="form-check mt-2">
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="paymentMethod" value="card" checked>
                            Card/Mobile Banking</label>
                        </div>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-secondary" type="button" onclick="nextTab('review-tab')">Back</button>
                        <button class="btn btn-success">Place Order</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- ===== End Checkout Section ===== -->
@endsection

@section('script')
<script src="{{ asset('/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('/js/popper.js') }}"></script>
<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/js/custom.js') }}"></script>

<script>
    function isOutsideDhakaButSelectedDhaka(postcode) {
        const postcodeNum = parseInt(postcode);
        const isSavar = (postcodeNum >= 1340 && postcodeNum <= 1349);
        const isNarayanganj = (postcodeNum >= 1400 && postcodeNum <= 1430);
        return isSavar || isNarayanganj;
    }

   
    function isDhakaCorePostcode(postcode) {
        const p = parseInt(postcode);
        return (p == 1000 || p == 1100 || (p >= 1203 && p <= 1236));
    }

    function isSavar(postcode) {
        const p = parseInt(postcode);
        return (p >= 1340 && p <= 1349);
    }

    function isNarayanganj(postcode) {
        const p = parseInt(postcode);
        return (p >= 1400 && p <= 1430);
    }

    function isOutsideDhakaRegion(postcode) {
        return isSavar(postcode) || isNarayanganj(postcode);
    }
</script>

<script>

    // Track completed steps
    const completedSteps = {
        billing: false,
        shipping: false,
        review: false
    };

    function nextTab(tabId) {
        const tab = $('#' + tabId);
        if (!tab.hasClass('disabled')) {
            tab.tab('show');
        }
    }

    function enableTab(tabId) {
        const tab = $('#' + tabId);
        tab.removeClass('disabled');
        tab.css({'pointer-events': 'auto', 'opacity': '1'});
    }

    // Helper functions to show/hide error messages
    function showError(fieldId, message) {
        const errorDiv = $('#error' + fieldId);
        if (errorDiv.length) {
            errorDiv.text(message).css('display', 'block');
            const inputField = $('#billing' + fieldId);
            if (inputField.length) {
                inputField.addClass('is-invalid');
            } else {
                // Fallback: try to find by placeholder or other selector
                const fallbackField = $('#billing input, #billing select').filter(function() {
                    return $(this).attr('id') && $(this).attr('id').includes(fieldId.toLowerCase());
                });
                if (fallbackField.length) {
                    fallbackField.addClass('is-invalid');
                }
            }
        }
    }

    function hideError(fieldId) {
        const errorDiv = $('#error' + fieldId);
        if (errorDiv.length) {
            errorDiv.hide();
            const inputField = $('#billing' + fieldId);
            if (inputField.length) {
                inputField.removeClass('is-invalid');
            }
        }
    }

    function clearAllErrors() {
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');
    }

    // function validateBilling() {
    //     clearAllErrors();
    //     let isValid = true;

    //     // Get values using IDs
    //     const firstName = $("#billingFirstName").val() || '';
    //     const lastName = $("#billingLastName").val() || '';
    //     const email = $("#billingEmail").val() || '';
    //     const phone = $("#billingPhone").val() || '';
    //     const address = $("#billingAddress").val() || '';
    //     const country = $("#billingCountry").val() || '';
    //     const district = $("#billingDistrict").val() || '';
    //     const city = $("#billingCity").val() || '';
    //     const postcode = $("#billingPostcode").val() || '';
    //     const alternative_phone = $("#alternativePhone").val() || '';

    //     // Validate First Name
    //     if (!postcode.trim()) {
    //         showError('Postcode', 'Postcode is required.');
    //         isValid = false;

    //     } else if (!/^\d{4}$/.test(postcode.trim())) {
    //         showError('Postcode', 'Please enter a valid 4-digit postcode.');
    //         isValid = false;

    //     } else {

    //         const p = parseInt(postcode);

    //         // =========================
    //         // ЁЯФ┤ DHAKA SELECTED RULE
    //         // =========================
    //         if (district.toLowerCase() === 'dhaka') {

    //             // тЭМ Savar or Narayanganj block
    //             if (
    //                 (p >= 1340 && p <= 1349) ||   // Savar
    //                 (p >= 1400 && p <= 1430)      // Narayanganj
    //             ) {
    //                 showError('Postcode', 'This postcode is not inside Dhaka city.');
    //                 isValid = false;
    //             }

    //             // тЭМ invalid Dhaka core postcode block
    //             else if (!(p == 1000 || p == 1100 || (p >= 1203 && p <= 1236))) {
    //                 showError('Postcode', 'Please enter valid Dhaka city postcode.');
    //                 isValid = false;
    //             }
    //         }

    //         // =========================
    //         // ЁЯФ┤ SAVAR SELECTED RULE
    //         // =========================
    //         if (district.toLowerCase() === 'savar') {
    //             if (!(p >= 1340 && p <= 1349)) {
    //                 showError('Postcode', 'Invalid Savar postcode.');
    //                 isValid = false;
    //             }
    //         }

    //         // =========================
    //         // ЁЯФ┤ NARAYANGANJ SELECTED RULE
    //         // =========================
    //         if (district.toLowerCase() === 'narayanganj') {
    //             if (!(p >= 1400 && p <= 1430)) {
    //                 showError('Postcode', 'Invalid Narayanganj postcode.');
    //                 isValid = false;
    //             }
    //         }

    //          // Validate Email
    //         if (!email.trim()) {
    //             showError('Email', 'Email address is required.');
    //             isValid = false;
    //         } else {
    //             const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    //             if (!emailRegex.test(email.trim())) {
    //                 showError('Email', 'Please enter a valid email address.');
    //                 isValid = false;
    //             }
    //         }

    //          // Validate Phone
    //         if (!phone.trim()) {
    //             showError('Phone', 'Phone number is required.');
    //             isValid = false;
    //         } else {
    //             const phoneClean = phone.replace(/\s/g, '');
    //             if (!/^01\d{9}$/.test(phoneClean)) {
    //                 showError('Phone', 'Please enter a valid 11-digit phone number starting with 01.');
    //                 isValid = false;
    //             }
    //         }

            
    //     // Validate Address
    //     if (!address.trim()) {
    //         showError('Address', 'Street address is required.');
    //         isValid = false;
    //     }

    //         // Validate Country
    //         if (!country || country === 'Select Country...') {
    //             showError('Country', 'Please select a country.');
    //             isValid = false;
    //         }

    //         // Validate District
    //         if (!district || district === 'Select your district...') {
    //             showError('District', 'Please select a district.');
    //             isValid = false;
    //         }

    //         // Validate City
    //         if (!city.trim()) {
    //             showError('City', 'Town/City is required.');
    //             isValid = false;
    //         }

    //         // Validate Alternative Phone (Optional)
    //         if (alternative_phone.trim()) {
    //             const altPhoneClean = alternative_phone.replace(/\s/g, '');

    //             if (!/^01\d{9}$/.test(altPhoneClean)) {
    //                 showError('AlternativePhone', 'Please enter a valid 11-digit alternative phone number starting with 01.');
    //                 isValid = false;
    //             }
    //         }
    //     }

    //     return isValid;
    // }

    function validateBilling() {
    clearAllErrors();

    let isValid = true;

    // Get values
    const firstName = $("#billingFirstName").val() || '';
    const lastName = $("#billingLastName").val() || '';
    const email = $("#billingEmail").val() || '';
    const phone = $("#billingPhone").val() || '';
    const address = $("#billingAddress").val() || '';
    const country = $("#billingCountry").val() || '';
    const district = $("#billingDistrict").val() || '';
    const city = $("#billingCity").val() || '';
    const postcode = $("#billingPostcode").val() || '';
    const alternative_phone = $("#alternativePhone").val() || '';


    // ==========================================
    // FIRST NAME
    // ==========================================
    if (!firstName.trim()) {
        showError('FirstName', 'First Name is required.');
        isValid = false;
    } else if (firstName.trim().length < 2) {
        showError('FirstName', 'First Name must be at least 2 characters.');
        isValid = false;
    }


    // ==========================================
    // LAST NAME
    // ==========================================
    if (!lastName.trim()) {
        showError('LastName', 'Last Name is required.');
        isValid = false;
    } else if (lastName.trim().length < 2) {
        showError('LastName', 'Last Name must be at least 2 characters.');
        isValid = false;
    }


    // ==========================================
    // EMAIL
    // ==========================================
    if (!email.trim()) {
        showError('Email', 'Email address is required.');
        isValid = false;
    } else {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email.trim())) {
            showError('Email', 'Please enter a valid email address.');
            isValid = false;
        }
    }


    // ==========================================
    // PHONE
    // ==========================================
    if (!phone.trim()) {
        showError('Phone', 'Phone number is required.');
        isValid = false;
    } else {
        const phoneClean = phone.replace(/\s/g, '');

        if (!/^01\d{9}$/.test(phoneClean)) {
            showError(
                'Phone',
                'Please enter a valid 11-digit phone number starting with 01.'
            );
            isValid = false;
        }
    }


    // ==========================================
    // ADDRESS
    // ==========================================
    if (!address.trim()) {
        showError('Address', 'Street address is required.');
        isValid = false;
    }


    // ==========================================
    // COUNTRY
    // ==========================================
    if (!country || country === 'Select Country...') {
        showError('Country', 'Please select a country.');
        isValid = false;
    }


    // ==========================================
    // DISTRICT
    // ==========================================
    if (!district || district === 'Select your district...') {
        showError('District', 'Please select a district.');
        isValid = false;
    }


    // ==========================================
    // CITY
    // ==========================================
    if (!city.trim()) {
        showError('City', 'Town/City is required.');
        isValid = false;
    }


    // ==========================================
    // POSTCODE
    // ==========================================
    if (!postcode.trim()) {

        showError('Postcode', 'Postcode is required.');
        isValid = false;

    } else if (!/^\d{4}$/.test(postcode.trim())) {

        showError(
            'Postcode',
            'Please enter a valid 4-digit postcode.'
        );
        isValid = false;

    } else {

        const p = parseInt(postcode.trim(), 10);


        // ==========================================
        // DHAKA SELECTED
        // ==========================================
        if (district.toLowerCase() === 'dhaka') {

            // Savar / Narayanganj postcode cannot use Dhaka
            if (
                (p >= 1340 && p <= 1349) ||
                (p >= 1400 && p <= 1430)
            ) {
                showError(
                    'Postcode',
                    'This postcode is not inside Dhaka city.'
                );
                isValid = false;
            }

            // Valid Dhaka Core postcode
            else if (
                !(
                    p === 1000 ||
                    p === 1100 ||
                    (p >= 1203 && p <= 1236)
                )
            ) {
                showError(
                    'Postcode',
                    'Please enter valid Dhaka city postcode.'
                );
                isValid = false;
            }
        }


        // ==========================================
        // SAVAR SELECTED
        // ==========================================
        if (district.toLowerCase() === 'savar') {

            if (!(p >= 1340 && p <= 1349)) {
                showError(
                    'Postcode',
                    'Invalid Savar postcode.'
                );
                isValid = false;
            }
        }


        // ==========================================
        // NARAYANGANJ SELECTED
        // ==========================================
        if (district.toLowerCase() === 'narayanganj') {

            if (!(p >= 1400 && p <= 1430)) {
                showError(
                    'Postcode',
                    'Invalid Narayanganj postcode.'
                );
                isValid = false;
            }
        }
    }


    // ==========================================
    // ALTERNATIVE PHONE - OPTIONAL
    // ==========================================
    if (alternative_phone.trim()) {

        const altPhoneClean = alternative_phone.replace(/\s/g, '');

        if (!/^01\d{9}$/.test(altPhoneClean)) {
            showError(
                'AlternativePhone',
                'Please enter a valid 11-digit alternative phone number starting with 01.'
            );
            isValid = false;
        }
    }


    return isValid;
}

    function validateShipping() {
        // If "Ship to different address" is not checked, shipping is automatically valid
        if (!$("#shipDifferent").is(":checked")) {
            return true;
        }

        // If checked, validate shipping fields using HTML5 validation
        const shippingForm = document.getElementById("shippingForm");
        if (shippingForm.checkValidity()) {
            return true;
        } else {
            // Show HTML5 validation messages
            shippingForm.reportValidity();
            return false;
        }
    }

    function validateReview() {
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        if (cart.length === 0) {
            // Show error in order summary area
            const orderSummary = document.getElementById("orderSummary");
            if (orderSummary) {
                orderSummary.innerHTML = '<div class="alert alert-danger">Your cart is empty! Please add items to your cart before proceeding.</div>';
            }
            return false;
        }
        return true;
    }

    function validateAndNext(currentStep, nextTabId) {
    let isValid = false;

    switch (currentStep) {

        case 'billing':

            isValid = validateBilling();

            if (isValid) {
                completedSteps.billing = true;

                // Shipping বাদ দেওয়া হয়েছে
                // সরাসরি Order Review enable হবে
                enableTab('review-tab');

                nextTab('review-tab');
            }

            break;


        case 'review':

            isValid = validateReview();

            if (isValid) {
                completedSteps.review = true;

                // Payment enable
                enableTab('payment-tab');

                nextTab('payment-tab');
            }

            break;
    }
}

    // Check if postcode is in the special range (1000, 1100, 1203-1236)
    function isPostcodeInSpecialRange(postcode) {
        const postcodeNum = parseInt(postcode);
        // If postcode is 1000, 1100, or between 1203-1236
        return (postcodeNum == 1000 || postcodeNum == 1100 || (postcodeNum >= 1203 && postcodeNum <= 1236));
    }

    // Calculate delivery charge based on postcode
    function calculateDeliveryCharge(postcode) {
        // If postcode is in special range, delivery charge is 80
        if (isPostcodeInSpecialRange(postcode)) {
            return 80;
        }
        return 150;
    }

 
    
    
    // Update payment method options based on postcode AND district
    function updatePaymentMethods() {
        const postcode = $("#billingPostcode").val() || '';
        const district = $("#billingDistrict").val() || '';

        const codOption = $("#codPaymentOption");
        const cardRadio = $("input[name='paymentMethod'][value='card']");
        const codRadio = $("input[name='paymentMethod'][value='cod']");

        // тЭМ Dhaka ржирж╛ рж╣рж▓рзЗ COD ржирж╛ржЗ
        if (district && district.toLowerCase() !== 'dhaka') {
            codOption.hide();
            if (codRadio.is(':checked')) {
                cardRadio.prop('checked', true);
            }
            return;
        }

        // ЁЯФ┤ Dhaka select but Savar/Narayanganj тЖТ COD hide
        if (district.toLowerCase() === 'dhaka' && isOutsideDhakaButSelectedDhaka(postcode)) {
            codOption.hide();
            cardRadio.prop('checked', true);
            return;
        }

        // тЬЕ Dhaka valid postcode рж╣рж▓рзЗ
        if (postcode && /^\d{4}$/.test(postcode.trim())) {
            const isInSpecialRange = isPostcodeInSpecialRange(postcode);

            if (isInSpecialRange) {
                codOption.show();
                if (cardRadio.is(':checked')) {
                    codRadio.prop('checked', true);
                }
            } else {
                codOption.hide();
                if (codRadio.is(':checked')) {
                    cardRadio.prop('checked', true);
                }
            }
        } else {
            codOption.hide();
            cardRadio.prop('checked', true);
        }
    }
    
    


    

    function loadOrderSummary() {

    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    let subtotal = 0;

    // Get postcode from billing form
    const postcode = $("#billingPostcode").val() || '';

    // Calculate delivery charge
    const deliveryCharge = calculateDeliveryCharge(postcode);

    // =========================================================
    // CSS
    // =========================================================

    let html = `
        <style>

            /* =================================================
               DESKTOP REVIEW
            ================================================= */

            .review-desktop-table {
                display: block;
                width: 100%;
            }

            .review-desktop-table table {
                width: 100%;
                margin-bottom: 0;
                table-layout: auto;
            }

            .review-desktop-table th,
            .review-desktop-table td {
                vertical-align: middle;
            }

            /* Price column */
            .review-price-column {
                white-space: nowrap;
                text-align: right !important;
            }

            /* Desktop totals */
            .review-desktop-total {
                width: 100%;
                margin-top: 0;
                border-top: 0;
            }

            .review-desktop-total table {
                width: 100%;
                margin-bottom: 0;
            }

            .review-desktop-total td {
                padding: 8px 12px;
                border-top: 0;
            }

            /*
             * IMPORTANT:
             * Label will occupy all columns before Price column.
             * Amount will stay exactly under Price column.
             */
            .review-total-label {
                text-align: right !important;
                font-weight: 500;
            }

            .review-total-amount {
                width: 120px;
                text-align: right !important;
                white-space: nowrap;
                font-weight: 500;
            }

            .review-grand-total td {
                border-top: 1px solid #ddd !important;
                padding-top: 10px !important;
                font-size: 16px;
                font-weight: 700 !important;
            }


            /* =================================================
               MOBILE REVIEW
            ================================================= */

            .review-mobile-list {
                display: none;
            }

            .review-mobile-title {
                font-size: 16px;
                font-weight: 700;
                margin-bottom: 10px;
            }

            .review-product-card {
                display: flex;
                align-items: center;
                width: 100%;
                gap: 9px;
                padding: 10px 0;
                border-bottom: 1px solid #e5e5e5;
            }

            .review-product-img {
                width: 58px;
                height: 58px;
                object-fit: cover;
                border-radius: 6px;
                flex-shrink: 0;
            }

            .review-product-info {
                flex: 1;
                min-width: 0;
            }

            .review-product-name {
                font-size: 13px;
                font-weight: 600;
                line-height: 1.3;
                margin-bottom: 3px;

                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .review-product-meta {
                font-size: 11px;
                color: #777;
                line-height: 1.45;
            }

            .review-product-price {
                font-size: 13px;
                font-weight: 600;
                white-space: nowrap;
                flex-shrink: 0;
            }

            .review-mobile-remove {
                border: none;
                background: transparent;
                color: #dc3545;
                font-size: 21px;
                line-height: 1;
                font-weight: 700;
                padding: 2px 3px;
                cursor: pointer;
                flex-shrink: 0;
            }

            .review-mobile-remove:hover {
                color: #a71d2a;
            }

            .review-mobile-total {
                width: 100%;
                margin-top: 12px;
                padding-top: 10px;
                border-top: 1px solid #ddd;
            }

            .review-mobile-total-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 13px;
                margin-bottom: 7px;
            }

            .review-mobile-total-row:last-child {
                margin-bottom: 0;
            }

            .review-mobile-grand-total {
                border-top: 1px solid #ddd;
                padding-top: 9px;
                margin-top: 9px;
                font-size: 16px;
                font-weight: 700;
            }


            /* =================================================
               MOBILE
            ================================================= */

            @media (max-width: 767px) {

                .review-desktop-table {
                    display: none !important;
                }

                .review-mobile-list {
                    display: block !important;
                    width: 100%;
                    overflow: hidden;
                }

                #orderSummary {
                    width: 100%;
                    overflow: hidden;
                }

                .review-product-card {
                    max-width: 100%;
                }

                .review-product-name {
                    max-width: 170px;
                }
            }

        </style>


        <!-- =====================================================
             DESKTOP ORDER TABLE
        ===================================================== -->

        <div class="review-desktop-table">

            <table class="table">

                <thead>
                    <tr>
                        <th>Remove</th>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Qty</th>
                        <th>Barcode</th>
                        <th class="review-price-column">Price</th>
                    </tr>
                </thead>

                <tbody>
    `;


    // =========================================================
    // PRODUCTS
    // =========================================================

    cart.forEach(item => {

        const qty = Number(item.qty) || 0;
        const price = Number(item.price) || 0;

        const itemTotal = qty * price;

        subtotal += itemTotal;


        html += `
            <tr>

                <!-- Remove -->
                <td>
                    <button
                        type="button"
                        class="removeItem"
                        data-id="${item.id}"
                        title="Remove"
                        style="
                            border:none;
                            background:transparent;
                            color:#dc3545;
                            font-size:22px;
                            font-weight:bold;
                            line-height:1;
                            cursor:pointer;
                            padding:2px 5px;
                        "
                    >&times;</button>
                </td>


                <!-- Image -->
                <td>
                    <img
                        src="${item.img}"
                        alt="${item.name}"
                        width="50"
                        height="50"
                        style="
                            width:50px;
                            height:50px;
                            object-fit:cover;
                            border-radius:5px;
                        "
                    >
                </td>


                <!-- Product -->
                <td>
                    ${item.name}
                </td>


                <!-- Size -->
                <td>
                    ${item.size || '-'}
                </td>


                <!-- Color -->
                <td>
                    ${item.color || '-'}
                </td>


                <!-- Quantity -->
                <td>
                    ${qty}
                </td>


                <!-- Barcode -->
                <td>
                    ${item.barcode || '-'}
                </td>


                <!-- PRICE -->
                <td class="review-price-column">
                    ৳ ${itemTotal}
                </td>

            </tr>
        `;
    });


    html += `
                </tbody>

            </table>


            <!-- =================================================
                 DESKTOP TOTALS

                 8 columns exactly same as above.

                 First 7 columns = label area
                 Last column = Price column

                 Therefore amount stays under Price column.
            ================================================= -->

            <div class="review-desktop-total">

                <table class="table">

                    <tbody>

                        <!-- SUBTOTAL -->
                        <tr>

                            <td
                                colspan="7"
                                class="review-total-label"
                            >
                                Subtotal
                            </td>

                            <td
                                class="review-total-amount"
                            >
                                ৳ ${subtotal}
                            </td>

                        </tr>


                        <!-- DELIVERY -->
                        <tr>

                            <td
                                colspan="7"
                                class="review-total-label"
                            >
                                Delivery
                                ${postcode ? `(Postcode: ${postcode})` : ''}
                            </td>

                            <td
                                class="review-total-amount"
                            >
                                ৳ ${deliveryCharge}
                            </td>

                        </tr>


                        <!-- TOTAL -->
                        <tr class="review-grand-total">

                            <td
                                colspan="7"
                                class="review-total-label"
                            >
                                <strong>Total</strong>
                            </td>

                            <td
                                class="review-total-amount"
                            >
                                <strong>
                                    ৳ ${subtotal + deliveryCharge}
                                </strong>
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- =====================================================
             MOBILE ORDER
        ===================================================== -->

        <div class="review-mobile-list">

            <div class="review-mobile-title">
                Your Order
            </div>
    `;


    // =========================================================
    // MOBILE PRODUCTS
    // =========================================================

    cart.forEach(item => {

        const qty = Number(item.qty) || 0;
        const price = Number(item.price) || 0;

        const itemTotal = qty * price;


        html += `
            <div class="review-product-card">

                <!-- Product Image -->
                <img
                    src="${item.img}"
                    alt="${item.name}"
                    class="review-product-img"
                >


                <!-- Product Info -->
                <div class="review-product-info">

                    <div class="review-product-name">
                        ${item.name}
                    </div>

                    <div class="review-product-meta">

                        ${item.size
                            ? `Size: ${item.size}`
                            : ''
                        }

                        ${
                            item.size && item.color
                                ? ' | '
                                : ''
                        }

                        ${item.color
                            ? `Color: ${item.color}`
                            : ''
                        }

                    </div>

                    <div class="review-product-meta">
                        Qty: ${qty}
                    </div>

                </div>


                <!-- Product Price -->
                <div class="review-product-price">
                    ৳ ${itemTotal}
                </div>


                <!-- Remove X -->
                <button
                    type="button"
                    class="review-mobile-remove removeItem"
                    data-id="${item.id}"
                    title="Remove"
                >&times;</button>

            </div>
        `;
    });


    // =========================================================
    // MOBILE TOTAL
    // =========================================================

    html += `

            <div class="review-mobile-total">

                <div class="review-mobile-total-row">

                    <span>
                        Subtotal
                    </span>

                    <strong>
                        ৳ ${subtotal}
                    </strong>

                </div>


                <div class="review-mobile-total-row">

                    <span>
                        Delivery
                        ${postcode ? `(${postcode})` : ''}
                    </span>

                    <strong>
                        ৳ ${deliveryCharge}
                    </strong>

                </div>


                <div class="review-mobile-total-row review-mobile-grand-total">

                    <span>
                        Total
                    </span>

                    <strong>
                        ৳ ${subtotal + deliveryCharge}
                    </strong>

                </div>

            </div>

        </div>
    `;


    // =========================================================
    // INSERT HTML
    // =========================================================

    document.getElementById("orderSummary").innerHTML = html;


    // =========================================================
    // REMOVE ITEM
    // =========================================================

    document.querySelectorAll(".removeItem").forEach(btn => {

        btn.addEventListener("click", function(e) {

            e.preventDefault();

            const id = this.dataset.id;

            let cart = JSON.parse(localStorage.getItem("cart")) || [];

            cart = cart.filter(item => item.id != id);

            localStorage.setItem("cart", JSON.stringify(cart));


            // Reload review
            loadOrderSummary();


            // Update cart if functions exist
            if (typeof renderCart === "function") {
                renderCart();
            }

            if (typeof updateCartCounts === "function") {
                updateCartCounts();
            }

        });

    });

}

    $(document).ready(function() {
        // Prevent clicking on disabled tabs
        $('a[data-toggle="tab"].disabled').on('click', function(e) {
            e.preventDefault();
            return false;
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            if(e.target.id === 'review-tab') {
                loadOrderSummary();
            }
            if(e.target.id === 'payment-tab') {
                updatePaymentMethods();
            }
        });
        
        // Update payment methods and order summary when postcode changes
        $('#billingPostcode, #billing input[placeholder="4-digit Number"]').on('change blur input', function() {
            updatePaymentMethods();
            if($('#review-tab').hasClass('active')) {
                loadOrderSummary();
            }
        });

        // Shipping toggle
        $(".shippingHidden").hide();
        $("#shipDifferent").on("change", function() {
            if($(this).is(":checked")) {
                $(".shippingHidden").slideDown();
            } else {
                $(".shippingHidden").slideUp();
            }
        });

        // Silently check if billing is complete (without alerts) to enable next tab
        $('.billing-field, #billingCountry').on('change blur', function() {
            // Only check silently, don't show alerts
            const firstName = $("#billing input[placeholder='First Name']").val() || '';
            const lastName = $("#billing input[placeholder='Last Name']").val() || '';
            const email = $("#billing input[placeholder='example@email.com']").val() || '';
            const phone = $("#billing input[placeholder*='01']").val() || '';
            const alternative_phone = $("#billing input[placeholder*='01']").val() || '';
            const address = $("#billing input[placeholder*='Building']").val() || '';
            const country = $("#billingCountry").val() || '';
            //const district = $("#billing select.form-control.billing-field").val() || '';
            const district = $("#billingDistrict").val() || '';
            const city = $("#billing input[placeholder='Town / City']").val() || '';
            const postcode = $("#billing input[placeholder='4-digit Number']").val() || '';
            
            // Check if all fields are filled (silent check)
            if (firstName.trim() && lastName.trim() && email.trim() && phone.trim() && 
                address.trim() && country && country !== 'Select Country...' &&
                district && district !== 'Select your district...' && 
                city.trim() && postcode.trim()) {
                // Validate formats silently
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const phoneClean = phone.replace(/\s/g, '');
                if (emailRegex.test(email.trim()) && /^01\d{9}$/.test(phoneClean) && /^\d{4}$/.test(postcode.trim())) {
                    if (!completedSteps.billing) {
                        completedSteps.billing = true;
                        enableTab('review-tab');
                    }
                }
            }
        });
    });

//--------place order-----
 $(document).on("click", ".btn-success", function() {
    const cart = localStorage.getItem("cart");
    if (!cart || JSON.parse(cart).length === 0) {
        // Show error in payment section
        $('#payment').prepend('<div class="alert alert-danger alert-dismissible fade show" role="alert">Your cart is empty! Please add items to your cart before placing an order.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
        return;
    }

    const paymentMethod = $("input[name='paymentMethod']:checked").val();
	const district = $("#billingDistrict").val();
    const form = {
        first_name: $("#billing input[placeholder='First Name']").val(),
        last_name: $("#billing input[placeholder='Last Name']").val(),
        email: $("#billing input[placeholder='example@email.com']").val(),
        phone: $("#billing input[placeholder*='01']").val(),
        alternative_phone: $("#billing input[placeholder*='01']").val(),
        address: $("#billing input[placeholder*='Building']").val(),
        apartment: $("#billing input[placeholder*='Apartment']").val() || '',
        country: $("#billingCountry").val() || 'Bangladesh',
        //district: $("#billing select.form-control.billing-field").val(),
        district: $("#billingDistrict").val(),
        city: $("#billing input[placeholder='Town / City']").val(),
        postcode: $("#billing input[placeholder='4-digit Number']").val(),
        note: $("#billing textarea").val() || '',
        payment_method: paymentMethod,
        cart: cart
    };
	
	// ЁЯЪл Dhaka outside рж╣рж▓рзЗ payment mandatory (no COD)
	if (district && district.toLowerCase() !== 'dhaka') {
		if (paymentMethod !== 'card') {
			$('#payment').prepend(`
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					Orders outside Dhaka require advance payment. Please complete your payment to proceed. 
					Please select Card/Mobile Banking.
					<button type="button" class="close" data-dismiss="alert">
						<span>&times;</span>
					</button>
				</div>
			`);

			setTimeout(function() {
				$('.alert').fadeOut();
			}, 5000);

			return; // тЭМ Stop order submit
		}
	}

  

    // If Card payment, create a form and submit directly (not via AJAX)
    if (paymentMethod === 'card') {
        // Create a hidden form and submit it
        // Note: CSRF is excluded for this route in middleware
        const paymentForm = document.createElement('form');
        paymentForm.method = 'POST';
        paymentForm.action = "{{ route('checkout.pay') }}";
        paymentForm.style.display = 'none';
        
        // Add CSRF token (even though route is excluded, include it for safety)
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        paymentForm.appendChild(csrfInput);
        
        // Add all form fields
        Object.keys(form).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = form[key];
            paymentForm.appendChild(input);
        });
        
        // Append to body and submit
        document.body.appendChild(paymentForm);
        
        // Add a small delay to ensure form is properly added to DOM
        setTimeout(function() {
            paymentForm.submit();
        }, 100);
        return;
            } else {
        // For COD and bKash, use existing order.store route
        // The route now redirects to success page, so we need to submit as form
        const orderForm = document.createElement('form');
        orderForm.method = 'POST';
        orderForm.action = "{{ route('order.store') }}";
        orderForm.style.display = 'none';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        orderForm.appendChild(csrfInput);
        
        // Add all form fields
        Object.keys(form).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = form[key];
            orderForm.appendChild(input);
        });
        
        // Append to body and submit
        document.body.appendChild(orderForm);
        
        // Add a small delay to ensure form is properly added to DOM
        setTimeout(function() {
            orderForm.submit();
        }, 100);
    }

 });
</script>
@endsection

