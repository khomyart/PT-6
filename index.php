<?php
    $feedbackData = [];

    /**
     * Returns TRUE if current request is POST, otherwise FALSE
     *
     * @return bool
     */
    function isPostRequest() {
        return defined('IS_POST_REQUEST');
    }

    /**
     * Returns value of a given field
     *
     * @param string $field
     * @return string
     */
    function getFieldValue($field) {
        global $feedbackData;
        global $product;
        if (isset($feedbackData[$field])) {
            return $product[$field];
        } else {
            return '';
        }
    }

    /**
     * Returns a css class for a given field
     *
     * @param array $feedbackData
     * @param string $field
     * @return string
     */
    function getFeedbackClass(&$feedbackData, $field) {
        if (!isPostRequest()) {
            return '';
        }

        return isset($feedbackData[$field]) ? 'is-invalid' : 'is-valid';
    }

    /**
     * Returns a feedback block for a given field
     *
     * @global array $feedbackData
     * @param string $field
     * @return string
     */
    function getFeedbackBlock($field) {
        global $feedbackData;

        if (!isPostRequest()) {
            return '';
        }

        if (isset($feedbackData[$field])) {
            return '<div class="invalid-feedback">'.$feedbackData[$field].'</div>';
        } else {
            return '<div class="valid-feedback">Looks good!</div>';
        }
    }

    /**
    *
    * Checks a string for unecceptable symbols, where allowed symbols are 
    * located in "@param array $comparation_symbols". If found not allowed  
    * symbol - print "@param string $error_message" under given field
    *
    * @global array $product
    * @param string $field
    * @param array $comparation_symbols
    * @param string $error_message
    *
    */
    function fieldSymbolsValidation($field, $comparation_symbols, $error_message){
        global $product;
        $splited = str_split($product[$field]);
        foreach ($splited as $index) {
            foreach ($comparation_symbols as $symbol) {
                $symbol_validness = 0;
                if ($index === $symbol){
                    $symbol_validness = 1;
                    break;
                }
            }
            if($symbol_validness===0) {
                addFeedback($field, $error_message);
                break;
            };
        }
    }

    /**
     * Is a helper method for adding feedback for a particular field
     *
     * @global array $feedbackData
     * @param string $field
     * @param string $feedback
     */
    function addFeedback($field, $feedback) {
        global $feedbackData;

        $feedbackData[$field] = $feedback;
    }

    if (!empty($_POST['product'])) {
        define('IS_POST_REQUEST', true);

        $product = $_POST['product'];

        if (strlen($product['title']) < 15) {
            addFeedback('title', 'Min. length is 15 symbols');
        }

        if (strlen($product['sdesc']) < 20) {
            addFeedback('sdesc', 'Min. length is 20 symbols');
        }

        if (strlen($product['desc']) < 25) {
            addFeedback('desc', 'Min. length is 25 symbols');
        }

        if ($product['min-price'] <= 0) {
            addFeedback('min-price', 'Min. price must be more than 0');
        }

        if ($product['min-stay'] < 0 
        || $product['min-stay'] > $product['max-stay'] 
        || empty($product['min-stay'])) {
            addFeedback('min-stay', 'Min. stay is not on available value');
        }

        if ($product['max-stay'] < 0 
        || $product['max-stay'] < $product['min-stay'] 
        || empty($product['max-stay'])) {
            addFeedback('max-stay', 'Max. stay is not on available value');
        }

        if ($product['c-policy'] === "0" ) {
            addFeedback("c-policy", 'No empty allowed');
        }

        if (strlen($product['nettwerk']) < 3 ) {
            addFeedback("nettwerk", 'Min. length is 3 symbols');
        }

        if (strlen($product['nettwerk-pass']) < 3 ) {
            addFeedback("nettwerk-pass", 'Password length cannot be less than 3 symbols!');
        }

        if ((stristr($product['mail'], '@') === FALSE) 
        || (strlen($product['mail']) < 4))  {
            addFeedback("mail", 'Email address length is not enough (3 symbols min) or symbol "@" is not here');
        }

        if(strlen($product["phone"])<7) {
            addFeedback("phone", 'Min. length is 7 symbols');
        }
        
        $check_symbols = ["","0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "+", "(", ")"];

        fieldSymbolsValidation("phone",  $check_symbols, 'Not allowed symbols in phone number');
    }
?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" 
            href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
            integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" 
            crossorigin="anonymous">

        <title>Property Data</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <br>
        <div class="container">
            <div class="row">
                <div class="col">
                    <h2 class="text-center">Property Data</h2>
                    <form action="index.php" method="post">
                        <!-- Descriptions -->
                        <div class="section-header">Description</div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="input-title">Title</label>
                                <input type="text" 
                                    class="form-control <?= getFeedbackClass($feedbackData, 'title');?>" 
                                    id="input-title" name="product[title]" 
                                    placeholder="Title" 
                                    value="<?= getFieldValue('title')?>">
                                <?= getFeedbackBlock('title'); ?>
                            </div>

                            <div class="form-group col-12">
                                <label for="input-sdesc">Short Description</label>
                                <input type="text" 
                                    class="form-control <?= getFeedbackClass($feedbackData, 'sdesc'); ?>" 
                                    id="input-sdesc" name="product[sdesc]" 
                                    placeholder="Short Description" 
                                    value="<?= getFieldValue('sdesc')?>">
                                <?= getFeedbackBlock('sdesc'); ?>
                            </div>

                            <div class="form-group col-12">
                                <label for="input-desc">Description</label>
                                <textarea class="form-control <?= getFeedbackClass($feedbackData, 'desc'); ?>" 
                                        id="input-desc" name="product[desc]" 
                                        cols="30" rows="5" placeholder="Description"><?= getFieldValue('desc')?></textarea>
                                <?= getFeedbackBlock('desc'); ?>
                            </div>
                        </div>
                        <!-- WiFi section -->
                        <div class="section-header">Restrictions</div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="input-min-price">Min. price</label>
                                <input type="number" 
                                    class="form-control  <?= getFeedbackClass($feedbackData, 'min-price'); ?>" 
                                    id="input-min-price"  name="product[min-price]" placeholder="Min. price" 
                                    value="<?= getFieldValue('min-price')?>">
                                <?= getFeedbackBlock('min-price'); ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="input-min-stay">Min. stay</label>
                                <input type="number" 
                                    class="form-control <?= getFeedbackClass($feedbackData, 'min-stay'); ?>" 
                                    id="input-min-stay" name="product[min-stay]" placeholder="Min. stay" 
                                    value="<?= getFieldValue('min-stay')?>">
                                <?= getFeedbackBlock('min-stay');?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="input-max-stay">Max. stay</label>
                                <input type="number" 
                                    class="form-control <?= getFeedbackClass($feedbackData, 'max-stay'); ?>" 
                                    id="input-max-stay" name="product[max-stay]" placeholder="Max. stay" 
                                    value="<?= getFieldValue('max-stay')?>">
                                <?= getFeedbackBlock('max-stay'); ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="select-c-policy">Cancellation Policy</label>
                                <select class="form-control <?= getFeedbackClass($feedbackData, 'c-policy'); ?>" 
                                        id="select-c-policy" name="product[c-policy]" 
                                        value="<?= getFieldValue('c-policy')?>">
                                    <option value="0">Empty</option>
                                    <option value="1">Flexible: Full refund 1 day prior to arrival, except fees</option>
                                    <option value="3">Moderate: Full refund 5 days prior to arrival, except fees</option>
                                    <option value="11">Strict Non Refundable</option>
                                    <option value="21">Partially Refundable</option>
                                    <option value="22">Partially Refundable 14 Days prior</option>
                                </select>
                                <?= getFeedbackBlock('c-policy'); ?>
                            </div>
                            
                        </div>

                        <!-- WiFi section -->
                        <div class="section-header">Wi-Fi</div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="input-wifi-network">Network</label>
                                <input type="text" 
                                    class="form-control <?= getFeedbackClass($feedbackData, 'nettwerk'); ?>"
                                    name="product[nettwerk]" id="input-wifi-network" placeholder="Network" 
                                    value="<?= getFieldValue('nettwerk')?>">
                                <?= getFeedbackBlock('nettwerk'); ?>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="input-wifi-password">Password</label>
                                <input type="text" 
                                    class="form-control <?= getFeedbackClass($feedbackData, 'nettwerk-pass'); ?>" 
                                    name="product[nettwerk-pass]" id="input-wifi-password" placeholder="Password" 
                                    value="<?= getFieldValue('nettwerk-pass')?>">
                                <?= getFeedbackBlock('nettwerk-pass'); ?>
                            </div>

                        </div>

                        <!-- Owner info -->
                        <div class="section-header">Owner data</div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="input-owner-email">Email</label>
                                <input type="email" 
                                    class="form-control <?= getFeedbackClass($feedbackData, 'mail'); ?>" 
                                    id="input-owner-email" name="product[mail]" placeholder="Owner's email" 
                                    value="<?= getFieldValue('mail')?>">
                                <?= getFeedbackBlock('mail'); ?>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="input-owner-phone">Phone</label>
                                <input type="text" 
                                    class="form-control <?= getFeedbackClass($feedbackData, 'phone'); ?>" 
                                    id="input-owner-phone" name="product[phone]" placeholder="Owner's phone" 
                                    value="<?= getFieldValue('phone')?>">
                                <?= getFeedbackBlock('phone'); ?>
                            </div>
                            
                        </div>

                        <hr>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Save data</button>
                        </div>
                    </form>
                    <br /><br />
                </div>
            </div>
        </div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
                integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" 
                crossorigin="anonymous">
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" 
                integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" 
                crossorigin="anonymous">
        </script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" 
                integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" 
                crossorigin="anonymous">
        </script>   
    </body>
</html>
