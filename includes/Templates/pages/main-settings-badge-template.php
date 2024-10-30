<?php
defined( 'ABSPATH' ) || exit;

$core          = $args['core'];
$plugin_info   = $args['plugin_info'];
$template_page = $args['template_page'];
?>
<div class="badge-settings <?php echo esc_attr( $plugin_info['classes_prefix'] . '-badge-settings-wrapper' ); ?>">
	<div class="row">
		<div class="col-md-6">
			<div id="badges-settings-id">
				<nav>
					<div class="nav nav-tabs" id="badge-settings-nav-tab" role="tablist">
						<button class="nav-link active" id="best-seller-badge-btn" data-bs-toggle="tab" data-bs-target="#best-seller-badge" type="button" role="tab" aria-controls="best-seller-badge" aria-selected="true"><?php esc_html_e( 'Best Seller Badge', 'best-seller-for-woocommerce' ); ?></button>
						<button class="nav-link" id="total-sales-badge-btn" data-bs-toggle="tab" data-bs-target="#total-sales-badge" type="button" role="tab" aria-controls="total-sales-badge" aria-selected="false"><?php esc_html_e( 'Total Sales Badge', 'best-seller-for-woocommerce' ); ?> <?php $core->pro_btn(); ?></button>
						<button class="nav-link" id="category-badge-btn" data-bs-toggle="tab" data-bs-target="#category-badge" type="button" role="tab" aria-controls="category-badge" aria-selected="false"><?php esc_html_e( 'Best Seller rank in Category Badge', 'best-seller-for-woocommerce' ); ?> <?php $core->pro_btn(); ?></button>
					</div>
				</nav>
				<div class="tab-content badges-settings">
					<div class="tab-pane fade show active" role="tabpanel" aria-labelledby="best-seller-badge-btn" id="best-seller-badge">
						<?php $template_page->settings->print_settings( 'badge', 'general', false ); ?>
					</div>
					<div class="tab-pane fade" role="tabpanel" aria-labelledby="total-sales-badge-btn" id="total-sales-badge">
						<?php $template_page->settings->print_settings( 'badge', 'sales_badge', false ); ?>
					</div>
					<div class="tab-pane fade" role="tabpanel" aria-labelledby="category-badge-btn" id="category-badge">
						<?php $template_page->settings->print_settings( 'badge', 'category_badge', false ); ?>
					</div>
				</div>
			</div>
		<?php
		$template_page->settings->nonce_field();
		$template_page->settings->save_field();
		?>
		</div>
		<div class="col-md-6">
			<div class="h-100 d-flex align-items-start justify-content-center">
				<div class="preview-img-wrapper text-center w-100" style="position:sticky; top:25%;">
					<div class="preview-img d-inline-block position-relative">
						<?php $badge_settings = $template_page->badge()->get_badge_settings(); ?>
						<!-- Preview badge -->
						<img class="border" src="<?php echo esc_url_raw( $plugin_info['url'] . 'assets/images/badge-preview.jpg' ); ?>" width="350" height="350">
						<!-- Icon Badge -->
						<?php $template_page->badge()->best_seller_badge_wrapper_start( 'loop', 'best_seller', false ); ?>
						<img src="<?php echo esc_url_raw( $plugin_info['url'] . 'assets/images/icons/best-seller-icon-1.png' ); ?>" class="preview-img-badge" style="<?php echo esc_attr( 'text' === $badge_settings['badge_type'] ? 'display:none;' : '' ); ?>">
						<?php $template_page->badge()->best_seller_badge_wrapper_end( 'loop', false ); ?>
						<!-- Text Badge -->
						<?php $template_page->badge()->best_seller_badge_wrapper_start( 'loop', 'best_seller', false ); ?>
						<?php $template_page->text_badge(); ?>
						<?php $template_page->badge()->best_seller_badge_wrapper_end( 'loop', false ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $template_page::loader_html(); ?>
	<?php $template_page->badge_styles(); ?>
	<?php $template_page->text_badge_settings_styles(); ?>
</div>
