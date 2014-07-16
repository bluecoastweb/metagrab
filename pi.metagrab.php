<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
    'pi_name'        => 'Metagrab',
    'pi_version'     => '1.0.0',
    'pi_author'      => 'Steve Pedersen',
    'pi_author_url'  => 'http://www.bluecoastweb.com',
    'pi_description' => 'Give it up, NSM Better Meta!',
    'pi_usage'       => Metagrab::usage()
);

class Metagrab {
    public $return_data = '';
    private $debug = false;

    public function __construct() {
        $this->EE =& get_instance();
        $this->debug = $this->is_truthy($this->EE->TMPL->fetch_param('debug'));
        $entry_id = $this->EE->TMPL->fetch_param('entry_id');
        $default  = $this->EE->TMPL->fetch_param('default');
        $title = false;
        $sql = 'SELECT title FROM exp_nsm_better_meta WHERE entry_id=?';
        $query = $this->EE->db->query($sql, array($entry_id));
        if ($query->num_rows() == 1) {
            $title = $query->row('title');
         }
        if ($this->debug) {
            $this->EE->TMPL->log_item(__CLASS__." entry_id=$entry_id, title=$title");
        }
        $this->return_data = $title ? $title : $default;
    }

    private function is_truthy($value) {
        $truthy_values = array('on', 'true', 'yes', '1');
        return in_array(strtolower($value), $truthy_values);
    }

    public static function usage() {
        ob_start();
?>

{exp:channel:entries limit='1'}

    {exp:metagrab entry_id='{entry_id}' default='{title}'}

{/exp:channel:entries}

<?php
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
}

