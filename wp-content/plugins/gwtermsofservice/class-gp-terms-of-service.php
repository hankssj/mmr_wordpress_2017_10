<?php 

class GP_Terms_Of_Service extends GWPerk {

    public $version = '1.3.7';
    protected $min_gravity_perks_version = '1.2.12';
    protected $min_gravity_forms_version = '1.9.18.2';

	private static $instance = null;

	public static function get_instance( $perk_file ) {
		if( null == self::$instance ) {
			self::$instance = new self( $perk_file );
		}
		return self::$instance;
	}

	public function __construct( $perk_file ) {
		parent::__construct( $perk_file );
	}

    public function init() {
        
        $this->add_tooltip($this->key('require_scroll'), __('<h6>Require Full Scroll</h6>Checking this option will disable the acceptance checkbox until the user has scrolled through the full Terms of Service.', 'gravityperks'));
        $this->add_tooltip($this->key('terms'), __('<h6>The Terms</h6>Specify the terms the user is agreeing to here.', 'gravityperks'));

	    require_once( 'includes/class-gf-field-terms-of-service.php' );
        
    }

    public function documentation() {
        return array(
            'type'  => 'url',
            'value' => 'http://gravitywiz.com/documentation/gp-terms-of-service/'
        );
    }
    
}

class GWTermsOfService extends GP_Terms_Of_Service { };

function gp_terms_of_service() {
    return GP_Terms_Of_Service::get_instance( null );
}