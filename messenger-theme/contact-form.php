<?php
$contact_count = 0;

function renderFormContents($errs, $vals) {
    $messageError = '';
    if (isset($errs['message'])) {
        $messageError = $errs['message'];
    }
    $messageErrorClass = "hidden";
    if (!empty($messageError)) {
        $messageErrorClass = "";
    }

    $emailError = '';
    if (isset($errs['email'])) {
        $emailError = $errs['email'];
    }
    $emailErrorClass = "hidden";
    if (!empty($emailError)) {
        $emailErrorClass = "";
    }

    $messageTouched = "";
    $emailTouched = "";
    $nameTouched = "";
    $phoneTouched = "";
    if (strlen($vals['message']) > 0) {
        $messageTouched = "touched";
    }
    if (strlen($vals['email']) > 0) {
        $emailTouched = "touched";
    }
    if (strlen($vals['name']) > 0) {
        $nameTouched = "touched";
    }
    if (strlen($vals['phone']) > 0) {
        $phoneTouched = "touched";
    }

    ?><div class="messenger_contact_field">
        <label class="hidden" for="message">Message</label>
        <textarea required class="touch-track p-[12px] w-full min-h-[96px] my-[6px] <?php echo $messageTouched; ?>" name="message" placeholder="Enter your message here"><?php echo $vals['message']; ?></textarea>
        <label class="error f-message <?php echo $messageErrorClass; ?>" for="message"><?php echo $messageError; ?></label>
    </div>
    <div class="messenger_contact_field">
        <label class="hidden" for="name">Name</label>
        <input class="touch-track w-full my-[6px] px-[12px] py-[6px] <?php echo $nameTouched; ?>" type="text" name="name" placeholder="Enter your name here (optional)" value="<?php echo $vals['name']; ?>" />
    </div>
    <div class="messenger_contact_field">
        <label class="hidden" for="email">Email</label>
        <input required class="touch-track w-full my-[6px] px-[12px] py-[6px] <?php echo $emailTouched; ?>" type="email" name="email" placeholder="Enter your email here (required)" value="<?php echo $vals['email']; ?>"/>
        <label class="error f-name <?php echo $messageErrorClass; ?>" for="email"><?php echo $emailError; ?></label>
    </div>
    <div class="messenger_contact_field">
        <label class="hidden" for="phone">Phone</label>
        <input class="touch-track w-full my-[6px] px-[12px] py-[6px] <?php echo $phoneTouched; ?>" type="text" name="phone"  placeholder="Enter your phone here (optional)" value="<?php echo $vals['phone']; ?>"/>
    </div>
    <?php 
}

function makeHoneyPot() {
    ?>
        <div style="display:none;">
            <label class="hidden" for="address">Address</label>
            <input type="text" name="address" />
        </div>
    <?php
}

function loadRecaptcha() {
 ?>
 <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
 <?php 
}

function renderButton() {
    ?><div class="messenger_contact_field">
        <input class="w-full my-[6px] p-[12px] text-[18px] w-[50%] rounded-sm" type="submit" value="Send" />
        <span class="error-submit" id="contact-submit-error"></span>
        <progress style="opacity:0;"></progress>
    </div><?php
}

function renderRecaptchaButton() {
    global $contact_count;
    $funcName = "onSubmitCaptcha".$contact_count;
    ?><div class="keep">
    <script>
        function <?php echo $funcName; ?>(token) {
            document.getElementById("messenger_contact_<?php echo $contact_count;?>").submit();
        }
    </script>
    <input type="submit" value="Send" class="g-recaptcha w-full my-[6px] p-[12px] text-[18px] w-[50%] rounded-sm"
        data-sitekey="6Le2NhgtAAAAAB_YkysmXyukYMG8WTEIOQZ_rhqb"
        data-callback='<?php echo $funcName; ?>'
        data-action='submit' />
    </div><?php
}

