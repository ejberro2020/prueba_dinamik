<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$calendar_id = absint( ! empty( $_GET['calendar_id'] ) ? $_GET['calendar_id'] : 0 );

?>

<div class="wrap wpbs-wrap wpbs-wrap-legend-items">

	<!-- Page Heading -->
	<h1 class="wp-heading-inline"><?php echo __( 'Calendar Legend', 'wp-booking-system' ); ?><span class="wpbs-heading-tag"><?php printf( __( 'Calendar ID: %d', 'wp-booking-system' ), $calendar_id ); ?></span></h1>
	<a href="<?php echo add_query_arg( array( 'subpage' => 'add-legend-item', 'calendar_id' => $calendar_id ), $this->admin_url ); ?>" class="page-title-action"><?php echo __( 'Add New Legend Item', 'wp-booking-system' ); ?></a>

	<!-- Page Heading Actions -->
	<div class="wpbs-heading-actions">
		<a href="<?php echo add_query_arg( array( 'subpage' => 'edit-calendar' ) ); ?>" class="button-secondary"><?php echo __( 'Back to Calendar', 'wp-booking-system' ); ?></a>
	</div>
	
	<hr class="wp-header-end" />

	<!-- Calendars List Table -->
	<?php 
		$table = new WPBS_WP_List_Table_Legend_Items();
		$table->display();
	?>

</div>