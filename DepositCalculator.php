<?php
error_reporting(E_ERROR | E_PARSE);
session_start();

extract($_GET);

$principalAmount = $_GET["principalAmount"];
$interestRate = $_GET["interestRate"];
$depositYear = $_GET["depositYear"] ? $_GET["depositYear"] : -1;

$customerName = $_GET["customerName"];
$postalCode = $_GET["postalCode"];
$phoneNumber = $_GET["phoneNumber"];
$emailAddress = $_GET["emailAddress"];

$contactMethod = $_GET["contactMethod"];
$contactTimeArray = array();
if (isset($_GET['contactTime'])) {
    $contactTimeArray = $_GET['contactTime'];
}

/***************Validation functions****************/
$principalErrMsg = '';
function ValidatePrincipal($principalAmount): string
{

    if (!is_numeric($principalAmount)) {
        return 'Principal amount must be numeric and non-empty!';
    } else if ($principalAmount <= 0) {
        return 'Principal amount must be positive!';
    } else {
        return '';
    }
};

$intRateErrMsg = '';
function ValidateintRate($interestRate): string
{

    if (!is_numeric($interestRate)) {
        return 'Interest rate amount must be numeric and non-empty!';
    } else if ($interestRate < 0) {
        return 'Interest rate amount must be non-negative!';
    } else {
        return '';
    }
};

$depoYearErrMsg = '';
function ValidateYear($depositYear): string
{

    return $depositYear == -1 ? 'You must select a deposit year' : '';
}

$nameErrMsg = '';
function ValidateName($customerName): string
{

    return preg_match('/[A-Za-z]/', $customerName) ? '' : 'You must enter a valid name';
};

$postalCodeErrMsg = '';
function ValidatePostalCode($postalCode): string
{

    return preg_match("/[a-z][0-9][a-z]\s*[0-9][a-z][0-9]/i", $postalCode) == 1 ? '' : 'You must enter a valid postal code';
};

$phoneNumberErrMsg = '';
function ValidatePhoneNumber($phoneNumber): string
{

    return preg_match("/^[1-9]\d{2}-\d{3}-\d{4}$/", $phoneNumber) ? '' : 'You must enter a valid phone number format.';
};

$emailErrMsg = '';
function ValidateEmail($emailAddress): string
{

    return preg_match("/\b[a-zA-Z0-9._%+-]+@(([a-zA-Z0-9-]+)\.)+[a-zA-Z]{2,4}\b/", $emailAddress) ? '' : 'You must enter a valid email address.';
};

$contactMethodErrMsg = '';
function ValidateContact($contactMethod, $contactTimeArray): string
{

    return (($contactMethod == "Phone" && $contactTimeArray) || $contactMethod == "Email") == true ? '' : 'When preferredn contact method is phone, you have to select contact time';
};

$checkAllInputField = 0;
/**************************************************/
if (isset($btnSubmit)) {

    $principalErrMsg = ValidatePrincipal($principalAmount);
    $intRateErrMsg = ValidateintRate($interestRate);
    $depoYearErrMsg = ValidateYear($depositYear);
    $nameErrMsg = ValidateName($customerName);
    $postalCodeErrMsg = ValidatePostalCode($postalCode);
    $phoneNumberErrMsg = ValidatePhoneNumber($phoneNumber);
    $emailErrMsg = ValidateEmail($emailAddress);
    $contactMethodErrMsg = ValidateContact($contactMethod, $contactTimeArray);

    if (!$principalErrMsg && !$intRateErrMsg && !$depoYearErrMsg && !$nameErrMsg && !$postalCodeErrMsg && !$phoneNumberErrMsg && !$emailErrMsg && !$contactMethodErrMsg) {

        $checkAllInputField = 1;
    }
};

$principalArray;
$interestArray;

if ($checkAllInputField == 1) {

    // Initialize the arrays:
    $principalArray = array(sprintf('%.2f', $principalAmount));
    $interestArray = array(sprintf('%.2f', $principalAmount * ($interestRate / 100)));

    for ($i = 1; $i < $depositYear; $i++) {

        $principal = sprintf('%.2f', (end($principalArray) * ($interestRate / 100) + end($principalArray)));
        array_push($principalArray, $principal);

        $interest = sprintf('%.2f', (end($principalArray) * (($interestRate / 100))));
        array_push($interestArray, $interest);
    };
};

// print_r($checkAllInputField);

// print_r($principalArray);
// print_r($interestArray);
?>

<html>

