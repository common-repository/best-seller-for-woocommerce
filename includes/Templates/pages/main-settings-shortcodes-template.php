<?php
defined( 'ABSPATH' ) || exit;

$core          = $args['core'];
$plugin_info   = $args['plugin_info'];
$template_page = $args['template_page'];
?>

<div class="badge-settings <?php echo esc_attr( $plugin_info['classes_prefix'] . '-shortcodes-settings-wrapper' ); ?>">
	<div class="container">
		<div class="row">
			<!-- Listings -->
			<div class="col-12 my-3">
				<h2><?php esc_html_e( 'Listing', 'best-seller-for-woocommerce' ); ?></h2>
				<div class="shortcodes-list bg-white p-3">
					<!-- General Best Sellers Shortcode -->
					<div class="shortcode-item mt-4">
						<div class="shortcode-title bg-muted border">
							<h4 class="m-0 p-3 bg-light"><?php esc_html_e( 'General Best Seller Products', 'best-seller-for-woocommerce' ); ?></h4>
							<span class="ps-3"><?php esc_html_e( 'best seller products based on total sales', 'best-seller-for-woocommerce' ); ?></span>
						</div>
						<div class="shortcode-text py-3 d-flex align-items-center">
							<code class="best-sellers-general-shortcode"><?php echo esc_html( '[' . str_replace( '-', '_', $plugin_info['classes_prefix'] ) . '_best_sellers ]' ); ?></code>
							<?php $template_page::clipboard_icon( '.best-sellers-general-shortcode' ); ?>
							<button type="button" class="btn btn-secondary ms-2" data-bs-toggle="collapse" data-bs-target="#shortcode-1" aria-controls="shortcode-1" aria-expanded="false" ><?php esc_html_e( 'attributes', 'best-seller-for-woocommerce' ); ?></button>
						</div>
						<div id="shortcode-1" class="shortcode-attrs collapse ">
							<table class="table table-striped table-bordered my-4">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Attribute', 'best-seller-for-woocommerce' ); ?></th>
										<th><?php esc_html_e( 'Description', 'best-seller-for-woocommerce' ); ?></th>
										<th><?php esc_html_e( 'Status', 'best-seller-for-woocommerce' ); ?></th>
										<th><?php esc_html_e( 'Default value', 'best-seller-for-woocommerce' ); ?></th>
										<th><?php esc_html_e( 'Possible values', 'best-seller-for-woocommerce' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?php esc_html_e( 'limit', 'best-seller-for-woocommerce' ); ?></td>
										<td><?php esc_html_e( 'The max products to list', 'best-seller-for-woocommerce' ); ?></td>
										<td><?php esc_html_e( 'Optional', 'best-seller-for-woocommerce' ); ?></td>
										<td><?php esc_html_e( '30', 'best-seller-for-woocommerce' ); ?></td>
										<td></td>
									</tr>
									<tr>
										<td><?php esc_html_e( 'title', 'best-seller-for-woocommerce' ); ?></td>
										<td><?php esc_html_e( 'The listing title', 'best-seller-for-woocommerce' ); ?></td>
										<td><?php esc_html_e( 'Optional', 'best-seller-for-woocommerce' ); ?></td>
										<td><?php esc_html_e( 'Best Sellers', 'best-seller-for-woocommerce' ); ?></td>
										<td></td>
									</tr>
									<tr>
										<td><?php esc_html_e( 'title_tag', 'best-seller-for-woocommerce' ); ?></td>
										<td><?php esc_html_e( 'The title tag', 'best-seller-for-woocommerce' ); ?></td>
										<td><?php esc_html_e( 'Optional', 'best-seller-for-woocommerce' ); ?></td>
										<td><?php esc_html_e( 'h3', 'best-seller-for-woocommerce' ); ?></td>
										<td><?php esc_html_e( 'Title tags available: h1 | h2 | h3 | h4 | h5 | span | p', 'best-seller-for-woocommerce' ); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<!-- Best Sellers in Category Shortcode -->
					<div class="shortcode-item mt-4">
						<div class="shortcode-title bg-muted border">
							<h4 class="m-0 p-3 bg-light"><?php esc_html_e( 'Best Seller Products in a Category', 'best-seller-for-woocommerce' ); ?> <?php $core->pro_btn(); ?></h4>
						</div>
					</div>
					<!-- Best Sellers in all categories Shortcode -->
					<div class="shortcode-item mt-4">
						<div class="shortcode-title bg-muted border">
							<h4 class="m-0 p-3 bg-light"><?php esc_html_e( 'List Best Seller Products in all best seller categories', 'best-seller-for-woocommerce' ); ?> 	<?php $core->pro_btn(); ?></h4>
						</div>
					</div>
				</div>
			</div>
			<!-- Widgets -->
			<div class="col-12 my-3">
				<h2><?php esc_html_e( 'Widgets', 'best-seller-for-woocommerce' ); ?> <?php $core->pro_btn(); ?></h2>
				<div class="shortcodes-list bg-white p-3">
					<!-- Best Sellers in Category Shortcode -->
					<div class="shortcode-item mt-4">
						<div class="shortcode-title bg-muted border">
							<h4 class="m-0 p-3 bg-light"><?php esc_html_e( 'Best Seller Categories widget', 'best-seller-for-woocommerce' ); ?></h4>
						</div>
					</div>
				</div>
			</div>
			<!-- Badges -->
			<div class="col-12 my-3">
				<h2><?php esc_html_e( 'Badges', 'best-seller-for-woocommerce' ); ?> <?php $core->pro_btn(); ?></h2>
				<div class="shortcodes-list bg-white p-3">
					<!-- Text Badge -->
					<div class="shortcode-item mt-4">
						<div class="shortcode-title bg-muted border">
							<h4 class="m-0 p-3 bg-light">
								<?php esc_html_e( 'Text Badge', 'best-seller-for-woocommerce' ); ?>
								<img src="<?php echo esc_url_raw( $plugin_info['url'] . 'assets/images/text-badge.png' ); ?>" alt="text badge icon">
							</h4>
						</div>
					</div>
					<!-- Icon Badge -->
					<div class="shortcode-item mt-4">
						<div class="shortcode-title bg-muted border">
							<h4 class="m-0 p-3 bg-light">
								<?php esc_html_e( 'Icon Badge', 'best-seller-for-woocommerce' ); ?>
								<img src="<?php echo esc_url_raw( $plugin_info['url'] . 'assets/images/icon-badge.png' ); ?>" alt="icon badge icon">
							</h4>
						</div>
					</div>
					<!-- Product rank in categories Badge -->
					<div class="shortcode-item mt-4">
						<div class="shortcode-title bg-muted border">
							<h4 class="m-0 p-3 bg-light">
								<?php esc_html_e( 'Product rank in categories Badge', 'best-seller-for-woocommerce' ); ?>
								<img src="<?php echo esc_url_raw( $plugin_info['url'] . 'assets/images/category-badge.png' ); ?>" alt="category badge icon">
							</h4>
						</div>
					</div>
					<!-- Total Sales Badge -->
					<div class="shortcode-item mt-4">
						<div class="shortcode-title bg-muted border">
							<h4 class="m-0 p-3 bg-light">
								<?php esc_html_e( 'Total Sales Badge', 'best-seller-for-woocommerce' ); ?>
								<img src="<?php echo esc_url_raw( $plugin_info['url'] . 'assets/images/sales-badge.png' ); ?>" alt="total sales badge">
							</h4>
						</div>
					</div>
				</div>
			</div>
			<!-- Sliders -->
			<div class="col-12 my-3">
				<h2><?php esc_html_e( 'Sliders', 'best-seller-for-woocommerce' ); ?> <?php $core->pro_btn(); ?></h2>
				<div class="shortcodes-list bg-white p-3">
					<!-- General Best Sellers Sliders Shortcode -->
					<div class="shortcode-item mt-4">
						<div class="shortcode-title bg-muted border">
							<h4 class="m-0 p-3 bg-light"><?php esc_html_e( 'General Best Seller Products Slider', 'best-seller-for-woocommerce' ); ?></h4>
						</div>
					</div>
					<!-- Best Sellers in Category Sliders Shortcode -->
					<div class="shortcode-item mt-4">
						<div class="shortcode-title bg-muted border">
							<h4 class="m-0 p-3 bg-light"><?php esc_html_e( 'Best Seller Products in a Category Slider', 'best-seller-for-woocommerce' ); ?></h4>
						</div>
					</div>

					<!-- Best Sellers in all Categories Sliders Shortcode -->
					<div class="shortcode-item mt-4">
						<div class="shortcode-title bg-muted border">
							<h4 class="m-0 p-3 bg-light"><?php esc_html_e( 'Best Seller Products in best seller categories sliders', 'best-seller-for-woocommerce' ); ?></h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
