<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
    'pi_name'        => 'Metagrab',
    'pi_version'     => '2.0',
    'pi_author'      => 'Steve Pedersen',
    'pi_author_url'  => 'http://www.bluecoastweb.com',
    'pi_description' => 'Give it up, NSM Better Meta!',
    'pi_usage'       => Metagrab::usage()
);

class Metagrab {
    public $return_data = '';
    private $ee;
    private $debug;

    public function __construct() {
        $this->ee = function_exists('ee') ? ee() : get_instance();

        $entry_id = $this->ee->TMPL->fetch_param('entry_id');
        if (! $entry_id) {
            $this->give_up('The "entry_id" parameter is required.');
        }

        $attribute = $this->ee->TMPL->fetch_param('attribute', 'title');
        if (! in_array($attribute, array('title', 'description', 'keywords'))) {
            $this->give_up('The "attribute" parameter must be one: title, description and keywords.');
        }

        $default = $this->ee->TMPL->fetch_param('default', '');

        $this->debug = $this->is_truthy($this->ee->TMPL->fetch_param('debug'));
        $this->log("From tag: entry_id=$entry_id, attribute=$attribute, default=$default");

        // derive attribute value from NSM Better Meta entry
        $sql = "SELECT $attribute FROM exp_nsm_better_meta WHERE entry_id = ? LIMIT 1";
        $query = $this->ee->db->query($sql, array($entry_id));
        $value = '';

        if ($query->num_rows() > 0) {
            $value = $query->row($attribute);
            $this->log("From NSM: entry_id=$entry_id, $attribute=$value");
        }

        if (empty($value)) {
            // fall back to NSM Better Meta default setting
            $value = $this->nsm_default($attribute);
            $this->log("From NSM default: $attribute=$value");
        }

        if (empty($value)) {
            // fall back to tag default
            $value = $default;
            $this->log("From tag default: $attribute=$value");
        }

        $this->return_data = $value;
    }

    private function nsm_default($attribute) {
        if (! class_exists('Nsm_better_meta_ext')) {
            include(PATH_THIRD. 'nsm_better_meta/ext.nsm_better_meta.php');
        }

        $nsm_extension = new Nsm_better_meta_ext;
        return $nsm_extension->settings['default_site_meta'][$attribute == 'title' ? 'site_title' : $attribute];
    }

    private function give_up($string) {
        $this->ee->output->fatal_error(__CLASS__.": $string");
    }

    private function is_truthy($value) {
        return in_array(strtolower($value), array('on', 'true', 'yes', '1'));
    }

    private function log($string) {
        if ($this->debug) {
            $this->ee->TMPL->log_item(__CLASS__.": $string");
        }
    }

    public static function usage() {
        ob_start();
?>

{exp:channel:entries limit='1'}

    {exp:metagrab entry_id='{entry_id}' attribute='title' default='{title}'}

    {exp:metagrab entry_id='{entry_id}' attribute='description' default='My Enchanting Description'}

    {exp:metagrab entry_id='{entry_id}' attribute='keywords' default='foo bar'}

{/exp:channel:entries}

<?php
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
}

