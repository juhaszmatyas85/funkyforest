<?php
$wp_root = dirname( dirname( __FILE__ ) );

require_once( $wp_root . "../../../../wp-load.php" );

// if ( ! current_user_can( 'manage_options' ) ) die();
if ( ! defined( 'SURBMA_HC_PREMIUM' ) || ! SURBMA_HC_PREMIUM ) die();

$product_id = isset( $_GET['product_id'] ) ? $_GET['product_id'] : 0;
$product = wc_get_product( $product_id );

// Stop if we don't process a product
if ( $product ) {
	$product_regular_price = intval( $product->get_regular_price() );

	// Always get the actual and active price
	$product_price = intval( $product->get_price() );

	$current_time = current_datetime();
	$current_time = strval( date( 'Y-m-d H:i:s', $current_time->getTimestamp() + $current_time->getOffset() ) );

	$hc_params_delete = array_merge( $_GET, array( 'hc-product_price_history' => 'delete' ) );
	$hc_delete_query_string = http_build_query( $hc_params_delete );

	// Remove query parameter from url
	$hc_manual_request = isset( $_GET['hc-product_price_history'] ) ? $_GET['hc-product_price_history'] : false;
	if ( $hc_manual_request ) {
		if ( current_user_can( 'manage_options' ) && 'delete' == $hc_manual_request ) {
			delete_post_meta( $product_id, '_hc_product_price_history' );
		}

		$url = esc_url_raw( remove_query_arg( 'hc-product_price_history' ) );
		wp_redirect( $url );
	}

	// If there is no data, create the first price item
	if ( ! get_post_meta( $product_id, '_hc_product_price_history' ) ) {
		$product_price_history = array(
			array( $current_time, $product_regular_price, $product_price )
		);
		add_post_meta( $product_id, '_hc_product_price_history', $product_price_history );
	}

	$product_price_history = get_post_meta( $product_id, '_hc_product_price_history', true );
	array_multisort( $product_price_history, SORT_DESC );

	// Create special array for Google Chart
	$chart_array = $product_price_history;
	// Change data order to show proper timeline
	array_multisort( $chart_array, SORT_ASC );
	// Add heading to chart
	$chart_heading = array( 'Dátum', 'Normál ár', 'Aktív ár' );
	array_unshift( $chart_array, $chart_heading );
	// Convert array to json
	$chart_data = json_encode( $chart_array );

	// Convert data to CSV
	$csv = '';
	$header = false;
	foreach ( $product_price_history as $line ) {
		if ( !$header ) {
			$header = array_keys( $line );
			$csv .= implode( ',', $header );
			$header = array_flip( $header );
		}

		$line_array = array();

		foreach( $line as $value ) {
			array_push( $line_array, $value );
		}

		$csv .= "\n" . implode( ',', $line_array );
	}
}

