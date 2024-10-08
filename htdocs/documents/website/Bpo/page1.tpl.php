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
<title>About us</title>
<meta charset="utf-8">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="robots" content="index, follow" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="" />
<meta name="title" content="About us" />
<meta name="description" content="" />
<meta name="generator" content="Dolibarr 18.0.4 (https://www.dolibarr.org)" />
<meta name="dolibarr:pageid" content="1" />
<link rel="canonical" href="/about.php" />
<?php if ($_SERVER["PHP_SELF"] == "/about.php") { ?>
<link rel="alternate" hreflang="fr" href="<?php echo $website->virtualhost; ?>/about.php" />
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
<body id="bodywebsite" class="bodywebsite bodywebpage-about">
<!-- Enter here your HTML content. Add a section with an id tag and tag contenteditable="true" if you want to use the inline editor for the content  -->
<?php includeContainer('header'); ?>

<section id="mysection1" contenteditable="true">
        <main>

            <header class="site-header site-about-header">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-10 col-12 mx-auto">
                            <h1 class="text-white">À propos de nous</h1>

                            <strong class="text-white">Créée au cœur de Safi en 1994,</strong>
                            <strong class="text-white">notre boulangerie et pâtisserie familiale vous propose un large choix de pains, viennoiseries et pâtisseries de qualité. Chez nous, chaque produit est fait avec passion et savoir-faire, en utilisant des ingrédients frais et de première qualité.</strong>
                        </div>

                    </div>
                </div>

                <div class="overlay"></div>
            </header>           

            <section class="about section-padding">
                <div class="container">
                    <div class="row">

                        <div class="col-12">
                            <h2 class="mb-5">l'équipe dirigeante</h2>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="team-thumb">
                                <img src="image/Bpo/team/matthew-hamilton-tNCH0sKSZbA-unsplash.jpg" class="img-fluid team-image" alt="">
                                
                                <div class="team-info">
                                    <h4 class="mt-3 mb-0">Ahmed</h4>

                                    <p>CEO &amp; Founder</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12 my-lg-0 my-4">
                            <div class="team-thumb">
                                <img src="image/Bpo/team/nicolas-horn-MTZTGvDsHFY-unsplash.jpg" class="img-fluid team-image" alt="">

                                <h4 class="mt-3 mb-0">Mohamed</h4>

                                <p>Senior Chef</p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="team-thumb">
                                <img src="image/Bpo/team/rc-cf-FMh5o5m5N9E-unsplash.jpg" class="img-fluid team-image" alt="">
                                
                                <h4 class="mt-3 mb-0">Hassan</h4>

                                <p>Senior Chef</p>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
            
        </main>

</section>


<?php includeContainer('footer'); ?>


</body>
</html>
<?php // BEGIN PHP
$tmp = ob_get_contents(); ob_end_clean(); dolWebsiteOutput($tmp, "html", 1); dolWebsiteIncrementCounter(3, "page", 1);
// END PHP ?>
