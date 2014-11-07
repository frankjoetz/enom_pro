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


	<?php $defaultCurrencyPrefix = $this->getDefaultCurrencyPrefix(); ?>
	<?php if ( $this->isNonUSDinWHMCS() ) :?>
		<div class="well">
			<button type="button"
							class="close"
							data-dismiss="alert"
							aria-hidden="true">&uarr;</button>
			<h3>Beta — Currency Conversion <span class="enom-pro-icon enom-pro-icon-currency"></span></h3>
			<?php if ($this->isCustomExchangeRate()) :?>
				<?php $exchangeRate = $this->getCustomExchangeRate();?>
			<?php else: ?>
				<?php $exchangeRate = $this->get_exchange_rate_from_USD_to($defaultCurrencyCode);?>
			<?php endif;?>
			<div class="row">
				<div class="col-lg-6">
					<div class="alert alert-info">
						We have detected that your WHMCS configuration is not using USD as a base currency.
					</div>
					<?php if (null !== $exchangeRate) :?>
						<div class="alert alert-warning">
							<p>
									Exchange rate <span class="badge"><?php echo $exchangeRate; ?></span>
							used to convert eNom's <span class="badge">USD</span> pricing into your <span class="label label-info">WHMCS</span> Default currency: <span class="badge"><?php echo $defaultCurrencyCode ?></span>
							</p>
						</div>
					<?php else: ?>
						<div class="alert alert-danger">
							<p>No exchange rate found. Please enter one manually, or enter an API key for updating currencies.</p>
						</div>
					<?php endif;?>
				</div>
				<div class="col-sm-6">
					<div class="well well-sm">
							<div class="alert <?php if ($this->isCustomExchangeRate()):?>alert-danger<?php else: ?>alert-info<?php endif; ?>">
								<?php if ($this->isCustomExchangeRate()):?>
									Using Custom Exchange Rate. <br/>
								<?php else: ?>
									Enter Custom Exchange Rate. <br/>
								<?php endif;?>
								<form method="post" class="form-inline">
									<input type="hidden" name="action" value="save_custom_exchange_rate" />
									<label>
										Use Exchange Rate:
										<input type="text" name="custom-exchange-rate" value="<?php echo enom_pro::get_addon_setting('custom-exchange-rate'); ?>"/>
									</label>
									<input type="submit" value="Update" class="btn btn-primary"/>
								</form>
								<?php if ($this->isCustomExchangeRate()) :?>
									<form method="post" class="form-inline">
										<input type="hidden" name="action" value="save_custom_exchange_rate" />
										<input type="hidden" name="custom-exchange-rate" value="-1" />
										<input type="submit" value="Clear" class="btn btn-danger"/>
									</form>
								<?php endif;?>
							</div>
						<h3>
							<span class="badge">USD</span>
							&rarr;
							<span class="badge"><?php echo $defaultCurrencyCode ?></span> = <span class="badge">1</span> &rarr; <span class="badge"><?php echo $exchangeRate !== null ? $exchangeRate : '???'; ?></span>
						</h3>
						<p>Exchange rate updated <span class="badge"><?php echo $this->get_exchange_rate_cache_date() ?></span>
							<?php if ($this->isUsingExchangeRateAPIKey()) :?><span class="badge">Using API key</span><?php endif;?></p>
						<div class="btn-group">
							<a class="btn btn-default btn-link" href="<?php echo enom_pro::MODULE_LINK . '&action=clear_exchange_cache' ?>"><span class="enom-pro-icon enom-pro-icon-refresh-alt"></span>Update Exchange Rate</a>
							<a target="_blank" href="configcurrencies.php" class="ep_tt ep_lightbox btn btn-default" data-title="WHMCS Currencies" data-width="90%" title="" data-original-title="Configure currencies in WHMCS">Edit WHMCS Currency</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php if ( !enom_pro_controller::isDismissed( 'multiple_currencies' ) ) : ?>
			<div class="alert alert-danger">
				<button type="button"
								class="close"
								data-dismiss="alert"
								data-alert="multiple_currencies"
								aria-hidden="true">&times;</button>
				<h4>More than 1 currency in WHMCS?</h4>
				<p>Once you have imported the pricing from eNom into WHMCS, you can use <b>WHMCS built-in product pricing currency update</b> function to convert the rest of your currencies.</p>
				<ol class="numbered">
					<li>Click "Edit WHMCS Currency" Above</li>
					<li>Click "Update Product Prices" on the WHMCS Currency Configuration Page</li>
					<li>Additionally, you can have WHMCS automatically update product pricing on the CRON run. See the WHMCS docs for more information. <a href="http://docs.whmcs.com/Automation_Settings#Currency_Auto_Update_Settings" target="_blank" >WHMCS Currency Auto Update</a></li>
				</ol>
			</div>
		<?php endif; ?>
	<?php endif;//End default currency check ?>

	<div class="well row">
			<button type="button"
							class="close"
							data-dismiss="alert"
							aria-hidden="true">&uarr;</button>

		<div class="col-lg-6">

			<h3>Bulk Import</h3>

			<p>Import all TLDs on this page</p>

			<form class="bulkImport form-inline"
						role="form"
						action="<?php echo enom_pro::MODULE_LINK ?>">
				<h4>Profit Settings</h4>
				<div class="form-group col-xs-12">
					<h5 class="ep_pop" title="Minimum Profit" data-placement="auto top" data-content="Enter the minimum acceptable profit. Use this to cover credit card processing fees, for example.">Minimum Profit</h5>
					<div class="input-group">
						<label for="percentMarkup" class="input-group-addon">Markup</label>
						<input type="number"
									 min="0"
									 step="0.01"
									 max="500"
									 name="markup"
									 id="percentMarkup"
									 class="form-control input-sm"/>
						<span class="input-group-addon">%</span>
					</div>
					<div class="input-group">
						<label>+</label>
					</div>
					<div class="input-group">
						<label for="wholeMarkup" class="input-group-addon">$</label>
						<input type="number"
									 min="0.00"
									 max="500"
									 step="0.05"
									 name="markup2"
									 id="wholeMarkup"
									 placeholder="0.00"
									 class="form-control input-sm"/>
					</div>
				</div>
				<div class="form-group col-xs-12">
					<div class="alert alert-danger"><h3>BETA: Mock-up only</h3><p>Non functional at this point</p></div>
					<h5 class="ep_pop" title="Preferred Profit" data-placement="auto top" data-content="The profit you'd like to make, while still being protected from under-selling.">Preferred Profit</h5>
					<div class="input-group">
						<label for="percentMarkup" class="input-group-addon">Markup</label>
						<input type="number"
									 min="0"
									 max="500"
									 step="0.01"
									 name="markup"
									 id="percentMarkup"
									 class="form-control input-sm"/>
						<span class="input-group-addon">%</span>
					</div>
					<div class="input-group">
						<label>+</label>
					</div>
					<div class="input-group">
						<label for="wholeMarkup" class="input-group-addon">$</label>
						<input type="number"
									 min="0.00"
									 max="500"
									 step="0.05"
									 name="markup2"
									 id="wholeMarkup"
									 placeholder="0.00"
									 class="form-control input-sm"/>
					</div>
				</div>


				<div class="form-group col-xs-12">
					<h5>Price Options</h5>
					<div class="input-group">
						<label for="roundTo" class="input-group-addon">Round up to $</label>
						<select name="round" id="roundTo" class="form-control input-sm-2">
							<option value="99">.99</option>
							<option value="98">.98</option>
							<option value="95">.95</option>
							<option value="50">.50</option>
							<option value="01">.01</option>
							<option value="00">.00</option>
							<option value="-1">Disabled</option>
						</select>
					</div>
					<div class="input-group checkbox">
						<label for="overWriteWHMCS" class="input-group-addon">
							<input type="checkbox" name="overwrite" id="overWriteWHMCS"/>
							Overwrite Values Already in WHMCS
						</label>
					</div>
				</div>

				<div class="btn-group pull-right">
					<button type="submit" class="btn btn-primary ep_pop" data-content="Press ENTER in the form above for rapid previewing" title="Helpful Hint" data-placement="auto top" data-container="body">Preview</button>
					<button type="button" class="btn btn-success savePricing">Save</button>

					<div class="btn-group">
						<button type="reset" class="btn btn-danger clear_all">Clear</button>
						<button type="button"
										class="btn btn-danger dropdown-toggle"
										data-toggle="dropdown">
							<span class="caret"></span>
							&nbsp;
							<span class="sr-only">Toggle Dropdown</span>
						</button>
						<ul class="dropdown-menu clearDropdown" role="menu">
							<li><a href="#" class="deleteFromWHMCS">Delete all from WHMCS</a>
							</li>
						</ul>
					</div>
				</div>
			</form>
		</div>
		<div class="col-lg-6">
			<h3>Domain Pricing Meta</h3>
			Pricing for a total of <?php echo count( $this->getAllDomainsPricing() ) ?> TLDs.
			<br/>
			Pricing data last updated <?php echo $this->get_price_cache_date(); ?>
			<a class="btn btn-default btn-info"
				 href="<?php echo enom_pro::MODULE_LINK; ?>&action=clear_price_cache">Clear Cache</a>
		</div>
	</div>
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
		<p class="text-center"></p>
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
								 $(".progress, .stopPriceBatch").hide();
								 setTimeout(function  (){
									 window.location.reload();
								 }, 1000);
							 } else {
								 doPriceBatch();
								 var percent = Math.round( (data.loaded / data.total) * 100 );
								 $loader.hide();
								 $(".progress-bar").css('width', percent + '%').html(percent + "% Complete").attr('aria-valuenow', percent);
								 $(".alert p").html('Loaded pricing for: ' + data.tld);
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