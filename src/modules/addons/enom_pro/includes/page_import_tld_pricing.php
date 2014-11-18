<?php
global $per_page;
$per_page = 25;
/**
 * @param enom_pro $enom_pro
 */
function pager( $enom_pro ) {
	global $per_page;
	?>
	<ul class="pager">
		<li class="previous">
			<?php if ( @$_GET['start'] >= $per_page ) : ?>
				<?php $prev_start = isset( $_GET['start'] ) ? (int) $_GET['start'] - $per_page : $per_page; ?>
				<a data-start="<?php echo $prev_start ?>"
					 href="<?php echo $_SERVER['PHP_SELF']; ?>?module=enom_pro&view=pricing_import&start=<?php echo $prev_start ?>#enom_pro_pricing_table">&larr; Prev</a>
			<?php endif; ?>
		</li>
		<li class="next">
			<?php if ( @$_GET['start'] <= ( count( $enom_pro->getAllDomainsPricing() ) - $per_page ) ) : ?>
				<?php $next_start = isset( $_GET['start'] ) ? (int) $_GET['start'] + $per_page : $per_page; ?>
				<a data-start="<?php echo $next_start ?>"
					 href="<?php echo $_SERVER['PHP_SELF']; ?>?module=enom_pro&view=pricing_import&start=<?php echo $next_start ?>#enom_pro_pricing_table">Next &rarr;</a>
			<?php endif; ?>
		</li>
	</ul>
<?php
}

?>
<?php
/**
 * @var $this enom_pro
 */
/*
 ***********************
 * TLD Cache Check
 ***********************
 */
