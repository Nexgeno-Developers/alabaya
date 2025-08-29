<?php
/*
Widget-title: Right company contact
Widget-preview-image: /assets/img/widgets_preview/right_contact.jpg
 */
?>

<div class="row">
    <div class="col-md-12">
        <div class="section-title-wr">
            <h3 class="section-title left"><span>{lang_Info}</span></h3>
        </div>
        <div class="contact-info">
            <p>
                <span style="font-weight: bold;">
                    {settings_address_footer}
                </span>
                <br>
                <span style="font-weight: bold;">
                    <span><?php echo _l('Tel');?>:</span>
                </span> <?php echo $settings_phone; ?><br>
                							<?php if(isset($settings_fax))							{ ?>
                <span style="font-weight: bold;">
                    <span><?php echo _l('Fax');?>:</span>
                </span> <?php echo $settings_fax; ?> <br>
							<?php } ?>
                <span style="font-weight: bold;">
                    <span><?php echo _l('Mail');?>:</span>
                </span> <?php echo $settings_email; ?>
            </p>
        </div>
    </div>
</div>
<div class="section-title-wr">
    <h3 class="section-title left"><span><?php _l('Stay connected');?></span></h3>
</div>
<p>
</p>
<div class="social-media">
   <a href="#" data-url='{homepage_url}' data-title='{settings_websitetitle}' class="btn-share-fb"><i class="fa fa-facebook facebook"></i></a>
   <a href="#" data-url='{homepage_url}'  class="btn-share-google-plus"><i class="fa fa-google-plus google"></i></a>
   <a href="#" data-url='{homepage_url}' data-title='{settings_websitetitle}' class="btn-share-tw"><i class="fa fa-twitter twitter"></i></a>
</div>