function renderContactForm($args = []) {
    global $contact_count;
    $formId = "messenger_contact_".++$contact_count;
    $isRecaptcha = isset($args['protection']) && $args['protection'] == "recaptcha";
    $vals = array('message' => '', 'email' => '', 'name' => '', 'phone' => '');
    ob_start();
    ?>
    <div class="messenger_contact_form_container px-[16px] py-[24px]">
        <?php if ($isRecaptcha) {loadRecaptcha();} ?>
        <h3 class="text-[24px] font-bold">Send us a message</h3>
        <form id="<?php echo $formId; ?>">
            <?php if ($isRecaptcha) {echo '<div class="g-recaptcha" data-sitekey="6Le2NhgtAAAAAB_YkysmXyukYMG8WTEIOQZ_rhqb" data-action="CONTACT"></div>';} ?>
            <?php if(isset($args['protection']) && $args['protection'] == "honey") {makeHoneyPot(); }?>
            <div class="messenger-update-container">
                <?php renderFormContents([], $vals); ?>
            </div>
            <?php renderButton(); ?>
        </form>
        <script>
            <?php if ($isRecaptcha) {
                echo "const isRecaptcha = true;";
            } else echo "const isRecaptcha = false;" ?>
            (function() {
                const form = document.getElementById('<?php echo $formId; ?>');
                
                form.addEventListener('submit', async (e) => {
                    const progress = document.querySelector('#<? echo $formId; ?> progress');
                    const submitBut = document.querySelector('#<? echo $formId; ?> input[type="submit"]');
                    e.preventDefault();
                    submitBut.setAttribute('disabled', true);
                    const reqFormFields = document.querySelectorAll('#<? echo $formId; ?> *:required');
                    let errs = [];
                    reqFormFields.forEach(f => {
                        if (f.value == "" || f.value == null) {
                            errs.push({field: f.name, err: `${f.name} is required`});
                        }
                    });
                    console.log(errs);
                    if (errs.length) {
                        const errEls = document.querySelectorAll('#<?php echo $formId; ?> label.error');
                        errEls.forEach(el => {
                            errs.forEach(
                                er => {
                                    console.log(er);
                                    if (el.className.contains(`f-${er.field}`)) {
                                        el.innerHtml = er.err;
                                        el.classList.remove('hidden');
                                    }
                                }
                            );
                        });
                        submitBut.removeAttribute('disabled');
                        return;
                    }
                    const url = `/wp-admin/admin-ajax.php?action=messenger_send_contact`;
                    console.log(e);
                    const bodyLength = 5;// isRecaptcha ? 5 : 4;
                    const body = Array.from(e.target)
                        .slice(0,bodyLength)
                        .reduce((p, c) => {
                            p[c.name] = c.value;
                            return p;
                        },
                        {});
                    console.log(body);
                    if (!body['g-recaptcha-response'] && isRecaptcha) {
                        document.getElementById('contact-submit-error').innerHTML = "Recaptcha error";
                        return;
                    }
                    fetch(
                        url,
                        {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify(body)
                        }
                    ).then(response => {
                        if (response.status >= 500) {
                            form.innerHTML = '<div class="messenger-contact-error"><p>There was an error submitting your message. Please try again.</p></div>';
                        }
                        response.text().then(result => {
                            submitBut.removeAttribute('disabled');
                            if (!result.includes('error')) {
                                form.innerHTML = result;
                                return;
                            }
                            form.querySelector('.messenger-update-container').innerHTML = result;
                            const formFields = document.querySelectorAll('#<? echo $formId; ?> .touch-track');
                            formFields.forEach(f => {
                                f.addEventListener('input', () => f.classList.add('touched'));
                            });
                            
                        });
                        
                        progress.style.opacity = '0';
                    });

                    progress.style.opacity = '1';
                });

                const formFields = document.querySelectorAll('#<? echo $formId; ?> .touch-track');
                formFields.forEach(f => {
                    f.addEventListener('input', () => f.classList.add('touched'));
                });
            })();
        </script>
    </div>
    <?php
    return ob_get_clean();
}

function sanitiseContactForm($postContents) {
    if (isset($postContents['address']) && $postContents['address'] != "") {
        return false;
    }
    $sanitised = array();
    $sanitised['name'] = strip_tags($postContents['name']);
    $sanitised['email'] = strip_tags($postContents['email']);
    $sanitised['message'] = strip_tags($postContents['message']);
    $sanitised['phone'] = strip_tags($postContents['phone']);
    return $sanitised;
}

function validateContactForm($postContents) {
    $errs = array();

    if (strlen($postContents['message']) > 1000) {
        $errs['message'] = "Message is too long - max length 1000 characters.";
    }
    if (strlen($postContents['message']) == 0) {
        $errs['message'] = "Your message is empty - no point messaging if you have nothing to say!";
    }
    if (!filter_var($postContents['email'], FILTER_VALIDATE_EMAIL)) {
        $errs['email'] = "Email address is invalid";
    }

    return $errs;
}

function processContactForm() {
    global $wpdb;
    
    $postContents = json_decode(file_get_contents('php://input'), true);
    
    if (isset($postContents['g-recaptcha-response'])) {
        $captchaResp = processCaptcha($postContents['g-recaptcha-response']);
    }

    $postContents = sanitiseContactForm($postContents);

    if (!$postContents) {
        $vals = array('message' => '', 'email' => '', 'name' => '', 'phone' => '');
        echo renderFormContents([], $vals);
        exit();
    }
    $errs = validateContactForm($postContents);
    if (!empty($errs)) {
        echo renderFormContents($errs, $postContents);
        exit();
    }
    $contactEmail = $postContents['email'];
    $contactMessage = $postContents['message'];
    $contactName = strip_tags($postContents['name']);
    $contactPhone = $postContents['phone'];

    sendContactEmail($contactEmail, $contactMessage, $contactPhone, $contactName);
    sendUserContactEmail($contactEmail, $contactMessage, $contactPhone, $contactName);

    echo '<div class="messenger-contact-success"><p>Thank you, '.$contactName.'. Your message has been received.</p></div>';
    //echo '<div style="display:none">';var_dump($captchaResp); echo '</div>';
    exit();
}