if ( $this->is_pricing_cached() ) :
	?>
	<div id="enom_pro_pricing_import_page">
	<?php if (
		isset( $_GET['cleared'] ) ||
		isset( $_GET['new'] ) ||
		isset( $_GET['updated'] ) ||
		isset( $_GET['deleted'] ) ||
		isset( $_GET['exchange'] ) ||
		isset( $_GET['nochange'] )
	) :
		?>
		<div class="slideup fixed" data-timeout="3">
			<?php if ( isset( $_GET['cleared'] ) ) : ?>
				<button type="button"
								class="close"
								data-dismiss="alert"
								aria-hidden="true">&times;</button>
				<div class="alert alert-info">Cache Cleared</div>
			<?php endif; ?>
			<?php if ( isset( $_GET['new'] ) ): ?>
				<div class="alert alert-success">
					<button type="button"
									class="close"
									data-dismiss="alert"
									aria-hidden="true">&times;</button>
					<p>Created <?php echo (int) $_GET['new'] ?> new TLD pricing in WHMCS</p>
				</div>
			<?php endif; ?>
			<?php if ( isset( $_GET['updated'] ) ): ?>
				<div class="alert alert-success">
					<button type="button"
									class="close"
									data-dismiss="alert"
									aria-hidden="true">&times;</button>
					<p>Updated <?php echo (int) $_GET['updated'] ?> TLD pricing in WHMCS</p>
				</div>
			<?php endif; ?>
			<?php if ( isset( $_GET['deleted'] ) ): ?>
				<div class="alert alert-danger">
					<button type="button"
									class="close"
									data-dismiss="alert"
									aria-hidden="true">&times;</button>
					<p>Deleted <?php echo (int) $_GET['deleted'] ?> TLD pricing from WHMCS</p>
				</div>
			<?php endif; ?>
			<?php if ( isset( $_GET['nochange'] ) ): ?>
				<div class="alert alert-danger">
					<button type="button"
									class="close"
									data-dismiss="alert"
									aria-hidden="true">&times;</button>
					<p>No Pricing was Selected for Update</p>
				</div>
			<?php endif; ?>
			<?php if ( isset( $_GET['exchange'] ) ): ?>
				<div class="alert alert-info">
					<button type="button"
									class="close"
									data-dismiss="alert"
									aria-hidden="true">&times;</button>
					<p>Exchange Rates Updated</p>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; //End Message wrap?>

	<?php if ( !enom_pro_controller::isDismissed( 'price_intro' ) ) : ?>
		<div class="alert alert-info">
			<button type="button"
							class="close"
							data-dismiss="alert"
							data-alert="price_intro"
							aria-hidden="true">&times;</button>
			<p>
				Import pricing for all 3 domain order types:
			<ul class="list-inline">
				<li>
					<span class="badge">register</span>
				</li>
				<li>
					<span class="badge">transfer</span>
				</li>
				<li>
					<span class="badge">renew</span>
				</li>
			</ul>
			Once they are imported, you can
			bulk edit all 3 pricing tiers, or you can fine tune pricing in directly in whmcs by
			clicking the TLD drop-down menu button.
			</p>
			<img src="../modules/addons/enom_pro/images/pricing-drop-down-help.jpg" height="209" width="238" alt="" />
		</div>
	<?php endif; ?>
	<?php if ( !enom_pro_controller::isDismissed( 'order-types' ) ) : ?>
		<div class="alert alert-warning fade in">
			<button type="button"
							class="close"
							data-dismiss="alert"
							data-alert="order-types"
							aria-hidden="true">&times;</button>
					<span><b>IMPORTANT:</b> Clicking Save will overwrite any specific order type pricing that have been customized
									(IE: Different prices for register vs. transfer).<br/>
									<em>If in doubt, please <a href="#"
																						 class="clear_all btn  btn-default btn-xs">Clear All Pricing</a> before saving.</em>
							</span>
		</div>
	<?php endif; ?>



	<?php require_once ENOM_PRO_INCLUDES . 'page_import_tld_pricing_currency_conversion.php'; ?>

	<?php require_once ENOM_PRO_INCLUDES . 'page_import_tld_pricing_bulk_process.php'; ?>

	<?php pager( $this ); ?>
	<form method="POST"
				action="<?php echo $_SERVER['PHP_SELF']; ?>?module=enom_pro&view=pricing_import"
				id="enom_pro_pricing_import">
		<input type="hidden" name="action" value="save_domain_pricing"/>
		<input type="hidden"
					 name="start"
					 value="<?php echo isset( $_GET['start'] ) ? (int) $_GET['start'] : '0'; ?>"/>


		<table class="table table-bordered table-responsive"
					 id="enom_pro_pricing_table">
			<tr>
				<th>Actions</th>
				<?php foreach ( array_keys( array_fill( 1,
					enom_pro::get_addon_setting( 'pricing_years' ),
					'' ) ) as $key => $year ) : ?>
					<th colspan="1"><?php echo $year; ?> Year<?php if ( $year > 1 ): ?>s<?php endif; ?>
					</th>
				<?php endforeach; ?>
			</tr>
			<?php
			$offset = isset( $_GET['start'] ) ? (int) $_GET['start'] : 0;
			$domains = array_slice( $this->getAllDomainsPricing(),
				$offset,
				$per_page );
			foreach ( $domains as $tld => $domainPriceData ):
				$isInWHMCS = false; ?>
				<?php $whmcs_pricing_for_tld = $this->get_whmcs_domain_pricing( $tld ); ?>
				<?php if ( count( $whmcs_pricing_for_tld ) > 0 ) : ?>
				<?php $whmcs_id = $whmcs_pricing_for_tld['id']; ?>
				<?php $isInWHMCS = true; ?>
			<?php endif; ?>
				<tr>
					<td>
						<div class="btn-group tldActions">
							<div class="btn tldAction <?php echo $isInWHMCS ? 'btn-success' : 'btn-default' ?> dropdown-toggle"
									 data-toggle="dropdown"
									 data-tld="<?php echo $tld ?>"
									 <?php if ($isInWHMCS) : ?>data-whmcs="true"<?php endif ?>><?php echo $tld; ?></div>
							<ul class="dropdown-menu" role="menu">
								<?php if ( $isInWHMCS ) : ?>
									<li>
										<a target="_blank" class="ep_lightbox"
											 data-pricing="true"
											 data-target="configdomains.php?action=editpricing&id=<?php echo $whmcs_id; ?>"
											 data-title="Pricing for .<?php echo $tld; ?>"
											 href="configdomains.php?action=editpricing&id=<?php echo $whmcs_id; ?>">Edit WHMCS Pricing</a>
									</li>
									<li><a href="#"
												 data-tld="<?php echo $tld ?>"
												 class="delete_tld">
											Delete Pricing from WHMCS</a></li>
									<li class="divider"></li>
								<?php endif; ?>
								<li>
									<a href="#"
										 data-tld="<?php echo $tld ?>"
										 class="toggle_tld"
										>Import eNom Pricing</a>
								</li>
								<li><a href="#"
											 data-tld="<?php echo $tld ?>"
											 class="mult_row"
										>Multiply 1-year price for Row</a></li>
							</ul>
						</div>
					</td>
					<?php foreach ( array_keys( array_fill( 1,
						enom_pro::get_addon_setting( 'pricing_years' ),
						'' ) ) as $key => $year ) : ?>
						<?php
						$rawEnomPrice = number_format( ( $domainPriceData['price'] * $year ),
							2,
							'.',
							'' );
						$formattedEnomPrice = number_format( ( $domainPriceData['price'] * $year ),
							2 );
						$whmcs_price = isset( $whmcs_pricing_for_tld[$year] ) ? number_format( $whmcs_pricing_for_tld[$year],
							2 ) : false;
						$notEnabledForPeriod = $domainPriceData['min_period'] > $year ? true : false;
						$class = '';
						if ( $whmcs_price && $whmcs_price > 0) {
							$class = 'has-success';
						} elseif ($whmcs_price && $whmcs_price == 0) {
							$class = 'has-warning';
						}
						if ( $notEnabledForPeriod ) {
							$class = 'has-warning';
						}
						?>
						<td>
							<div class="input-group input-group-sm <?php echo $class ?>">
																<span class="price ep_tt input-group-addon input-sm"
																			title="eNom Price">
																	<?php echo $defaultCurrencyPrefix . $formattedEnomPrice; ?>
																</span>
								<input
									data-tld="<?php echo $tld ?>"
									data-year="<?php echo $year; ?>"
									data-price="<?php echo $rawEnomPrice; ?>"
									<?php if ( $whmcs_price ) : ?>
										data-whmcs="<?php echo $whmcs_price; ?>"
									<?php endif; ?>
									class="myPrice form-control input-sm <?php echo $notEnabledForPeriod ? 'ep_tt' : '' ?>"
									size="6"
									type="text"
									name="pricing[<?php echo $tld ?>][<?php echo $year ?>]"
									<?php if ( $notEnabledForPeriod ) : ?>
										title="Min. Registration Period is <?php echo $domainPriceData['min_period'] ?>"
									<?php endif; ?>
									value=""
									/>
								<?php if ( $whmcs_price ) : ?>
									<span class="price ep_tt input-group-addon input-sm"
												title="WHMCS Price">
																		<?php echo $defaultCurrencyPrefix . $whmcs_price; ?></span>
								<?php endif; ?>
							</div>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</table>
		<input type="submit" value="Save" class="btn btn-block btn-success">
	</form>
	<?php pager( $this ); ?>
	<script>
		jQuery(function($) {
			$(".pager a").on('click', function() {
				var unsaved = false, $link = $(this);
				$('.myPrice').each(function(k, v) {
					$input = $(v);
					if ($input.val() != '') {
						//Value is not blank
						if ($input.val() != $input.data('whmcs')) {
							//Value does not equal saved WHMCS value
							unsaved = true;
						}
						if (! $input.data('whmcs')) {
							//There is no WHMCS pricing for this node
							unsaved = true;
						}
					}
				});
				if (unsaved) {
					$("input[name=start]").val($link.data('start'));
					$("#enom_pro_pricing_import").trigger('submit');
					return false;
				}
			});
		});
	</script>
	</div>
	<?php //End <div id="enom_pro_pricing_import_page">?>

