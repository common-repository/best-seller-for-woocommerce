<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR;

/**
 * Best Seller Queries Class.
 */
class BestSellerQueries extends Base {

	/**
	 * Get Best Seller Ranked.
	 *
	 * @param integer $limit
	 * @param boolean $published_only
	 * @param array   $categories_ids
	 * @param int     $start_date
	 * @param int     $end_date
	 * @return array
	 */
	public static function get_ranked_best_sellers( $limit = 100 ) {
		global $wpdb;
		$query =
		"SELECT
		    p.ID as product_id,
			-- p.post_title as product_title,
			CAST(pm.meta_value AS UNSIGNED) as items_sold,
			SUM(ot.meta_value) as net_revenue,
			(
				SELECT
					COUNT(DISTINCT(pm2.meta_value))
				FROM
					{$wpdb->postmeta} pm2
				JOIN
					{$wpdb->posts} p2 ON p2.ID = pm2.post_id
				JOIN
					{$wpdb->term_relationships} tr2 ON p2.ID = tr2.object_id
				JOIN
					{$wpdb->term_taxonomy} tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id AND tt2.taxonomy = 'product_cat'
				JOIN
					{$wpdb->terms} t2 ON tt2.term_id = t2.term_id
				WHERE
					pm2.meta_key = 'total_sales'
				AND
					CAST(pm2.meta_value AS UNSIGNED) > CAST(pm.meta_value AS UNSIGNED)";

		$query .= " ) + 1 AS rank
		FROM
			{$wpdb->posts} p
		JOIN
			{$wpdb->postmeta} pm
		ON
			p.ID = pm.post_id AND pm.meta_key = 'total_sales'
		JOIN
			{$wpdb->prefix}woocommerce_order_items oi ON p.ID = (
				SELECT meta_value
				FROM {$wpdb->prefix}woocommerce_order_itemmeta
				WHERE order_item_id = oi.order_item_id
					AND meta_key = '_product_id'
				LIMIT 1
			)
		JOIN
			{$wpdb->prefix}woocommerce_order_itemmeta ot
		ON
			oi.order_item_id = ot.order_item_id AND ot.meta_key = '_line_subtotal'
		JOIN
			{$wpdb->posts} p3
		ON
			oi.order_id = p3.ID AND ( p3.post_status IN ( 'wc-processing','wc-completed' ) )";

		$query .=
		" WHERE
			1=1
		AND
			p.post_type = 'product'
		AND
			p.post_status = 'publish'";

		$query .=
		" GROUP BY
			product_id ";

		$query .=
			"HAVING
			rank <= %d";

		$query .=
		" ORDER BY
			items_sold DESC,
			net_revenue DESC";

		$result     = $wpdb->get_results( $wpdb->prepare( $query, $limit ), \ARRAY_A );
		$new_result = array();

		foreach ( $result as $row ) {
			$filter_keys                      = array(
				'items_sold'      => 1,
				'net_revenue'     => 1,
				'product_id'      => 1,
			);
			$new_result[ $row['product_id'] ] = array_diff_key( $row, $filter_keys );
		}
		return $new_result;
	}
}