?>
<!DOCTYPE HTML>
<html lan="hu">
	<head>
		<meta charset="utf-8" />
		<meta name="robots" content="noindex">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.14.3/css/uikit.min.css" integrity="sha512-iWrYv6nUp7gzf+Ut/gMjxZn+SWdaiJYn+ZZNq63t2JO6kBpDc40wQfBzC1eOAzlwIMvRyuS974D1R8p1BTdaUw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.14.3/js/uikit.min.js" integrity="sha512-wqamZDJQvRHCyy5j5dfHbqq0rUn31pS2fJeNL4vVjl0gnSVIZoHFqhwcoYWoJkVSdh5yORJt+T9lTdd8j9W4Iw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php if ( get_post_meta( $product_id, '_hc_product_price_history' ) ) { ?>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
			google.charts.load('current', {'packages':['corechart']});
			google.charts.setOnLoadCallback(drawChart);

			function drawChart() {
				var data = google.visualization.arrayToDataTable(<?php echo $chart_data; ?>);

				var options = {
					title: 'Termék ár történet: <?php echo $product->get_title(); ?>',
					curveType: 'function',
					legend: { position: 'bottom' }
				};

				var chart = new google.visualization.LineChart(document.getElementById('product_price_history_chart'));

				chart.draw(data, options);
			}
		</script>
<?php } ?>
<?php if ( $product ) { ?>
		<script>
			function copyJsonData() {
				/* Get the text field */
				var copyJSON = document.getElementById("json-data");

				/* Select the text field */
				copyJSON.select();
				copyJSON.setSelectionRange(0, 99999); /* For mobile devices */

				/* Copy the text inside the text field */
				navigator.clipboard.writeText(copyJSON.value);

				/* Alert the copied text */
				setTimeout(function() {
					alert("JSON adatok kimásolva a vágólapra");
				}, 500);
			}
		</script>
		<script>
			function copyCsvData() {
				/* Get the text field */
				var copyCSV = document.getElementById("csv-data");

				/* Select the text field */
				copyCSV.select();
				copyCSV.setSelectionRange(0, 99999); /* For mobile devices */

				/* Copy the text inside the text field */
				navigator.clipboard.writeText(copyCSV.value);

				/* Alert the copied text */
				setTimeout(function() {
					alert("CSV adatok kimásolva a vágólapra");
				}, 500);
			}
		</script>
<?php } ?>
	</head>
	<body>
		<article class="uk-article">
		<?php if ( $product ) { ?>
			<div class="uk-section uk-section-default uk-section-xsmall">
				<div class="uk-container uk-text-center">
					<h1 class="uk-article-title"><?php echo $product->get_title(); ?></h1>
					<p class="uk-article-meta">Termék ár történet</p>
				</div>
			</div>

			<hr class="uk-margin-remove">

			<div class="uk-section uk-section-muted uk-section-xsmall">
				<div class="uk-container">
					<ul class="uk-subnav uk-subnav-divider uk-flex uk-flex-center" uk-margin>
						<li><a href="<?php echo get_permalink( $product_id ); ?>" target="_blank">Termék oldal</a></li>
						<?php if ( current_user_can( 'manage_options' ) ) { ?>
						<li><a href="/wp-admin/edit.php?post_type=product" target="_blank">Admin termékek listázása</a></li>
						<li><a href="/wp-admin/post.php?post=<?php echo $product_id; ?>&action=edit" target="_blank">Termék szerkesztése</a></li>
						<?php } ?>
					</ul>
				</div>
			</div>

			<hr class="uk-margin-remove">

			<div class="uk-section uk-section-default">
				<div class="uk-container">
					<h3 class="uk-heading-line uk-text-center"><span>Termék ár történet táblázat</span></h3>
					<?php if ( get_post_meta( $product_id, '_hc_product_price_history' ) ) { ?>
					<div class="uk-overflow-auto">
						<table class="uk-table uk-table-striped uk-table-hover uk-table-small uk-table-middle uk-text-center" style="margin: 0 auto;">
							<colgroup>
								<col style="width: 25%;">
								<col style="width: 25%;">
								<col style="width: 25%;">
								<col style="width: 25%;">
							</colgroup>
							<thead>
								<tr>
									<th class="uk-text-center">Dátum</th>
									<th class="uk-text-center">Normál ár</th>
									<th class="uk-text-center">Aktív ár</th>
									<th class="uk-text-center">Aktuális kedvezmény</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$curreny_symbol = get_woocommerce_currency_symbol();
								date_default_timezone_set('Europe/Budapest');
								for( $i = 0; $i < count( $product_price_history ) ; $i++ ) {
									if ( 0 === $product_price_history[$i][2] || 0 === $product_price_history[$i][1] ) {
										$product_price_discount = 0;
									} else {
										$product_price_discount = intval( number_format( round( ( ( 1 - ( $product_price_history[$i][2] / $product_price_history[$i][1] ) ) * 100 ), 2 ), 2 ) );
									}
									if ( strtotime( $product_price_history[$i][0] ) < strtotime( '-30 day' ) ) {
										echo '<tr class="history-table-old" style="border-left: 5px solid #f0506e;border-right: 1px solid #e5e5e5;" hidden>';
									} else {
										echo '<tr style="border-left: 5px solid #32d296;border-right: 1px solid #e5e5e5;">';
									}
									echo '<td style="text-align: left;">' . $product_price_history[$i][0] . '</td>';
									echo '<td>' . $product_price_history[$i][1] . ' ' . $curreny_symbol . '</td>';
									echo '<td>' . $product_price_history[$i][2] . ' ' . $curreny_symbol . '</td>';
									echo '<td>' . $product_price_discount . '%</td>';
									echo '</tr>';
								}
								?>
								<tr style="border-left: 5px solid #e5e5e5;border-right: 1px solid #e5e5e5;"><td colspan="4" style="padding: 0;"></td></tr>
							</tbody>
						</table>
					</div>
					<div class="uk-section uk-section-xsmall uk-text-center history-table-old"><button class="uk-button uk-button-default" type="button" uk-toggle="target: .history-table-old; animation: uk-animation-fade; queued: true">30 napnál régebbi termék történet mutatása</button></div>
					<?php } else { ?>
						<h2 class="uk-h5 uk-text-center">Nincs még termék ár történet mentve a megadott termékhez. <br>Az árak első módosítása után jön létre a szükséges adat a megjelenítéshez.</h2>
					<?php } ?>
				</div>
			</div>

			<hr class="uk-margin-remove">

			<?php if ( get_post_meta( $product_id, '_hc_product_price_history' ) ) { ?>
			<div class="uk-section uk-section-muted">
				<div class="uk-container">
					<h3 class="uk-heading-line uk-text-center"><span>Termék ár történet diagram</span></h3>
					<!-- https://developers.google.com/chart/interactive/docs/gallery/linechart?hl=hu -->
					<div id="product_price_history_chart" style="width: 100%; height: 500px"></div>
				</div>
			</div>

			<hr class="uk-margin-remove">

			<?php if ( current_user_can( 'manage_options' ) ) { ?>
			<div class="uk-section uk-section-default">
				<div class="uk-container">
					<h3 class="uk-heading-line uk-text-center"><span>Termék ár történet adatok másolása</span></h3>
					<form class="uk-form-horizontal uk-margin-large">
						<div class="uk-margin">
							<label class="uk-form-label" for="form-horizontal-text">JSON formátum</label>
							<div class="uk-form-controls">
								<textarea id="json-data" class="uk-textarea uk-background-muted" rows="10" style="font-family: Consolas,monaco,monospace;font-size: 12px;white-space: pre-wrap;word-break: break-all;" readonly><?php print_r( json_encode( $product_price_history ) ); ?></textarea>
								<p class="uk-text-right"><button class="uk-button uk-button-secondary" onclick="copyJsonData()">JSON adatok másolása</button></p>
							</div>
						</div>
						<div class="uk-margin">
							<label class="uk-form-label" for="form-horizontal-select">CSV formátum</label>
							<div class="uk-form-controls">
								<textarea id="csv-data" class="uk-textarea uk-background-muted" rows="10" style="font-family: Consolas,monaco,monospace;font-size: 12px;white-space: pre-wrap;word-break: break-all;" readonly><?php echo $csv; ?></textarea>
								<p class="uk-text-right uk-text-meta">CSV fejléc adatok jelentése: 0 = Dátum | 1 = Normál ár | 2 = Aktív ár</p>
								<p class="uk-text-right"><button class="uk-button uk-button-secondary" onclick="copyCsvData()">CSV adatok másolása</button></p>
							</div>
						</div>
					</form>
				</div>
			</div>

			<hr class="uk-margin-remove">

			<div class="uk-section uk-section-secondary">
				<div class="uk-container uk-text-center">
					<h3 class="uk-heading-line uk-text-center"><span>Termék ár történet adatok törlése</span></h3>
					<a href="<?php echo SURBMA_HC_PLUGIN_URL; ?>/modules-hu/product-price-history-display.php?<?php echo $hc_delete_query_string; ?>" class="uk-button uk-button-danger" onclick="return confirm('Biztosan törlöd az összes ár történet adatot ennél a terméknél?')">Adatok törlése</a>
				</div>
			</div>
			<?php } ?>
			<?php } ?>
		<?php } else { ?>
			<div class="uk-section uk-section-default">
				<div class="uk-container uk-container-xsmall">
					<div class="uk-alert-danger uk-text-center" uk-alert>
						<p>Hibás termék azonosító. Így nincs mit megjeleníteni.</p>
					</div>
				</div>
			</div>
		<?php } ?>
		</article>
	</body>
</html>
<?php