<?php
	/**
	 **************************
	 * TLD pricing un-cached
	 **************************
   */
	?>

<?php else: ?>
	<div class="alert alert-warning" id="loading_pricing">
		<h3>Loading <?php echo enom_pro::is_retail_pricing() ? 'retail' : 'wholesale'; ?> pricing for <?php echo count( $this->getTLDs() ) ?> top level domains.</h3>
		<p class="text-center loadedTLD"></p>
		<div class="enom_pro_loader"></div>
		<div class="progress">
			<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
			</div>
		</div>
		<button class="btn btn-danger btn-block stopPriceBatch">Cancel</button>
	</div>
	<script>
		var bulkPricingAJAX, aborted = false;
		jQuery(function($) {
			var $cancelButton = $(".stopPriceBatch"),
				$title = $(".alert h3"),
				$loader = $(".enom_pro_loader");
			function doPriceBatch() {
				if (aborted) {
					return false;
				}
				bulkPricingAJAX = $.ajax({
						 url    : '<?php echo enom_pro::MODULE_LINK; ?>&action=get_pricing_data',
						 success: function(data) {
							 if (data == 'success') {
								 $title.html('Import Complete. Reloading...');
							 		$title.closest('.alert').removeClass('alert-warning').addClass('alert-success');
								 $(".progress, .stopPriceBatch, .loadedTLD").hide();
								 setTimeout(function  (){
									 window.location.reload();
								 }, 1000);
							 } else {
								 doPriceBatch();
								 var percent = Math.round( (data.loaded / data.total) * 100 );
								 $loader.hide();
								 $(".progress-bar").css('width', percent + '%').html(percent + "% Complete").attr('aria-valuenow', percent);
								 $("#loading_pricing p").html('Loaded pricing for: ' + data.tld);
							 }
						 },
						 error  : function(xhr) {
							 $title.removeClass('alert-warning');
							 if (xhr.statusText == 'abort') {
								 $title.html('Process Cancelled').addClass('alert-success');
							 } else {
								 $title.html('Error: ' + xhr.responseText).addClass('alert-danger');
							 }
							 $('<span class="btn btn-default">Retry?</span>').on('click',function() {
								 window.location.reload();
								 return false;
							 }).appendTo($title);
							 $loader.hide();
						 }
					 });
			}

			doPriceBatch();
			$(".stopPriceBatch").on('click', function  (){
				var ans = confirm('This will abort fetching the current pricing from eNom. \n\n' +
														'You can restart the process later at the same point by simply coming back to this page.', "Are you sure?")
				if (ans) {
					aborted = true;
					bulkPricingAJAX.abort();
				}
			});
		})
	</script>
<?php endif; ?>