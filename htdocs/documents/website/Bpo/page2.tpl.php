<?php // BEGIN PHP
$websitekey=basename(__DIR__); if (empty($websitepagefile)) $websitepagefile=__FILE__;
if (! defined('USEDOLIBARRSERVER') && ! defined('USEDOLIBARREDITOR')) {
	$pathdepth = count(explode('/', $_SERVER['SCRIPT_NAME'])) - 2;
	require_once ($pathdepth ? str_repeat('../', $pathdepth) : './').'master.inc.php';
} // Not already loaded
require_once DOL_DOCUMENT_ROOT.'/core/lib/website.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/website.inc.php';
ob_start();
// END PHP ?>
<html lang="fr">
<head>
<title>Contact us</title>
<meta charset="utf-8">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="robots" content="index, follow" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="" />
<meta name="title" content="Contact us" />
<meta name="description" content="" />
<meta name="generator" content="Dolibarr 18.0.4 (https://www.dolibarr.org)" />
<meta name="dolibarr:pageid" content="2" />
<link rel="canonical" href="/contact.php" />
<?php if ($_SERVER["PHP_SELF"] == "/contact.php") { ?>
<link rel="alternate" hreflang="fr" href="<?php echo $website->virtualhost; ?>/contact.php" />
<?php } ?>
<?php if ($website->use_manifest) { print '<link rel="manifest" href="/manifest.json.php" />'."\n"; } ?>
<!-- Include link to CSS file -->
<link rel="stylesheet" href="/styles.css.php?website=<?php echo $websitekey; ?>" type="text/css" />
<!-- Include link to JS file -->
<script nonce="a59c65eb" async src="/javascript.js.php"></script>
<!-- Include HTML header from common file -->
<?php if (file_exists(DOL_DATA_ROOT."/website/".$websitekey."/htmlheader.html")) include DOL_DATA_ROOT."/website/".$websitekey."/htmlheader.html"; ?>
<!-- Include HTML header from page header block -->

</head>
<!-- File generated by Dolibarr website module editor -->
<body id="bodywebsite" class="bodywebsite bodywebpage-contact">
<!-- Enter here your HTML content. Add a section with an id tag and tag contenteditable="true" if you want to use the inline editor for the content  -->
<?php 
if (GETPOST('action') == 'sendmail')    {
    include_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
    $from = GETPOST('email', 'alpha');
    $to = $mysoc->email;
    $message = GETPOST('message', 'alpha');
    $cmail = new CMailFile('Contact from website', $to, $from, $message);
    if ($cmail->sendfile()) {
        ?>
        <script>
            alert("Message sent successfully !");
        </script>
        <?php
	} else {
		echo $langs->trans("ErrorFailedToSendMail", $from, $to).'. '.$cmail->error;
	}
}
?>

<?php includeContainer('header'); ?>


