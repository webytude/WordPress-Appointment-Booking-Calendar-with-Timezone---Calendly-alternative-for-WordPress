<?php
if(!class_exists( 'WP_List_Table' ) ) { 
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class appointments_List_Table extends WP_List_Table{	
	
	function column_cb($item) {
		return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['cb']
        );
    }

    function column_name($item) {
        $actions = array(
            'View' => sprintf('<a href="?page=report-incident&view=%s">%s</a>', $item['cb'], __('View', 'what')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['cb'], __('Delete', 'what')),
        );

       return sprintf('%s %s',
            $item['name'],
            $this->row_actions($actions)
        );
    }
    public function column_default( $item, $column_name ) {
		// $orderid = $item->id;
		switch( $column_name ) {
			case 'cb':
			case 'name':
			case 'date':
				return $item[ $column_name ];
			case 'app_email':
				return $item[ $column_name ];
			case 'app_comments':
				return $item[ $column_name ];
			case 'app_booked_time':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ) ;
		}
	}
	
	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
			'name' => 'Name',
			'date' => "Appointments's Time",
			'app_email' => 'Email',
			'app_booked_time' => 'Booked Date',
			'app_comments' => 'Comments',
		);		 
		return $columns;
	}
	
	function get_sortable_columns() {
        $sortable_columns = array(
            'date' => array('date', true),
            'name' => array('name', true)
        );
        return $sortable_columns;
    }
	
	public function prepare_items() {
		$this->process_bulk_action();

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
		$data = $this->table_data();
		
		if(!empty($_GET['orderby']))
			usort( $data, array( &$this, 'sort_data' ) );

		
		$perPage = 100;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
		$this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));
 
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
		$this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }
	
	function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete',
	  	);
	  	return $actions;
	} 
			
	function process_bulk_action() {
        global $wpdb;
        $table_appointments = $wpdb->prefix . "wt_appointments";

		if ('delete' === $this->current_action()) {
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array( $ids )) $ids = implode(',', $ids);
			
			if (!empty( $ids )) {
                $wpdb->query("DELETE FROM $table_appointments WHERE id IN($ids)");
			}
        }
    }
	
	public function get_hidden_columns() {
		return array();
	}
	
	private function table_data() {
		global $wpdb;		
		$data = array();
		$table_appointments = $wpdb->prefix . "wt_appointments";

		$date = current_time( 'mysql' );
		$appointments = '';
		$ap_type = ( isset( $_REQUEST['ap-type'] ) ) ? $_REQUEST['ap-type'] : '';
		if( !empty( $ap_type ) && $ap_type == 'past' ){
			$query = "SELECT * FROM {$table_appointments} AS wr_p WHERE wr_p.app_time <= '$date' order by app_id desc";
		}
		else{
			$query = "SELECT * FROM {$table_appointments} AS wr_p WHERE wr_p.app_time >= '$date' order by app_id desc";
		}
		
		$appointments = $wpdb->get_results( $query );
			
		if(!empty( $appointments )) {
			foreach( $appointments as $key => $value ){

            	$name = $value->app_name;
            	$date = $value->app_time;
            	$app_email = $value->app_email;
            	$app_comments = $value->app_comments;
            	$app_booked_time = $value->app_booked_time;
				
				$data[] = array(
					'cb' => $value->id,
					'name' => $name,					
					'date' => $date,					
					'app_email' 	=> $app_email,
					'app_comments' 	=> $app_comments,
					'app_booked_time' 	=> $app_booked_time,
				);
			}
		} 
		return $data;
	} 

	function get_views(){
		   $views = array();
		   $current = ( !empty($_REQUEST['ap-type']) ? $_REQUEST['ap-type'] : 'upcoming');

		   //Upcoming link
		   $upcoming_url = add_query_arg('ap-type','upcoming');
		   $class = ($current == 'upcoming' ? ' class="current"' :'');
		   $views['upcoming'] = "<a href='{$upcoming_url}' {$class} >Upcoming</a>";

		   //Past link
		   $past_url = add_query_arg('ap-type','past');
		   $class = ($current == 'past' ? ' class="current"' :'');
		   $views['past'] = "<a href='{$past_url}' {$class} >Past</a>";

		   return $views;
		}
}
?>