<head>
    <meta charset="UTF-8">
    <title>Deposit Calculator</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <style>
        body {
            padding-bottom: 50px;
        }

        h1 {
            margin-top: 50px;
            margin-bottom: 40px;
        }

        select.form-control {
            display: inline-block
        }

        label:not(.labelLight) {
            font-weight: 500;
        }

        .labelLight {
            font-weight: 100;
        }

        small {
            color: red;
        }

        .textHighlight {
            font-size: large;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Deposit Calculator</h1>

        <?php if (!$checkAllInputField) { ?>

            <form method="get" action="DepositCalculator.php" name="customerForm" id="customerForm">
                <div class="form-group row">
                    <label for="principalAmount" class="col-sm-2 col-form-label">Principal Amount</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="principalAmount" value="<?php echo $principalAmount ?>" name="principalAmount" placeholder="your principal">
                        <small><?php echo $principalErrMsg ?></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="interestRate" class="col-sm-2 col-form-label">Interest Rate (%):</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="interestRate" placeholder="interest rate" value="<?php echo $interestRate ?>" name="interestRate">
                        <small><?php echo $intRateErrMsg ?></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Years to Deposit:</label>
                    <div class="col-sm-3">
                        <select class="custom-select" aria-label="Default select example" id="yearsDropDown" name="depositYear">
                            <option value="-1">Select....</option>
                        </select>
                        <small><?php echo $depoYearErrMsg ?></small>
                    </div>
                </div>

                <hr>

                <div class="form-group row">
                    <label for="customerName" class="col-sm-2 col-form-label">Name:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="customerName" name="customerName" value="<?php echo $customerName ?>">
                    </div>
                    <small><?php echo $nameErrMsg ?></small>
                </div>
                <div class="form-group row">
                    <label for="postalCode" class="col-sm-2 col-form-label">Postal Code:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="postalCode" name="postalCode" value="<?php echo $postalCode ?>">
                        <small><?php echo $postalCodeErrMsg ?></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phoneNumber" class="col-sm-2 col-form-label">Phone Number:<span style="display: block">(nnn-nnn-nnnn)</span></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo $phoneNumber ?>">
                        <small><?php echo $phoneNumberErrMsg ?></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="emailAddress" class="col-sm-2 col-form-label">Email Address:</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="emailAddress" name="emailAddress" value="<?php echo $emailAddress ?>">
                        <small><?php echo $emailErrMsg ?></small>
                    </div>
                </div>

                <hr>

                <div>
                    <label>Preferred contact Method:</label>
                    <div class="form-check form-check-inline">
                        <input checked class="form-check-input" type="radio" id="inlineRadio1" value="Phone" name="contactMethod">
                        <label class="form-check-label labelLight" for="inlineRadio1">Phone</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="inlineRadio2" value="Email" name="contactMethod">
                        <label class="form-check-label labelLight" for="inlineRadio2">Email</label>
                    </div>
                </div>

                <div>

                    <label>If phone is selected, when can we contact you? (check all applicable)</label>

                    <br>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="Morning" name="contactTime[]" <?php if (in_array("Morning", $contactTimeArray)) {
                                                                                                                                        echo "checked";
                                                                                                                                    } ?>>
                        <label class="form-check-label labelLight" for="inlineCheckbox1">Morning</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="Afternoon" name="contactTime[]" <?php if (in_array("Afternoon", $contactTimeArray)) {
                                                                                                                                        echo "checked";
                                                                                                                                    } ?>>
                        <label class="form-check-label labelLight" for="inlineCheckbox2">Afternoon</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="Evening" name="contactTime[]" <?php if (in_array("Evening", $contactTimeArray)) {
                                                                                                                                        echo "checked";
                                                                                                                                    } ?>>
                        <label class="form-check-label labelLight" for="inlineCheckbox3">Evening</label>
                    </div>
                    <small><?php echo $contactMethodErrMsg ?></small>
                </div>

                <br>

                <div class="container">
                    <div class="row">
                        <button type="submit" class="btn btn-primary" style="margin-right: 20px;" name="btnSubmit">Calculate</button>
                        <button class="btn btn-primary" id="clearBtn">Clear</button>
                    </div>
                </div>

            </form>

        <?php } else if ($checkAllInputField) { ?>

            <h2>Thank you, <?php echo $customerName ?>, for using our deposit calculation tool.</h2>

            <?php if ($contactMethod == "Phone") { ?>

                <p>
                    Our customer service department will call you tomorrow in the following time period:
                    <span class="textHighlight"><?php foreach ($contactTimeArray as $time) {
                                                    echo strtolower("{$time}, ");
                                                }; ?></span>
                    at
                    <span class="textHighlight"><?php echo $phoneNumber ?>.</span>
                </p>

            <?php } elseif ($contactMethod == "Email") { ?>

                <p>Our customer service department will contact you shortly via email: <span class="textHighlight"><?php echo "{$emailAddress}" ?></span></p>

            <?php } ?>

            <p>The following is the result of the calculation:</p>

            <table class="table table-striped">
                <tr>
                    <th class="textHighlight">Year</th>
                    <th class="textHighlight">Principal at Year Start</th>
                    <th class="textHighlight">Interest for the Year</th>
                </tr>

                <?php for ($i = 0; $i < $depositYear; $i++) { ?>

                    <tr>
                        <th class="labelLight"><?php $j = $i + 1;
                                                echo "{$j}"; ?></th>
                        <th class="labelLight"><?php echo "$" . "{$principalArray[$i]}"; ?></th>
                        <th class="labelLight"><?php echo "$" . "{$interestArray[$i]}"; ?></th>
                    </tr>

                <?php } ?>
            <?php } ?>
    </div>
</body>

<script>
    // Be sure to use json_encode() method; PHP string and JavaScript string are not compatible.
    document.customerForm.contactMethod.value = <?php echo json_encode($contactMethod); ?>;

    /* For loop to add deposit years to 20 */
    let yearsDropDown = document.querySelector("#yearsDropDown");

    let min = 1;
    let max = 20;

    for (let i = min; i <= max; i++) {
        let opt = document.createElement('option');

        if (i == <?php echo $depositYear ?>) {
            opt.selected = true;
        };
        opt.value = i;
        opt.name = "depositYear";
        opt.innerHTML = i;

        yearsDropDown.appendChild(opt);
    }
    /*************************************** */

    //Reset the customer form:
    let clearBtn = document.getElementById("clearBtn");

    clearBtn.addEventListener("click", function(e) {

        e.preventDefault();

        document.getElementById('customerForm').reset();
        document.getElementById('principalAmount').value = null;

        let allTxtInputs = document.querySelectorAll('input');
        allTxtInputs.forEach((input) => {

            input.value = null;
        })
    });
</script>


</html>