function sendUserContactEmail($contactEmail, $contactMessage, $contactPhone, $contactName) {
    $query = new WP_Query( array( 'post_type'=>'email_template', 'meta_value'=>'user') );

    if (empty($contactName)) {
        $contactName = $contactEmail;
    }
    $siteEmail = get_bloginfo('admin_email');
    $siteName = get_bloginfo('name');
    $to = $contactEmail;
    
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: '.$siteName.' <'.$siteEmail.'>' . "\r\n" .
    'Reply-To: '.$contactEmail. "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    
    if (!$query->have_posts()) {
        $subject = "$siteName - new contact from $contactName";
        $message = "
            <html>
            <head>
              <title>Thank you $contactName, we have received your message</title>
            </head>
            <body>
              <p>Dear $contactName,</p>
              <p>We have received a contact form submission with the following details:</p>
              <p><strong>Message:</strong> $contactMessage </p>
              <p><strong>Name:</strong> $contactName </p>
              <p><strong>Phone:</strong> $contactPhone </p>
              <p><strong>Email:</strong> $contactEmail </p>
            </body>
            </html>
        ";
        
    } else {
        $query->the_post();
        
        $subject = "$siteName: ".str_replace('###name###', $contactName, get_the_title() );
        
        $content = str_replace('###name###', $contactName, apply_filters( 'the_content', get_the_content() ) );
        $content = str_replace('###phone###', $contactPhone, $content);
        $content = str_replace('###email###', $contactEmail, $content);
        $content = str_replace('###message###', $contactMessage, $content);
        
        $message = str_replace( ']]>', ']]&gt;', $content );
        
        $message = str_replace('}', "}\r\n", $message);
        $message = str_replace('</p>', "</p>\r\n", $message);
    }


    $emailresult = mail($to, $subject, $message, $headers);
}

function sendContactEmail($contactEmail, $contactMessage, $contactPhone, $contactName) {
    $query = new WP_Query( array( 'post_type'=>'email_template', 'meta_value'=>'admin') );

    if (empty($contactName)) {
        $contactName = $contactEmail;
    }
    $siteEmail = get_bloginfo('admin_email');
    $siteName = get_bloginfo('name');
    $to = $siteEmail;
    
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: '.$siteName.' <'.$siteEmail.'>' . "\r\n" .
    'Reply-To: '.$contactEmail. "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    
    if (!$query->have_posts()) {
        $subject = "$siteName - new contact from $contactName";
        $message = "
            <html>
            <head>
              <title>Contact Message Received</title>
            </head>
            <body>
              <p>Dear  </p>
              <p>A contact form submission has been received:</p>
              <p> $contactMessage </p>
              <p>Name: $contactName </p>
              <p>Phone: $contactPhone </p>
              <p>Email: $contactEmail </p>
            </body>
            </html>
        ";
        
    } else {
        $query->the_post();
        
        $subject = "$siteName: ".str_replace('###name###', $contactName, get_the_title() );
        
        $content = str_replace('###name###', $contactName, apply_filters( 'the_content', get_the_content() ) );
        $content = str_replace('###phone###', $contactPhone, $content);
        $content = str_replace('###email###', $contactEmail, $content);
        $content = str_replace('###message###', $contactMessage, $content);
        
        $message = str_replace( ']]>', ']]&gt;', $content );
        
        $message = str_replace('}', "}\r\n", $message);
        $message = str_replace('</p>', "</p>\r\n", $message);
    }


    $emailresult = mail($to, $subject, $message, $headers);
}

function processCaptcha($token) {

    $postData = array('event' => array(
        'token' => $token,
        /*'exectedAction' => "USER_ACTION",*/
        'siteKey' => "6Le2NhgtAAAAAB_YkysmXyukYMG8WTEIOQZ_rhqb"
    ));

    $api_key = "AIzaSyCQm2Da8xnQ2rU97v1A4fdX65dl4ILLfi4";
    // Setup cURL
    $ch = curl_init('https://recaptchaenterprise.googleapis.com/v1/projects/recaptcha-migrated-ad8676b9406/assessments?key='.$api_key);
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Authorization: '.$authToken,
            'Content-Type: application/json'
        ),
        CURLOPT_POSTFIELDS => json_encode($postData)
    ));

    // Send the request
    $response = curl_exec($ch);

    // Check for errors
    if($response === FALSE){
        die(curl_error($ch));
    }

    // Decode the response
    $responseData = json_decode($response, TRUE);

    // Close the cURL handler
    curl_close($ch);

    // Print the date from the response
    return $responseData;
}

add_shortcode('messenger_contact', 'renderContactForm');

add_action('wp_ajax_messenger_send_contact', 'processContactForm');
add_action('wp_ajax_nopriv_messenger_send_contact', 'processContactForm');
add_action('wp_ajax_messenger_recaptcha', 'processCaptcha');