<section id="mysection1" contenteditable="true">
    <main>
            <header class="site-header site-contact-header">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-10 col-12 mx-auto">
                            <h1 class="text-white">Say Hi</h1>

                            <strong class="text-white"
                                >We are happy to get in touch with you</strong
                            >
                        </div>
                    </div>
                </div>

                <div class="overlay"></div>
            </header>

            <a id="reservation"></a><br>
            
            <section class="contact section-padding">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="mb-4">Leave a message</h2>
                        </div>

                        <div class="col-lg-6 col-12">
                            <form
                                class="custom-form contact-form row"
                                action="#"
                                method="post"
                                role="form"
                            >
                                <input type="hidden" name="action" value="sendmail">
                                <input type="hidden" name="toekn" value="<?php echo newToken(); ?>">
                                
                                <div class="col-lg-6 col-6">
                                    <label for="contact-name" class="form-label"
                                        >Full Name</label
                                    >

                                    <input
                                        type="text"
                                        name="contact-name"
                                        id="contact-name"
                                        class="form-control"
                                        placeholder="Your Name"
                                        required
                                    />
                                </div>

                                <div class="col-lg-6 col-6">
                                    <label
                                        for="contact-phone"
                                        class="form-label"
                                        ><?php echo $weblangs->trans("Phone"); ?></label
                                    >

                                    <input
                                        type="telephone"
                                        name="contact-phone"
                                        id="contact-phone"
                                        class="form-control"
                                    />
                                </div>

                                <div class="col-12">
                                    <label
                                        for="contact-email"
                                        class="form-label"
                                        ><?php echo $weblangs->trans("Email"); ?></label
                                    >

                                    <input
                                        type="email"
                                        name="contact-email"
                                        id="contact-email"
                                        pattern="[^ @]*@[^ @]*"
                                        class="form-control"
                                        placeholder="Your Email"
                                        required=""
                                    />

                                    <label
                                        for="contact-message"
                                        class="form-label"
                                        ><?php echo $weblangs->trans("Message"); ?></label
                                    >

                                    <textarea
                                        class="form-control"
                                        rows="5"
                                        id="contact-message"
                                        name="contact-message"
                                        placeholder="Your Message"
                                    ></textarea>
                                </div>

                                <div class="col-lg-5 col-12 ms-auto">
                                    <button type="submit" class="form-control">
                                        <?php echo $weblangs->trans("Send"); ?>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="col-lg-4 col-12 mx-auto mt-lg-5 mt-4">
                            <h5>Weekdays</h5>

                            <div class=" mb-lg-3">
                                 <?php $days = ["MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY"];
                                   foreach ($days as $day){
                            echo "<p> $day : " .getDolGlobalString("MAIN_INFO_OPENINGHOURS_$day")  ."</p>";    
                        }
                        ?>
                            </div>

                            <h5>Weekends</h5>

                            <div class="d-flex">
                                <p>Saturday and Sunday</p>

                                <p class="ms-5">to be determined !</p>
                            </div>
                        </div>

                        <div class="col-12" id="divaddress">
                            <br><br>
                        
                            <h4 class="mt-5 mb-4 center">
                                <?php echo $mysoc->getFullAddress() ?>
                            </h4>

                            <!-- Google MAPS -->
                            <center><div class="mapouter"><div class="gmap_canvas"><iframe width="600" height="500" id="gmap_canvas" src="https://maps.google.com/maps?q=<?php echo urlencode($mysoc->getFullAddress()); ?>&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                            </div>
                            <style>.mapouter{text-align:right;height:500px;width:600px;}.gmap_canvas {overflow:hidden;background:none!important;height:500px;width:600px;}</style>
                            </div></center>
                        </div>
                    </div>
                </div>
            </section>
        </main>


        
        <!-- Modal -->
        <div
            class="modal fade"
            id="BookingModal"
            tabindex="-1"
            aria-labelledby="BookingModal"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="mb-0">Reserve a table</h3>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>

                    <div
                        class="modal-body d-flex flex-column justify-content-center"
                    >
                        <div class="booking">
                            <form
                                class="booking-form row"
                                role="form"
                                action="contact.php"
                                method="POST"
                            >
                                <input type="hidden" name="token" value="<?php echo newToken(); ?>" />
                                <input type="hidden" name="action" value="sendmail">
                                <div class="col-lg-6 col-12">
                                    <label for="name" class="form-label"
                                        >Full Name</label
                                    >

                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        class="form-control"
                                        placeholder="Your Name"
                                        required
                                    />
                                </div>

                                <div class="col-lg-6 col-12">
                                    <label for="email" class="form-label"
                                        >Email Address</label
                                    >

                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        pattern="[^ @]*@[^ @]*"
                                        class="form-control"
                                        placeholder="your@email.com"
                                        required
                                    />
                                </div>

                                <div class="col-lg-6 col-12">
                                    <label for="phone" class="form-label"
                                        >Phone Number</label
                                    >

                                    <input
                                        type="telephone"
                                        name="phone"
                                        id="phone"
                                        class="form-control"
                                    />
                                </div>

                                <div class="col-lg-6 col-12">
                                    <label for="people" class="form-label"
                                        >Number of persons</label
                                    >

                                    <input
                                        type="text"
                                        name="people"
                                        id="people"
                                        class="form-control"
                                        placeholder="12 persons"
                                    />
                                </div>

                                <div class="col-lg-6 col-12">
                                    <label for="date" class="form-label"
                                        >Date</label
                                    >

                                    <input
                                        type="date"
                                        name="date"
                                        id="date"
                                        value=""
                                        class="form-control"
                                    />
                                </div>

                                <div class="col-lg-6 col-12">
                                    <label for="time" class="form-label"
                                        >Time</label
                                    >

                                    <select
                                        class="form-select form-control"
                                        name="time"
                                        id="time"
                                    >
                                        <option value="5" selected>
                                            5:00 PM
                                        </option>
                                        <option value="6">6:00 PM</option>
                                        <option value="7">7:00 PM</option>
                                        <option value="8">8:00 PM</option>
                                        <option value="9">9:00 PM</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="message" class="form-label"
                                        >Special Request</label
                                    >

                                    <textarea
                                        class="form-control"
                                        rows="4"
                                        id="message"
                                        name="message"
                                        placeholder=""
                                    ></textarea>
                                </div>

                                <div class="col-lg-4 col-12 ms-auto">
                                    <button type="submit" class="form-control">
                                        Submit Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

</section>


<?php includeContainer('footer'); ?>


</body>
</html>
<?php // BEGIN PHP
$tmp = ob_get_contents(); ob_end_clean(); dolWebsiteOutput($tmp, "html", 2); dolWebsiteIncrementCounter(3, "page", 2);
// END PHP ?>
