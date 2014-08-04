<div class="primary-wide optin_main" role="main">
<!-- Begin Optin form -->
<div id="optinformpage">
<div class="row">
<div class="col-xs-1">
<div class="optin_arrow"></div>
</div>
<div class="col-xs-10">
<form class="optin" action="https://s1851.t.eloqua.com/e/f2" method="post" target="eloqua-submit" name="optinform" id="optinform">
<p>Sign up for our email list below. We'll keep you up to date on the categories of your choice.</p><label class="hidefromscreen" for="emailAddress">Email Address:</label><input id="optin_email" name="emailAddress" required="" type="email" placeholder="Email Address" /><br><br>
<label class="checkbox" for="industry"><strong>Industry categories you are interested in:</strong><br>
<input name="industry" type="radio" value="Media" /> Media<br>
<input name="industry" type="radio" value="Not For Profit" /> Nonprofit<br>
<input name="industry" type="radio" value="Utilities" /> Utilities<br>
<input name="industry" type="radio" value="Other" /> Other</label><br>
<hr class="fadeline" />
<label class="checkbox" for="legal"><input name="OptInCheckbox" type="checkbox" value="yes" required="true" /> By checking this box, I authorize CDS Global to contact me via the email address supplied about CDS Global products and services, including product releases, updates, seminars, events, surveys, trainings and special offers.</label>
<input type="hidden" name="PermissionDate" value="<?php echo date("m/d/Y"); ?>" />
<input type="hidden" name="elqSource" value="Opt_In_Form">
<input type="hidden" name="LeadSource" value="Website_Opt_In">
<input type="hidden" name="LeadSourceName" value="CDS-Global.com">
<input type="hidden" name="LSMostRecent" value="Website_Opt_In">
<input type="hidden" name="LSNameMostRecent" value="CDS-Global.com">
<input type="hidden" name="elqFormName" value="cds-global-resources">
<input type="hidden" name="elqSiteId" value="1851">
<hr class="fadeline" />
<div class="form-actions">

<button class="btn btn-danger optin_submit" id="optinSubmit" type="submit">STAY INFORMED!</button></div>
</form>
<p class="optin_note">All messages are moderated and always on topic. You can unsubscribe at any time.</p>
</div>
</div>
</div>
<!-- End Optin Form -->
<!-- Start Thank you text -->
<div id="thankyou" style="display: none;">
<div class="row">
<div class="col-xs-10 col-xs-offset-1">
<h3 style="color: #fc4c00;">Thank you for signing up to stay up to date on our latest products and services.</h3>
<p class="optin_note">All messages are moderated and always on topic. You can unsubscribe at any time.</p>
</div>
</div>
</div>
<!-- End Thank you text -->
</div>
<iframe name="eloqua-submit" style="display: none;"></iframe>
<script>
$('#optinform').submit(function(e) {
    e.preventDefault(); // don't submit multiple times
    this.submit();
    console.log("Hooray, it worked!");
    $("#optinformpage").hide();
    $("#thankyou").show();
});
</script>